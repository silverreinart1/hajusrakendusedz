<template>
  <AppLayout>
    <div class="max-w-xl mx-auto fade-up">
      <Link href="/blog" class="text-[var(--muted)] text-sm hover:text-[var(--text)] no-underline mb-6 inline-block">← Tagasi</Link>
      <h1 class="font-['Playfair_Display'] text-3xl mb-8">Uus postitus</h1>

      <div class="card space-y-4">
        <div>
          <label class="label">Pealkiri *</label>
          <input v-model="form.title" class="input" placeholder="Postitus pealkiri..." />
          <span v-if="errors.title" class="text-[var(--danger)] text-xs mt-1 block">{{ errors.title }}</span>
        </div>
        <div>
          <label class="label">Sisu *</label>
          <textarea v-model="form.description" class="input" rows="10" placeholder="Kirjuta siia..."></textarea>
          <span v-if="errors.description" class="text-[var(--danger)] text-xs mt-1 block">{{ errors.description }}</span>
        </div>
        <div class="flex gap-3 pt-2">
          <button @click="submit" class="btn btn-gold">Avalda</button>
          <Link href="/blog" class="btn btn-ghost">Tühista</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
const errors = page.props.errors || {}

const form = reactive({ title: '', description: '' })

const submit = () => router.post('/blog', form)
</script>
