<template>
  <AppLayout>
    <div class="fade-up">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-['Playfair_Display'] mb-1">Blogi</h1>
          <p class="text-[var(--muted)] text-sm">{{ posts.length }} postitust</p>
        </div>
        <Link v-if="$page.props.auth.user" href="/blog/create" class="btn btn-gold">+ Uus postitus</Link>
      </div>

      <div v-if="posts.length === 0" class="text-center py-20 text-[var(--muted)]">
        Postitusi veel pole.
      </div>

      <div class="space-y-4">
        <div v-for="post in posts" :key="post.id" class="card hover:border-[var(--gold-dim)] transition-colors">
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
              <Link :href="`/blog/${post.id}`" class="font-['Playfair_Display'] text-xl hover:text-[var(--gold)] no-underline transition-colors">
                {{ post.title }}
              </Link>
              <p class="text-[var(--muted)] text-sm mt-2 line-clamp-2">{{ post.description }}</p>
              <div class="flex items-center gap-3 mt-3 text-xs text-[var(--muted)]">
                <span>{{ post.user.name }}</span>
                <span>·</span>
                <span>{{ new Date(post.created_at).toLocaleDateString('et') }}</span>
              </div>
            </div>
            <div v-if="canEdit(post)" class="flex gap-1 shrink-0">
              <Link :href="`/blog/${post.id}/edit`" class="btn btn-ghost text-xs">✏️</Link>
              <Link :href="`/blog/${post.id}`" method="delete" as="button" class="btn btn-danger text-xs"
                @click.prevent="deletePost(post.id)">🗑</Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ posts: Array })

const page = usePage()

const canEdit = (post) => {
  const user = page.props.auth.user
  return user && (user.is_admin || user.id === post.user_id)
}

const deletePost = (id) => {
  if (confirm('Kustuta postitus?')) router.delete(`/blog/${id}`)
}
</script>
