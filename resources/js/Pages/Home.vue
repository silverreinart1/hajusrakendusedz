<template>
  <AppLayout>
    <div class="py-20 text-center fade-up">
      <p class="text-[var(--muted)] text-sm uppercase tracking-widest mb-4">Silveri Hajusrakendused</p>
      <h1 class="text-5xl font-['Playfair_Display'] text-[var(--text)] mb-6">
        <br /><em class="text-[var(--gold)]"></em>
      </h1>
      <p class="text-[var(--muted)] max-w-lg mx-auto mb-12 leading-relaxed">
      </p>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-3xl mx-auto text-left">
        <Link v-for="item in features" :key="item.href" :href="item.href"
          class="card hover:border-[var(--gold-dim)] transition-colors group no-underline">
          <div class="text-2xl mb-3">{{ item.icon }}</div>
          <div class="text-xs text-[var(--muted)] uppercase tracking-wider mb-1">Ülesanne {{ item.num }}</div>
          <div class="font-['Playfair_Display'] text-lg text-[var(--text)] group-hover:text-[var(--gold)] transition-colors">{{ item.label }}</div>
          <p class="text-xs text-[var(--muted)] mt-2">{{ item.desc }}</p>
        </Link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

const features = [
  { num: 1, href: '/weather', icon: '🌤', label: 'Ilmateenus', desc: 'OpenWeatherMap API + cache + linnaotsing' },
  { num: 2, href: '/map',     icon: '🗺', label: 'Kaardirakendus', desc: 'Leaflet + markerite CRUD + koordinaadid' },
  { num: 3, href: '/blog',    icon: '📝', label: 'Blogi',    desc: 'Auth + postitused + kommentaaride haldus' },
  { num: 4, href: '/shop',    icon: '🛍', label: 'E-pood',   desc: 'Ostukorv + Stripe Checkout + tellimused' },
  { num: 5, href: '/books',   icon: '📚', label: 'Raamatud API', desc: 'JSON API + filter + sort + limit + cache' },
]
</script>
