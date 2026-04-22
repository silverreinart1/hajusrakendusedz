<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto fade-up">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-['Playfair_Display']">Ostukorv</h1>
        <Link href="/shop" class="text-[var(--muted)] text-sm hover:text-[var(--text)] no-underline">← Jätka ostlemist</Link>
      </div>

      <div v-if="cartItems.length === 0" class="text-center py-20 text-[var(--muted)]">
        <div class="text-5xl mb-4">🛒</div>
        <p>Ostukorv on tühi.</p>
        <Link href="/shop" class="btn btn-gold mt-6">Vaata tooteid</Link>
      </div>

      <div v-else>
        <div class="space-y-3 mb-6">
          <div v-for="item in cartItems" :key="item.product_id"
            class="card py-3 px-4 flex items-center gap-4">
            <img :src="item.image" :alt="item.name"
              class="w-16 h-16 object-cover rounded flex-shrink-0" />
            <div class="flex-1 min-w-0">
              <div class="font-medium truncate">{{ item.name }}</div>
              <div class="text-[var(--gold)] text-sm">{{ item.price.toFixed(2) }} €</div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <input :value="item.quantity" type="number" min="1" max="99"
                class="input w-16 text-center text-sm py-1 px-2"
                @change="updateQty(item.product_id, $event.target.value)" />
              <button @click="remove(item.product_id)"
                class="text-[var(--muted)] hover:text-[var(--danger)] transition-colors text-lg">✕</button>
            </div>
            <div class="text-right shrink-0 w-20">
              <div class="text-[var(--text)] font-medium">{{ (item.price * item.quantity).toFixed(2) }} €</div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="flex items-center justify-between mb-4">
            <span class="text-[var(--muted)]">Kokku</span>
            <span class="font-['Playfair_Display'] text-2xl text-[var(--gold)]">{{ total.toFixed(2) }} €</span>
          </div>
          <Link href="/checkout" class="btn btn-gold w-full justify-center text-base">
            Jätka maksmisele →
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ cart: Object, products: Array })

const cartItems = computed(() => Object.values(props.cart || {}))
const total = computed(() => cartItems.value.reduce((s, i) => s + i.price * i.quantity, 0))

const updateQty = (id, qty) => {
  router.put(`/cart/${id}`, { quantity: parseInt(qty) }, { preserveScroll: true })
}
const remove = (id) => {
  router.delete(`/cart/${id}`, { preserveScroll: true })
}
</script>
