<template>
  <AppLayout>
    <div class="max-w-sm mx-auto fade-up pt-10">
      <h1 class="font-['Playfair_Display'] text-3xl mb-2 text-center">Registreeru</h1>
      <p class="text-[var(--muted)] text-sm text-center mb-8">Loo uus konto</p>

      <div class="card space-y-4">
        <div>
          <label class="label">Nimi</label>
          <input v-model="form.name" class="input" placeholder="Sinu nimi" />
          <span v-if="$page.props.errors?.name" class="text-[var(--danger)] text-xs mt-1 block">{{ $page.props.errors.name }}</span>
        </div>
        <div>
          <label class="label">E-post</label>
          <input v-model="form.email" type="email" class="input" placeholder="sinu@email.ee" />
          <span v-if="$page.props.errors?.email" class="text-[var(--danger)] text-xs mt-1 block">{{ $page.props.errors.email }}</span>
        </div>
        <div>
          <label class="label">Parool</label>
          <input v-model="form.password" type="password" class="input" placeholder="Vähemalt 8 tähemärki" />
          <span v-if="$page.props.errors?.password" class="text-[var(--danger)] text-xs mt-1 block">{{ $page.props.errors.password }}</span>
        </div>
        <div>
          <label class="label">Korda parooli</label>
          <input v-model="form.password_confirmation" type="password" class="input" placeholder="••••••••" />
        </div>

        <button @click="submit" class="btn btn-gold w-full justify-center">Registreeru</button>

        <p class="text-center text-sm text-[var(--muted)]">
          On juba konto?
          <Link href="/login" class="text-[var(--gold)]">Logi sisse</Link>
        </p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = reactive({ name: '', email: '', password: '', password_confirmation: '' })
const submit = () => router.post('/register', form)
</script>
