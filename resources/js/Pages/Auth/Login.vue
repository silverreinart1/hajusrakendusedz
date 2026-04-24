<template>
  <AppLayout>
    <div class="max-w-sm mx-auto fade-up pt-10">
      <h1 class="font-['Playfair_Display'] text-3xl mb-2 text-center">Logi sisse</h1>
      <p class="text-[var(--muted)] text-sm text-center mb-8">Tere tulemast tagasi</p>

      <div class="card space-y-4">
        <div>
          <label class="label">E-post</label>
          <input v-model="form.email" type="email" class="input" placeholder="sinu@email.ee" @keydown.enter="submit" />
          <span v-if="form.errors.email" class="text-[var(--danger)] text-xs mt-1 block">{{ form.errors.email }}</span>
        </div>
        <div>
          <label class="label">Parool</label>
          <input v-model="form.password" type="password" class="input" placeholder="••••••••" @keydown.enter="submit" />
        </div>

        <button @click="submit" :disabled="form.processing" class="btn btn-gold w-full justify-center">
          {{ form.processing ? 'Palun oota...' : 'Logi sisse' }}
        </button>

        <p class="text-center text-sm text-[var(--muted)]">
          Pole kontot?
          <Link href="/register" class="text-[var(--gold)]">Registreeru</Link>
        </p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({
  email: '',
  password: '',
  remember: false
})

function submit() {
  form.post('/login', {
    onError: () => {
      form.reset('password')
    }
  })
}
</script>
