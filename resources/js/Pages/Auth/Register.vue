<template>
  <AppLayout>
    <div class="max-w-sm mx-auto fade-up pt-10">
      <h1 class="font-['Playfair_Display'] text-3xl mb-2 text-center">Registreeru</h1>
      <p class="text-[var(--muted)] text-sm text-center mb-8">Loo uus konto</p>

      <div class="card space-y-4">
        <div>
          <label class="label">Nimi</label>
          <input v-model="form.name" class="input" placeholder="Sinu nimi" @keydown.enter="submit" />
          <span v-if="form.errors.name" class="text-[var(--danger)] text-xs mt-1 block">{{ form.errors.name }}</span>
        </div>
        <div>
          <label class="label">E-post</label>
          <input v-model="form.email" type="email" class="input" placeholder="sinu@email.ee" @keydown.enter="submit" />
          <span v-if="form.errors.email" class="text-[var(--danger)] text-xs mt-1 block">{{ form.errors.email }}</span>
        </div>
        <div>
          <label class="label">Parool</label>
          <input v-model="form.password" type="password" class="input" placeholder="Vähemalt 8 tähemärki" @keydown.enter="submit" />
          <span v-if="form.errors.password" class="text-[var(--danger)] text-xs mt-1 block">{{ form.errors.password }}</span>
        </div>
        <div>
          <label class="label">Korda parooli</label>
          <input v-model="form.password_confirmation" type="password" class="input" placeholder="••••••••" @keydown.enter="submit" />
        </div>

        <div v-if="form.errors.general" class="text-[var(--danger)] text-sm text-center">
          {{ form.errors.general }}
        </div>

        <button @click="submit" :disabled="form.processing" class="btn btn-gold w-full justify-center">
          {{ form.processing ? 'Palun oota...' : 'Registreeru' }}
        </button>

        <p class="text-center text-sm text-[var(--muted)]">
          On juba konto?
          <Link href="/login" class="text-[var(--gold)]">Logi sisse</Link>
        </p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

function submit() {
  form.post('/register', {
    onError: () => {
      form.reset('password', 'password_confirmation')
    }
  })
}
</script>
