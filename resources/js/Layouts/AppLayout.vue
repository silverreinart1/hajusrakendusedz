<template>
  <div class="min-h-screen flex flex-col">
    <!-- Nav -->
    <nav class="border-b border-[var(--border)] sticky top-0 z-50 backdrop-blur-sm bg-[rgba(14,14,15,0.9)]">
      <div class="max-w-6xl mx-auto px-6 h-14 flex items-center justify-between">
        <Link href="/" class="font-['Playfair_Display'] text-[var(--gold)] text-xl tracking-wide no-underline">
          <span class="text-[var(--text)]">Silendused</span>
        </Link>

        <div class="flex items-center gap-1 text-sm">
          <Link v-for="item in navItems" :key="item.href"
            :href="item.href"
            class="px-3 py-1.5 rounded transition-colors text-[var(--muted)] hover:text-[var(--text)] no-underline"
            :class="{ 'text-[var(--gold)]!': isActive(item.href) }">
            {{ item.label }}
          </Link>

          <div class="w-px h-4 bg-[var(--border)] mx-2"></div>

          <!-- Cart badge -->
          <Link href="/cart" class="relative px-3 py-1.5 text-[var(--muted)] hover:text-[var(--text)] no-underline">
            🛒
            <span v-if="$page.props.cartCount > 0"
              class="absolute -top-0.5 -right-0.5 bg-[var(--gold)] text-[#0e0e0f] text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold">
              {{ $page.props.cartCount }}
            </span>
          </Link>

          <template v-if="$page.props.auth.user">
            <span class="text-[var(--muted)] text-xs">{{ $page.props.auth.user.name }}</span>
            <Link href="/logout" method="post" as="button" class="btn btn-ghost text-xs ml-1">Logi välja</Link>
          </template>
          <template v-else>
            <Link href="/login" class="btn btn-ghost text-xs">Logi sisse</Link>
            <Link href="/register" class="btn btn-gold text-xs ml-1">Registreeru</Link>
          </template>
        </div>
      </div>
    </nav>

    <!-- Flash -->
    <div v-if="$page.props.flash.success || $page.props.flash.error" class="max-w-6xl mx-auto w-full px-6 pt-4">
      <div v-if="$page.props.flash.success" class="p-3 rounded border border-[var(--success)] text-[var(--success)] text-sm bg-[rgba(39,174,96,0.08)]">
        {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash.error" class="p-3 rounded border border-[var(--danger)] text-[var(--danger)] text-sm bg-[rgba(192,57,43,0.08)]">
        {{ $page.props.flash.error }}
      </div>
    </div>

    <!-- Main -->
    <main class="flex-1 max-w-6xl mx-auto w-full px-6 py-10">
      <slot />
    </main>

  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

const navItems = [
  { href: '/weather', label: 'Ilm' },
  { href: '/map',     label: 'Kaart' },
  { href: '/blog',    label: 'Blogi' },
  { href: '/shop',    label: 'Pood' },
  { href: '/books',   label: 'Raamatud' },
]

const isActive = (href) => page.url.startsWith(href)
</script>
