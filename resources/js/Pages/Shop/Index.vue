<template>
  <AppLayout>
    <div class="fade-up">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-['Playfair_Display'] mb-1">Eesti E-pood</h1>
          <p class="text-[var(--muted)] text-sm">{{ products.length }} toodet · Stripe maksed</p>
        </div>
        <Link href="/cart" class="btn btn-outline relative">
          🛒 Ostukorv
          <span v-if="$page.props.cartCount > 0"
            class="ml-1 bg-[var(--gold)] text-[#0e0e0f] text-xs rounded-full px-1.5 py-0.5 font-bold">
            {{ $page.props.cartCount }}
          </span>
        </Link>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="product in products" :key="product.id"
          class="card p-0 overflow-hidden group hover:border-[var(--gold-dim)] transition-colors">
          <div class="aspect-[4/3] overflow-hidden bg-[var(--border)]">
            <img :src="product.image" :alt="product.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          </div>
          <div class="p-4">
            <div class="text-xs text-[var(--muted)] uppercase tracking-wider mb-1">{{ product.category }}</div>
            <h3 class="font-['Playfair_Display'] text-lg mb-1">{{ product.name }}</h3>
            <p class="text-[var(--muted)] text-sm mb-4 line-clamp-2">{{ product.description }}</p>

            <div class="flex items-center justify-between">
              <span class="text-[var(--gold)] text-xl font-['Playfair_Display']">{{ product.price.toFixed(2) }} €</span>
              <div class="flex items-center gap-2">
                <input v-model="quantities[product.id]" type="number" min="1" max="99"
                  class="input w-16 text-center text-sm py-1 px-2" />
                <button @click="addToCart(product)"
                  class="btn btn-gold text-sm py-1.5">Lisa</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ products: Array })

const quantities = reactive(
  Object.fromEntries(props.products.map(p => [p.id, 1]))
)

const addToCart = (product) => {
  router.post('/cart', {
    product_id: product.id,
    quantity: quantities[product.id],
  }, { preserveScroll: true })
}
</script>
