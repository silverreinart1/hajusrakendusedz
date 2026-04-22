<template>
  <AppLayout>
    <div class="max-w-sm mx-auto fade-up pt-10">
      <h1 class="font-['Playfair_Display'] text-3xl mb-2 text-center">Logi sisse</h1>
      <p class="text-[var(--muted)] text-sm text-center mb-8">Tere tulemast tagasi</p>

      <div class="card space-y-4">
        <div>
          <label class="label">E-post</label>
          <input v-model="form.email" type="email" class="input" placeholder="sinu@email.ee" />
        </div>
        <div>
          <label class="label">Parool</label>
          <input v-model="form.password" type="password" class="input" placeholder="••••••••" />
        </div>

        <div v-if="$page.props.errors?.email" class="text-[var(--danger)] text-sm">
          {{ $page.props.errors.email }}
        </div>

        <button @click="submit" class="btn btn-gold w-full justify-center">Logi sisse</button>

        <p class="text-center text-sm text-[var(--muted)]">
          Pole kontot?
          <Link href="/register" class="text-[var(--gold)]">Registreeru</Link>
        </p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = reactive({ email: '', password: '', remember: false })
const submit = () => router.post('/login', form)
</script>
