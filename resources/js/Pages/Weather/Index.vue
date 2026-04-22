<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto fade-up">
      <h1 class="text-3xl font-['Playfair_Display'] mb-2">Ilmateave</h1>
      <p class="text-[var(--muted)] text-sm mb-8">Reaalajas ilmaandmed OpenWeatherMap API-st · cache 30 min</p>

      <!-- Search -->
      <div class="flex gap-2 mb-8">
        <input v-model="city" @keyup.enter="search"
          class="input flex-1" placeholder="Sisesta linna nimi..." />
        <button @click="search" class="btn btn-gold" :disabled="loading">
          {{ loading ? '...' : 'Otsi' }}
        </button>
      </div>

      <!-- Error -->
      <div v-if="error" class="p-4 rounded border border-[var(--danger)] text-[var(--danger)] text-sm mb-6">
        {{ error }}
      </div>

      <!-- Weather card -->
      <div v-if="weather" class="card">
        <div class="flex items-start justify-between mb-6">
          <div>
            <div class="text-[var(--muted)] text-xs uppercase tracking-wider mb-1">{{ weather.country }}</div>
            <h2 class="text-4xl font-['Playfair_Display']">{{ weather.city }}</h2>
            <p class="text-[var(--muted)] mt-1">{{ weather.description }}</p>
          </div>
          <img :src="weather.icon_url" :alt="weather.description" class="w-20 h-20" />
        </div>

        <div class="text-7xl font-['Playfair_Display'] text-[var(--gold)] mb-1">
          {{ weather.temp }}°
        </div>
        <p class="text-[var(--muted)] text-sm mb-8">Tundub nagu {{ weather.feels_like }}°C</p>

        <hr class="divider" />

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
          <div v-for="stat in stats" :key="stat.label">
            <div class="text-[var(--muted)] text-xs uppercase tracking-wider mb-1">{{ stat.label }}</div>
            <div class="text-[var(--text)]">{{ stat.value }}</div>
          </div>
        </div>

        <p class="text-[var(--muted)] text-xs mt-6">Uuendatud: {{ weather.cached_at ? new Date(weather.cached_at).toLocaleString('et') : '–' }}</p>
      </div>

      <!-- Popular cities -->
      <div class="mt-8">
        <p class="text-[var(--muted)] text-xs uppercase tracking-wider mb-3">Populaarsed linnad</p>
        <div class="flex flex-wrap gap-2">
          <button v-for="c in popularCities" :key="c"
            @click="city = c; search()"
            class="btn btn-ghost text-xs">{{ c }}</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

const props = defineProps({ weather: Object, error: String, defaultCity: String })

const city    = ref(props.defaultCity || 'Tallinn')
const weather = ref(props.weather)
const error   = ref(props.error)
const loading = ref(false)

const popularCities = ['Tallinn', 'Tartu', 'Pärnu', 'Narva', 'Helsinki', 'Riia', 'Vilnius', 'Stockholm']

const stats = computed(() => weather.value ? [
  { label: 'Niiskus',    value: weather.value.humidity + '%' },
  { label: 'Tuul',       value: weather.value.wind_speed + ' km/h' },
  { label: 'Rõhk',       value: weather.value.pressure + ' hPa' },
  { label: 'Päikeseloojang', value: weather.value.sunset },
] : [])

async function search() {
  if (!city.value.trim()) return
  loading.value = true
  error.value = null
  try {
    const { data } = await axios.get('/weather/search', { params: { city: city.value } })
    weather.value = data.weather
  } catch (e) {
    error.value = e.response?.data?.error || 'Viga ilmaandmete laadimisel.'
    weather.value = null
  } finally {
    loading.value = false
  }
}
</script>
