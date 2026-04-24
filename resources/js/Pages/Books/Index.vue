<template>
  <AppLayout>
    <div class="fade-up">
      <div class="flex items-start justify-between mb-8 gap-4 flex-wrap">
        <div>
          <h1 class="text-3xl font-['Playfair_Display'] mb-1">Raamatud</h1>
          <p class="text-[var(--muted)] text-sm">JSON API · filter · sort · limit · cache</p>
        </div>
        <div class="flex gap-2 items-center">
          <a href="/api/books" target="_blank" class="btn btn-ghost text-xs">API →</a>
          <div class="flex rounded overflow-hidden border border-[var(--border)]">
            <button @click="setSource('own')"
              :class="['px-3 py-1 text-xs transition-colors', source === 'own' ? 'bg-[var(--gold)] text-black' : 'text-[var(--muted)] hover:text-white']">
              Minu
            </button>
            <button @click="setSource('friend')"
              :class="['px-3 py-1 text-xs transition-colors', source === 'friend' ? 'bg-[var(--gold)] text-black' : 'text-[var(--muted)] hover:text-white']">
              Sõbra
            </button>
          </div>
          <button v-if="source === 'own'" @click="showForm = !showForm" class="btn btn-gold text-sm">+ Lisa raamat</button>
        </div>
      </div>

      <!-- Friend API URL input -->
      <div v-if="source === 'friend'" class="card mb-6">
        <label class="label mb-1">Sõbra API URL</label>
        <div class="flex gap-2">
          <input v-model="friendApiInput" class="input text-sm flex-1 font-mono"
            placeholder="https://sobra-app.up.railway.app/api/books"
            @keydown.enter="fetchFriendApi" />
          <button @click="fetchFriendApi" class="btn btn-gold text-sm" :disabled="loading">
            {{ loading ? '...' : 'Laadi' }}
          </button>
        </div>
        <p v-if="friendError" class="text-[var(--danger)] text-xs mt-2">{{ friendError }}</p>
      </div>

      <!-- Add form -->
      <div v-if="showForm && source === 'own'" class="card mb-8">
        <h2 class="font-['Playfair_Display'] text-xl mb-4">Lisa uus raamat</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="label">Pealkiri *</label>
            <input v-model="newBook.title" class="input" placeholder="Raamatu pealkiri"
              @blur="autoFetchCover" />
          </div>
          <div>
            <label class="label">Autor *</label>
            <input v-model="newBook.author" class="input" placeholder="Autori nimi"
              @blur="autoFetchCover" />
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
            <input v-model="newBook.isbn" class="input" placeholder="978-..."
              @blur="autoFetchCover" />
          </div>
          <div>
            <label class="label">
              Kaanepilt
              <span v-if="coverLoading" class="text-[var(--muted)] font-normal"> · otsib...</span>
              <span v-else-if="newBook.image" class="text-green-500 font-normal"> · ✓ leitud</span>
            </label>
            <div class="flex gap-2">
              <input v-model="newBook.image" class="input text-sm flex-1" placeholder="https://... (täidetakse automaatselt)" />
              <button @click="autoFetchCover" type="button" class="btn btn-ghost text-xs px-2" title="Otsi kaanepilti">
                🔍
              </button>
            </div>
            <div v-if="newBook.image" class="mt-2">
              <img :src="newBook.image" class="h-20 rounded object-cover border border-[var(--border)]"
                @error="newBook.image = ''" />
            </div>
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
      <div v-if="source === 'own'" class="card mb-6">
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
        <span class="text-[var(--gold)]">GET</span> {{ activeApiUrl }}
      </div>

      <!-- Fix existing books button -->
      <div v-if="source === 'own' && books.some(b => !b.image)" class="mb-4 flex items-center gap-3">
        <p class="text-xs text-[var(--muted)]">{{ books.filter(b => !b.image).length }} raamatul puudub kaanepilt.</p>
        <button @click="fixAllCovers" class="btn btn-ghost text-xs" :disabled="fixingCovers">
          {{ fixingCovers ? 'Otsin...' : '🔍 Otsi kõigile kaanepildid' }}
        </button>
      </div>

      <!-- State messages -->
      <div v-if="loading" class="text-center py-12 text-[var(--muted)]">Laadin...</div>
      <div v-else-if="source === 'friend' && !friendLoaded" class="text-center py-12 text-[var(--muted)]">
        Sisesta sõbra API URL ja vajuta <span class="text-[var(--gold)]">Laadi</span>.
      </div>
      <div v-else-if="books.length === 0" class="text-center py-12 text-[var(--muted)]">Raamatuid ei leitud.</div>

      <!-- Books grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="book in books" :key="book.id"
          class="card hover:border-[var(--gold-dim)] transition-colors relative group">

          <div class="aspect-[3/2] rounded overflow-hidden mb-3 bg-[var(--border)] flex items-center justify-center">
            <img v-if="book.image" :src="book.image" :alt="book.title"
              class="w-full h-full object-cover"
              @error="e => e.target.parentElement.innerHTML = '<span class=\'text-4xl opacity-20\'>📖</span>'" />
            <span v-else class="text-4xl opacity-20">📖</span>
          </div>

          <div class="text-xs text-[var(--muted)] uppercase tracking-wider mb-1">{{ book.genre || '–' }}</div>
          <h3 class="font-['Playfair_Display'] text-lg leading-tight mb-1">{{ book.title }}</h3>
          <p class="text-sm text-[var(--gold)]">{{ book.author }}, {{ book.publication_year }}</p>
          <p class="text-xs text-[var(--muted)] mt-2 line-clamp-3">{{ book.description }}</p>
          <div v-if="book.isbn" class="text-xs text-[var(--muted)] mt-2 font-mono">ISBN: {{ book.isbn }}</div>

          <button v-if="source === 'own'" @click="deleteBook(book.id)"
            class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity btn btn-danger text-xs py-1 px-2">
            🗑
          </button>
        </div>
      </div>

      <p class="text-[var(--muted)] text-xs mt-6 text-right">
        {{ meta.count ?? books.length }} tulemust
        <span v-if="source === 'own'">· cache 5 min</span>
        <span v-else>· sõbra andmed</span>
      </p>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

