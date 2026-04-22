<template>
  <AppLayout>
    <div class="max-w-xl mx-auto fade-up">
      <Link href="/cart" class="text-[var(--muted)] text-sm hover:text-[var(--text)] no-underline mb-6 inline-block">← Tagasi ostukorvi</Link>
      <h1 class="font-['Playfair_Display'] text-3xl mb-8">Maksmine</h1>

      <div class="grid grid-cols-1 gap-6">
        <!-- Order summary -->
        <div class="card">
          <h2 class="font-medium mb-4 text-sm uppercase tracking-wider text-[var(--muted)]">Tellimus</h2>
          <div class="space-y-2 mb-4">
            <div v-for="item in cart" :key="item.product_id"
              class="flex justify-between text-sm">
              <span class="text-[var(--muted)]">{{ item.name }} × {{ item.quantity }}</span>
              <span>{{ (item.price * item.quantity).toFixed(2) }} €</span>
            </div>
          </div>
          <div class="border-t border-[var(--border)] pt-3 flex justify-between font-medium">
            <span>Kokku</span>
            <span class="text-[var(--gold)] font-['Playfair_Display'] text-xl">{{ total.toFixed(2) }} €</span>
          </div>
        </div>

        <!-- Customer info -->
        <div class="card space-y-4">
          <h2 class="font-medium text-sm uppercase tracking-wider text-[var(--muted)]">Kontaktandmed</h2>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label">Eesnimi *</label>
              <input v-model="form.first_name" class="input" placeholder="Mari" />
            </div>
            <div>
              <label class="label">Perenimi *</label>
              <input v-model="form.last_name" class="input" placeholder="Maasikas" />
            </div>
          </div>
          <div>
            <label class="label">E-post *</label>
            <input v-model="form.email" type="email" class="input" placeholder="mari@email.ee" />
          </div>
          <div>
            <label class="label">Telefon</label>
            <input v-model="form.phone" class="input" placeholder="+372 5xxx xxxx" />
          </div>

          <div v-if="error" class="text-[var(--danger)] text-sm p-3 rounded border border-[var(--danger)] bg-[rgba(192,57,43,0.08)]">
            {{ error }}
          </div>

          <button @click="pay" class="btn btn-gold w-full justify-center text-base" :disabled="loading || !isValid">
            <span v-if="loading">Suunamine Stripe'i...</span>
            <span v-else>Maksa {{ total.toFixed(2) }} € →</span>
          </button>
          <p class="text-[var(--muted)] text-xs text-center">🔒 Turvaline makse Stripe kaudu</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

const props = defineProps({ cart: Array, total: Number, stripeKey: String })

const form = reactive({ first_name: '', last_name: '', email: '', phone: '' })
const loading = ref(false)
const error = ref(null)

const total = computed(() => props.cart.reduce((s, i) => s + i.price * i.quantity, 0))

const isValid = computed(() =>
  form.first_name && form.last_name && form.email
)

const pay = async () => {
  loading.value = true
  error.value = null
  try {
    const { data } = await axios.post('/checkout/session', form)
    window.location.href = data.url
  } catch (e) {
    error.value = e.response?.data?.error || 'Makse ebaõnnestus. Proovi uuesti.'
    loading.value = false
  }
}
</script>
