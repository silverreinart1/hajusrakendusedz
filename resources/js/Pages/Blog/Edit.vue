<template>
  <AppLayout>
    <div class="max-w-xl mx-auto fade-up">
      <Link href="/blog" class="text-[var(--muted)] text-sm hover:text-[var(--text)] no-underline mb-6 inline-block">← Tagasi</Link>
      <h1 class="font-['Playfair_Display'] text-3xl mb-8">Muuda postitust</h1>

      <div class="card space-y-4">
        <div>
          <label class="label">Pealkiri *</label>
          <input v-model="form.title" class="input" />
          <span v-if="$page.props.errors?.title" class="text-[var(--danger)] text-xs mt-1 block">{{ $page.props.errors.title }}</span>
        </div>
        <div>
          <label class="label">Sisu *</label>
          <textarea v-model="form.description" class="input" rows="10"></textarea>
          <span v-if="$page.props.errors?.description" class="text-[var(--danger)] text-xs mt-1 block">{{ $page.props.errors.description }}</span>
        </div>
        <div class="flex gap-3 pt-2">
          <button @click="submit" class="btn btn-gold">Salvesta</button>
          <Link :href="`/blog/${post.id}`" class="btn btn-ghost">Tühista</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ post: Object })

const form = reactive({
  title: props.post.title,
  description: props.post.description,
})

const submit = () => router.put(`/blog/${props.post.id}`, form)
</script>