const books        = ref([])
const meta         = ref({})
const loading      = ref(false)
const source       = ref('own')
const friendApiInput = ref('')
const friendLoaded   = ref(false)
const friendError    = ref(null)
const showForm     = ref(false)
const addLoading   = ref(false)
const addError     = ref(null)
const coverLoading = ref(false)
const fixingCovers = ref(false)

const filters = reactive({ search: '', genre: '', sort: 'id', order: 'asc', limit: 20 })
const newBook = reactive({
  title: '', author: '', publication_year: new Date().getFullYear(),
  genre: '', isbn: '', image: '', description: ''
})

const activeApiUrl = computed(() => {
  if (source.value === 'friend') return friendApiInput.value || '–'
  const params = new URLSearchParams()
  if (filters.search) params.set('search', filters.search)
  if (filters.genre)  params.set('genre', filters.genre)
  params.set('sort', filters.sort)
  params.set('order', filters.order)
  params.set('limit', filters.limit)
  return `/api/books?${params.toString()}`
})

// Fetch cover from Google Books API
async function fetchCover(title, author, isbn) {
  try {
    let query = ''
    if (isbn) {
      query = `isbn:${isbn.replace(/[^0-9X]/gi, '')}`
    } else if (title) {
      query = `intitle:${encodeURIComponent(title)}`
      if (author) query += `+inauthor:${encodeURIComponent(author)}`
    }
    if (!query) return null

    const res = await fetch(`https://www.googleapis.com/books/v1/volumes?q=${query}&maxResults=1`)
    const data = await res.json()
    const info = data.items?.[0]?.volumeInfo?.imageLinks
    if (!info) return null

    // Prefer large cover, fall back to thumbnail — force https and zoom for better quality
    const raw = info.extraLarge || info.large || info.medium || info.thumbnail || ''
    return raw.replace('http://', 'https://').replace('&edge=curl', '') + '&fife=w400'
  } catch {
    return null
  }
}

async function autoFetchCover() {
  if (newBook.image) return // don't overwrite manually entered URL
  if (!newBook.title && !newBook.isbn) return
  coverLoading.value = true
  const url = await fetchCover(newBook.title, newBook.author, newBook.isbn)
  if (url) newBook.image = url
  coverLoading.value = false
}

// Fix all existing books that have no image
async function fixAllCovers() {
  fixingCovers.value = true
  const missing = books.value.filter(b => !b.image)
  for (const book of missing) {
    const url = await fetchCover(book.title, book.author, book.isbn)
    if (url) {
      try {
        await axios.put(`/api/books/${book.id}`, { image: url })
        book.image = url // update reactively without full reload
      } catch (e) {
        console.error(`Failed to update book ${book.id}`, e)
      }
    }
  }
  fixingCovers.value = false
}

function setSource(s) {
  source.value = s
  books.value = []
  friendError.value = null
  if (s === 'own') {
    friendLoaded.value = false
    loadBooks()
  }
}

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

async function fetchFriendApi() {
  const url = friendApiInput.value.trim()
  if (!url) return
  loading.value = true
  friendError.value = null
  friendLoaded.value = false
  try {
    const { data } = await axios.get(url)
    books.value = Array.isArray(data) ? data : (data.data ?? [])
    meta.value  = { count: books.value.length }
    friendLoaded.value = true
  } catch {
    friendError.value = 'API ei vasta või CORS blokeerib päringu. Kontrolli URL-i.'
    books.value = []
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
    Object.assign(newBook, {
      title: '', author: '', publication_year: new Date().getFullYear(),
      genre: '', isbn: '', image: '', description: ''
    })
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

loadBooks()
</script>
