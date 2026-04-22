<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto fade-up">
      <Link href="/blog" class="text-[var(--muted)] text-sm hover:text-[var(--text)] no-underline mb-6 inline-block">← Tagasi blogisse</Link>

      <article class="card mb-8">
        <div class="text-xs text-[var(--muted)] uppercase tracking-wider mb-2">
          {{ post.user.name }} · {{ new Date(post.created_at).toLocaleDateString('et') }}
        </div>
        <h1 class="font-['Playfair_Display'] text-3xl mb-4">{{ post.title }}</h1>
        <div class="text-[var(--text)] leading-relaxed whitespace-pre-wrap">{{ post.description }}</div>

        <div v-if="canEdit" class="flex gap-2 mt-6 pt-6 border-t border-[var(--border)]">
          <Link :href="`/blog/${post.id}/edit`" class="btn btn-outline text-sm">Muuda</Link>
          <button @click="deletePost" class="btn btn-danger text-sm">Kustuta</button>
        </div>
      </article>

      <!-- Comments -->
      <section>
        <h2 class="font-['Playfair_Display'] text-xl mb-4">
          Kommentaarid ({{ post.comments.length }})
        </h2>

        <div class="space-y-3 mb-6">
          <div v-if="post.comments.length === 0" class="text-[var(--muted)] text-sm">
            Kommentaare veel pole. Ole esimene!
          </div>
          <div v-for="comment in post.comments" :key="comment.id"
            class="card py-3 px-4 flex items-start justify-between gap-3">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="text-[var(--gold)] text-sm font-medium">{{ comment.user.name }}</span>
                <span class="text-[var(--muted)] text-xs">{{ new Date(comment.created_at).toLocaleDateString('et') }}</span>
              </div>
              <p class="text-sm text-[var(--text)]">{{ comment.body }}</p>
            </div>
            <button v-if="canDeleteComment(comment)"
              @click="deleteComment(comment.id)"
              class="text-[var(--muted)] hover:text-[var(--danger)] text-xs shrink-0">✕</button>
          </div>
        </div>

        <!-- Add comment -->
        <div v-if="$page.props.auth.user" class="card">
          <h3 class="text-sm font-medium mb-3">Lisa kommentaar</h3>
          <textarea v-model="commentBody" class="input mb-3" rows="3" placeholder="Sinu kommentaar..."></textarea>
          <button @click="submitComment" class="btn btn-gold text-sm" :disabled="!commentBody.trim()">
            Postita
          </button>
        </div>
        <div v-else class="text-sm text-[var(--muted)]">
          <Link href="/login" class="text-[var(--gold)]">Logi sisse</Link> kommenteerimiseks.
        </div>
      </section>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ post: Object })
const page = usePage()
const commentBody = ref('')

const canEdit = computed(() => {
  const user = page.props.auth.user
  return user && (user.is_admin || user.id === props.post.user_id)
})

const canDeleteComment = (c) => {
  const user = page.props.auth.user
  return user && (user.is_admin || user.id === c.user_id)
}

const deletePost = () => {
  if (confirm('Kustuta postitus?')) router.delete(`/blog/${props.post.id}`)
}

const deleteComment = (id) => {
  if (confirm('Kustuta kommentaar?')) router.delete(`/comments/${id}`)
}

const submitComment = () => {
  if (!commentBody.value.trim()) return
  router.post(`/blog/${props.post.id}/comments`, { body: commentBody.value }, {
    onSuccess: () => { commentBody.value = '' }
  })
}
</script>
