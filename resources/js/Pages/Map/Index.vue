<template>
  <AppLayout>
    <h1 class="text-3xl font-['Playfair_Display'] mb-2">Kaardirakendus</h1>
    <p class="text-[var(--muted)] text-sm mb-6">Klõpsa kaardil markeri lisamiseks · OpenStreetMap + Leaflet</p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Map -->
      <div class="lg:col-span-2">
        <div id="map" class="rounded-lg border border-[var(--border)] overflow-hidden" style="height: 520px;"></div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-4">
        <!-- Add/edit form -->
        <div class="card">
          <h3 class="font-['Playfair_Display'] text-lg mb-4">{{ editingMarker ? 'Muuda markerit' : 'Lisa marker' }}</h3>
          <div class="space-y-3">
            <div>
              <label class="label">Nimi *</label>
              <input v-model="form.name" class="input" placeholder="Koha nimi" />
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="label">Laiuskraad</label>
                <input v-model="form.latitude" class="input" readonly placeholder="Klõpsa kaardil" />
              </div>
              <div>
                <label class="label">Pikkuskraad</label>
                <input v-model="form.longitude" class="input" readonly placeholder="Klõpsa kaardil" />
              </div>
            </div>
            <div>
              <label class="label">Kirjeldus</label>
              <textarea v-model="form.description" class="input" rows="3" placeholder="Valikuline kirjeldus..."></textarea>
            </div>
            <div class="flex gap-2">
              <button @click="saveMarker" class="btn btn-gold flex-1" :disabled="!form.name || !form.latitude">
                {{ editingMarker ? 'Uuenda' : 'Lisa' }}
              </button>
              <button v-if="editingMarker" @click="cancelEdit" class="btn btn-ghost">Tühista</button>
            </div>
          </div>
        </div>

        <!-- Marker list -->
        <div class="card">
          <h3 class="font-['Playfair_Display'] text-lg mb-3">Markerid ({{ markers.length }})</h3>
          <div class="space-y-2 max-h-64 overflow-y-auto">
            <div v-if="markers.length === 0" class="text-[var(--muted)] text-sm">Ühtegi markerit veel pole.</div>
            <div v-for="m in markers" :key="m.id"
              class="flex items-center justify-between p-2 rounded border border-[var(--border)] hover:border-[var(--gold-dim)] transition-colors text-sm">
              <div class="flex-1 min-w-0">
                <button @click="flyTo(m)" class="font-medium text-[var(--gold)] hover:underline text-left truncate block w-full">
                  {{ m.name }}
                </button>
                <span class="text-[var(--muted)] text-xs">{{ m.latitude.toFixed(4) }}, {{ m.longitude.toFixed(4) }}</span>
              </div>
              <div class="flex gap-1 ml-2">
                <button @click="startEdit(m)" class="text-[var(--muted)] hover:text-[var(--text)] text-xs px-1">✏️</button>
                <button @click="deleteMarker(m.id)" class="text-[var(--muted)] hover:text-[var(--danger)] text-xs px-1">🗑</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

const props = defineProps({ markers: Array })

const markers = ref(props.markers || [])
const form = ref({ name: '', latitude: null, longitude: null, description: '' })
const editingMarker = ref(null)

let map = null
let leafletMarkers = {}
let clickMarker = null

onMounted(async () => {
  const L = (await import('leaflet')).default
  await import('leaflet/dist/leaflet.css')

  // Fix default icon
  delete L.Icon.Default.prototype._getIconUrl
  L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
  })

  map = L.map('map').setView([58.5953, 25.0136], 7)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map)

  // Add existing markers
  markers.value.forEach(m => addLeafletMarker(L, m))

  // Click to place
  map.on('click', (e) => {
    form.value.latitude  = e.latlng.lat.toFixed(7)
    form.value.longitude = e.latlng.lng.toFixed(7)
    if (clickMarker) map.removeLayer(clickMarker)
    clickMarker = L.marker([e.latlng.lat, e.latlng.lng], {
      icon: L.divIcon({ className: '', html: '<div style="width:12px;height:12px;background:var(--gold);border-radius:50%;border:2px solid #fff;"></div>' })
    }).addTo(map)
  })

  function addLeafletMarker(L, m) {
    const lm = L.marker([m.latitude, m.longitude])
      .addTo(map)
      .bindPopup(`<b>${m.name}</b>${m.description ? '<br>' + m.description : ''}`)
    leafletMarkers[m.id] = lm
  }
})

function flyTo(m) {
  map?.flyTo([m.latitude, m.longitude], 14)
  leafletMarkers[m.id]?.openPopup()
}

function startEdit(m) {
  editingMarker.value = m
  form.value = { name: m.name, latitude: m.latitude, longitude: m.longitude, description: m.description || '' }
}

function cancelEdit() {
  editingMarker.value = null
  form.value = { name: '', latitude: null, longitude: null, description: '' }
}

async function saveMarker() {
  if (editingMarker.value) {
    const { data } = await axios.put(`/map/markers/${editingMarker.value.id}`, {
      name: form.value.name, description: form.value.description
    })
    const idx = markers.value.findIndex(m => m.id === data.id)
    if (idx !== -1) markers.value[idx] = data
    cancelEdit()
  } else {
    const { data } = await axios.post('/map/markers', form.value)
    markers.value.push(data)
    // Add leaflet marker
    if (map) {
      const L = (await import('leaflet')).default
      const lm = L.marker([data.latitude, data.longitude]).addTo(map)
        .bindPopup(`<b>${data.name}</b>${data.description ? '<br>' + data.description : ''}`)
      leafletMarkers[data.id] = lm
    }
    form.value = { name: '', latitude: null, longitude: null, description: '' }
    if (clickMarker) { map?.removeLayer(clickMarker); clickMarker = null }
  }
}

async function deleteMarker(id) {
  if (!confirm('Kustuta marker?')) return
  await axios.delete(`/map/markers/${id}`)
  markers.value = markers.value.filter(m => m.id !== id)
  if (leafletMarkers[id]) { map?.removeLayer(leafletMarkers[id]); delete leafletMarkers[id] }
}
</script>
