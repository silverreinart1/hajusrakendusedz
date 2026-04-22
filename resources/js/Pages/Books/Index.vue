<template>
  <AppLayout>
    <div class="fade-up">
      <div class="flex items-start justify-between mb-8 gap-4 flex-wrap">
        <div>
          <h1 class="text-3xl font-['Playfair_Display'] mb-1">Raamatud</h1>
          <p class="text-[var(--muted)] text-sm">JSON API · filter · sort · limit · cache</p>
        </div>
        <div class="flex gap-2">
          <a href="/api/books" target="_blank" class="btn btn-ghost text-xs">API →</a>
          <button @click="showForm = !showForm" class="btn btn-gold text-sm">+ Lisa raamat</button>
        </div>
      </div>

      <!-- Add form -->
      <div v-if="showForm" class="card mb-8">
        <h2 class="font-['Playfair_Display'] text-xl mb-4">Lisa uus raamat</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="label">Pealkiri *</label>
            <input v-model="newBook.title" class="input" placeholder="Raamatu pealkiri" />
          </div>
          <div>
            <label class="label">Autor *</label>
            <input v-model="newBook.author" class="input" placeholder="Autori nimi" />
          </div>
          <div>
            <label class="label">Ilmumisaasta *</label>
            <input v-model="newBook.publication_year" type="number" class="input" placeholder="2024" />
          </div>
          <div>
            <label class="label">Žanr</label>
            <input v-model="newBook.genre" class="input" placeholder="nt Romaan, Ulme..." />
          </div>
          <div>
            <label class="label">ISBN</label>
            <input v-model="newBook.isbn" class="input" placeholder="978-..." />
          </div>
          <div>
            <label class="label">Kaanepilt (URL)</label>
            <input v-model="newBook.image" class="input" placeholder="https://..." />
          </div>
          <div class="sm:col-span-2">
            <label class="label">Kirjeldus *</label>
            <textarea v-model="newBook.description" class="input" rows="3" placeholder="Lühikirjeldus..."></textarea>
          </div>
        </div>
        <div class="flex gap-2 mt-4">
          <button @click="addBook" class="btn btn-gold" :disabled="addLoading">
            {{ addLoading ? 'Lisamine...' : 'Lisa raamat' }}
          </button>
          <button @click="showForm = false" class="btn btn-ghost">Tühista</button>
        </div>
        <p v-if="addError" class="text-[var(--danger)] text-sm mt-2">{{ addError }}</p>
      </div>

      <!-- Filters -->
      <div class="card mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div>
            <label class="label">Otsing</label>
            <input v-model="filters.search" class="input text-sm" placeholder="Pealkiri / autor..." @input="debouncedLoad" />
          </div>
          <div>
            <label class="label">Žanr</label>
            <input v-model="filters.genre" class="input text-sm" placeholder="Kõik žanrid" @input="debouncedLoad" />
          </div>
          <div>
            <label class="label">Sorteeri</label>
            <select v-model="filters.sort" class="input text-sm" @change="loadBooks">
              <option value="id">Lisamise järjekord</option>
              <option value="title">Pealkiri</option>
              <option value="author">Autor</option>
              <option value="publication_year">Aasta</option>
            </select>
          </div>
          <div>
            <label class="label">Korda</label>
            <div class="flex gap-2">
              <select v-model="filters.order" class="input text-sm flex-1" @change="loadBooks">
                <option value="asc">Kasvav</option>
                <option value="desc">Kahanev</option>
              </select>
              <input v-model.number="filters.limit" type="number" min="1" max="100"
                class="input text-sm w-16" @change="loadBooks" />
            </div>
          </div>
        </div>
      </div>

      <!-- API URL preview -->
      <div class="mb-4 p-3 rounded border border-[var(--border)] bg-[var(--surface)] font-mono text-xs text-[var(--muted)] overflow-x-auto">
        <span class="text-[var(--gold)]">GET</span> {{ apiUrl }}
      </div>

      <!-- Books grid -->
      <div v-if="loading" class="text-center py-12 text-[var(--muted)]">Laadin...</div>
      <div v-else-if="books.length === 0" class="text-center py-12 text-[var(--muted)]">Raamatuid ei leitud.</div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="book in books" :key="book.id"
          class="card hover:border-[var(--gold-dim)] transition-colors relative group">
          <div v-if="book.image" class="aspect-[3/2] rounded overflow-hidden mb-3 bg-[var(--border)]">
            <img :src="book.image" :alt="book.title" class="w-full h-full object-cover" />
          </div>
          <div class="text-xs text-[var(--muted)] uppercase tracking-wider mb-1">{{ book.genre || '–' }}</div>
          <h3 class="font-['Playfair_Display'] text-lg leading-tight mb-1">{{ book.title }}</h3>
          <p class="text-sm text-[var(--gold)]">{{ book.author }}, {{ book.publication_year }}</p>
          <p class="text-xs text-[var(--muted)] mt-2 line-clamp-3">{{ book.description }}</p>
          <div v-if="book.isbn" class="text-xs text-[var(--muted)] mt-2 font-mono">ISBN: {{ book.isbn }}</div>

          <button @click="deleteBook(book.id)"
            class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity btn btn-danger text-xs py-1 px-2">
            🗑
          </button>
        </div>
      </div>

      <p class="text-[var(--muted)] text-xs mt-6 text-right">{{ meta.count }} tulemust · cache 5 min</p>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

const books    = ref([])
const meta     = ref({})
const loading  = ref(false)
const showForm = ref(false)
const addLoading = ref(false)
const addError   = ref(null)

const filters = reactive({ search: '', genre: '', sort: 'id', order: 'asc', limit: 20 })

const newBook = reactive({
  title: '', author: '', publication_year: new Date().getFullYear(),
  genre: '', isbn: '', image: '', description: ''
})

const apiUrl = computed(() => {
  const params = new URLSearchParams()
  if (filters.search) params.set('search', filters.search)
  if (filters.genre)  params.set('genre', filters.genre)
  params.set('sort', filters.sort)
  params.set('order', filters.order)
  params.set('limit', filters.limit)
  return `/api/books?${params.toString()}`
})

let debounceTimer = null
const debouncedLoad = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(loadBooks, 400)
}

async function loadBooks() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/books', { params: filters })
    books.value = data.data
    meta.value  = data.meta
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function addBook() {
  addLoading.value = true
  addError.value = null
  try {
    await axios.post('/api/books', newBook)
    showForm.value = false
    Object.assign(newBook, { title: '', author: '', publication_year: new Date().getFullYear(), genre: '', isbn: '', image: '', description: '' })
    await loadBooks()
  } catch (e) {
    addError.value = e.response?.data?.message || 'Viga lisamisel.'
  } finally {
    addLoading.value = false
  }
}

async function deleteBook(id) {
  if (!confirm('Kustuta raamat?')) return
  await axios.delete(`/api/books/${id}`)
  await loadBooks()
}

onMounted(loadBooks)
</script>
