<template>
  <div class="min-h-screen">
    <!-- Header -->
    <header class="sticky top-0 z-50 border-b border-zinc-200 bg-white/80 backdrop-blur">
      <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img :src="logoUrl" alt="Bex Soluciones" class="h-9 w-auto" />
          <div class="leading-tight">
            <div class="font-semibold tracking-tight">Bex Soluciones</div>
            <div class="text-xs text-zinc-500">Visits dashboard</div>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button
            @click="reload"
            class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-medium text-white
                   bg-gradient-to-r from-orange-500 to-amber-500 hover:opacity-95 active:opacity-90 shadow-sm"
          >
            Refresh
          </button>
        </div>
      </div>
    </header>

    <!-- Content -->
    <main class="mx-auto max-w-7xl px-4 py-5">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Map Card -->
        <section class="lg:col-span-2 rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
          <div class="px-4 py-3 border-b border-zinc-200 flex items-center justify-between">
            <div>
              <div class="font-semibold tracking-tight">Mapa de visitas</div>
              <div class="text-xs text-zinc-500">Marcadores por latitud y longitud</div>
            </div>

            <div class="text-sm text-zinc-500" v-if="visits.length">
              {{ visits.length }} visitas
            </div>
          </div>

          <div class="h-[70vh]">
            <div ref="mapEl" class="h-full w-full"></div>
          </div>
        </section>

        <!-- List Card -->
        <aside class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
          <div class="px-4 py-3 border-b border-zinc-200">
            <div class="font-semibold tracking-tight">Listado</div>
            <div class="mt-2">
              <input
                v-model="q"
                placeholder="Buscar por nombre o email…"
                class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm
                       focus:outline-none focus:ring-2 focus:ring-orange-400"
              />
            </div>
          </div>

          <div v-if="loading" class="p-4 text-sm text-zinc-500">Cargando…</div>
          <div v-else-if="error" class="p-4 text-sm text-red-600">{{ error }}</div>

          <div v-else class="divide-y divide-zinc-100 max-h-[70vh] overflow-auto">
            <button
              v-for="v in filtered"
              :key="v.id"
              @click="selectVisit(v)"
              class="w-full text-left p-4 hover:bg-zinc-50 transition"
              :class="selected?.id === v.id ? 'bg-orange-50/60' : ''"
            >
              <div class="flex items-start justify-between gap-2">
                <div>
                  <div class="font-medium">{{ v.name }}</div>
                  <div class="text-sm text-zinc-500 break-all">{{ v.email }}</div>
                </div>

                <span class="shrink-0 rounded-lg bg-zinc-900 text-white text-xs px-2 py-1">
                  #{{ v.id }}
                </span>
              </div>

              <div class="mt-2 text-xs text-zinc-500">
                {{ Number(v.latitude).toFixed(5) }}, {{ Number(v.longitude).toFixed(5) }}
              </div>
            </button>
          </div>
        </aside>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import L from 'leaflet'

const logoUrl = '/brand/LogoBexSoluciones.svg'

// Fix icon paths (Leaflet + Vite)
import icon2x from 'leaflet/dist/images/marker-icon-2x.png'
import icon from 'leaflet/dist/images/marker-icon.png'
import shadow from 'leaflet/dist/images/marker-shadow.png'

delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({ iconRetinaUrl: icon2x, iconUrl: icon, shadowUrl: shadow })

const mapEl = ref(null)
const map = ref(null)
const markers = ref(new Map())

const visits = ref([])
const selected = ref(null)
const q = ref('')
const loading = ref(false)
const error = ref(null)

const filtered = computed(() => {
  const s = q.value.trim().toLowerCase()
  if (!s) return visits.value
  return visits.value.filter(v =>
    String(v.name).toLowerCase().includes(s) || String(v.email).toLowerCase().includes(s)
  )
})

function initMap() {
  map.value = L.map(mapEl.value).setView([4.7110, -74.0721], 12) // Bogotá default
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map.value)
}

function escapeHtml(str) {
  return String(str).replace(/[&<>"']/g, s => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
  }[s]))
}

function renderMarkers(items) {
  markers.value.forEach(m => m.remove())
  markers.value.clear()

  items.forEach(v => {
    const lat = Number(v.latitude)
    const lng = Number(v.longitude)
    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return

    const m = L.marker([lat, lng]).addTo(map.value)
      .bindPopup(`
        <div style="min-width:200px">
          <div style="font-weight:700">${escapeHtml(v.name)}</div>
          <div style="color:#71717a;font-size:12px">${escapeHtml(v.email)}</div>
        </div>
      `)

    markers.value.set(v.id, m)
  })

  if (items.length) {
    const bounds = L.latLngBounds(items
      .map(v => [Number(v.latitude), Number(v.longitude)])
      .filter(([a,b]) => Number.isFinite(a) && Number.isFinite(b))
    )
    if (bounds.isValid()) map.value.fitBounds(bounds.pad(0.2))
  }
}

async function reload() {
  loading.value = true
  error.value = null
  try {
    const res = await fetch('/api/visits', { headers: { Accept: 'application/json' } })
    if (!res.ok) throw new Error(`API error: ${res.status}`)
    const json = await res.json()
    visits.value = json.data ?? [] // paginate()
    renderMarkers(visits.value)
  } catch (e) {
    error.value = e?.message ?? 'Error'
  } finally {
    loading.value = false
  }
}

function selectVisit(v) {
  selected.value = v
  const m = markers.value.get(v.id)
  if (m) {
    map.value.setView(m.getLatLng(), Math.max(map.value.getZoom(), 14), { animate: true })
    m.openPopup()
  }
}

onMounted(async () => {
  initMap()
  await reload()
})
</script>