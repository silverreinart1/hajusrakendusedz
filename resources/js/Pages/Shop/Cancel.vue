<template>
  <AppLayout>
    <div class="max-w-md mx-auto text-center py-20 fade-up">
      <div class="text-6xl mb-6">❌</div>
      <h1 class="font-['Playfair_Display'] text-3xl mb-3 text-[var(--danger)]">Makse tühistatud</h1>
      <p class="text-[var(--muted)] mb-8">Tooted on endiselt ostukorvis. Proovi uuesti.</p>
      <div class="flex gap-3 justify-center">
        <Link href="/cart" class="btn btn-gold">Tagasi ostukorvi</Link>
        <Link href="/shop" class="btn btn-ghost">Vaata tooteid</Link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
</script>
