<template>
  <AppLayout>
    <div class="max-w-md mx-auto text-center py-20 fade-up">
      <div class="text-6xl mb-6">✅</div>
      <h1 class="font-['Playfair_Display'] text-3xl mb-3 text-[var(--success)]">Makse õnnestus!</h1>
      <p class="text-[var(--muted)] mb-2">Suur tänu ostu eest.</p>
      <p v-if="order" class="text-sm text-[var(--muted)] mb-8">
        Tellimus #{{ order.id }} · {{ order.email }}
      </p>
      <Link href="/shop" class="btn btn-gold">Tagasi poodi</Link>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
defineProps({ order: Object })
</script>
