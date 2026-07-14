<script setup>
import { ref, onMounted, nextTick } from 'vue'
import L from 'leaflet'
import api from '../services/api'

const emit = defineEmits(['navigate'])

const form = ref({
  name: '',
  description: '',
  start_at: '',
  event_type: 'Outdoor',
  city: 'Belo Horizonte',
  state: 'MG',
  country: 'BRA',
  latitude: '-19.919100',
  longitude: '-43.937800',
  search_address: ''
})

const isLoading = ref(false)
const isSearching = ref(false)
const addressError = ref('')

let map = null
let marker = null

onMounted(async () => {
  await nextTick()

  const container = document.getElementById('mapContainer')
  if (!container) return

  delete L.Icon.Default.prototype._getIconUrl
  L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
  })

  const initialCoords = [parseFloat(form.value.latitude), parseFloat(form.value.longitude)]

  try {
    map = L.map('mapContainer', {
      zoomControl: true,
      attributionControl: false
    }).setView(initialCoords, 13)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map)

    marker = L.marker(initialCoords, { draggable: true }).addTo(map)

    marker.on('dragend', async () => {
      const position = marker.getLatLng()
      updateLocationState(position.lat, position.lng)
      await reverseGeocode(position.lat, position.lng)
    })
  } catch (error) {
    console.error("Erro ao inicializar o mapa:", error)
  }
})

function updateLocationState(lat, lng) {
  form.value.latitude = Number(lat).toFixed(6)
  form.value.longitude = Number(lng).toFixed(6)
}

function parseAddressDetails(addressObj) {
  if (!addressObj) return

  form.value.city = addressObj.city || addressObj.town || addressObj.village || addressObj.suburb || ''
  
  if (addressObj['ISO3166-2-lvl4']) {
    form.value.state = addressObj['ISO3166-2-lvl4'].split('-')[1].toUpperCase()
  } else {
    form.value.state = addressObj.state_code ? addressObj.state_code.toUpperCase() : (addressObj.state || '').substring(0, 2).toUpperCase()
  }
  
  if (addressObj.country_code) {
    form.value.country = addressObj.country_code.toUpperCase() === 'BR' ? 'BRA' : addressObj.country_code.toUpperCase()
  }
}

async function searchAddress() {
  if (!form.value.search_address.trim()) return

  isSearching.value = true
  addressError.value = ''

  try {
    const url = `https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&q=${encodeURIComponent(form.value.search_address)}&limit=1`
    const response = await fetch(url, {
      headers: { 'User-Agent': 'EventChildApp/1.0' }
    })
    const data = await response.json()

    if (data && data.length > 0) {
      const result = data[0]
      const lat = parseFloat(result.lat)
      const lon = parseFloat(result.lon)

      if (map && marker) {
        map.setView([lat, lon], 16)
        marker.setLatLng([lat, lon])
        updateLocationState(lat, lon)
        parseAddressDetails(result.address)
        addressError.value = ''
      }
    } else {
      addressError.value = 'Endereço não encontrado. Tente refinar a busca.'
    }
  } catch (error) {
    console.error(error)
    addressError.value = 'Erro ao buscar o endereço.'
  } finally {
    isSearching.value = false
  }
}

async function reverseGeocode(lat, lng) {
  try {
    const url = `https://nominatim.openstreetmap.org/reverse?format=json&addressdetails=1&lat=${lat}&lon=${lng}`
    const response = await fetch(url, {
      headers: { 'User-Agent': 'EventChildApp/1.0' }
    })
    const result = await response.json()
    if (result && result.address) {
      parseAddressDetails(result.address)
      if (result.display_name) {
        form.value.search_address = result.display_name
      }
    }
  } catch (error) {
    console.error("Erro na busca reversa:", error)
  }
}

function useCurrentLocation() {
  if (!navigator.geolocation) {
    addressError.value = 'Geolocalização não suportada pelo seu navegador.'
    return
  }

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      const lat = position.coords.latitude
      const lon = position.coords.longitude

      if (map && marker) {
        map.setView([lat, lon], 16)
        marker.setLatLng([lat, lon])
        updateLocationState(lat, lon)
        addressError.value = ''
        await reverseGeocode(lat, lon)
      }
    },
    (error) => {
      addressError.value = 'Permissão de localização negada ou indisponível.'
    }
  )
}

async function handleSubmit() {
  isLoading.value = true
  addressError.value = ''

  try {
    const response = await api.post('/events', {
      ...form.value,
      event_type: form.value.event_type.toLowerCase()
    })

    emit('navigate', 'dash')
  } catch (error) {
    console.error(error)
    
    if (error.response?.data?.errors) {
      const messages = Object.values(error.response.data.errors).flat()
      addressError.value = messages.join(' | ')
    } else {
      addressError.value = error.response?.data?.message || 'Falha ao se comunicar com o servidor.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="w-full max-w-[840px] mx-auto font-roboto antialiased pb-12 px-4">
    
    <div @click="emit('navigate', 'dash')" class="inline-flex items-center gap-2 text-xs text-slate-100 hover:text-slate-200 transition-colors cursor-pointer mb-6 select-none font-light">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
      </svg>
      Voltar para o painel
    </div>

    <header class="mb-8">
      <h2 class="font-roboto font-light text-3xl text-white/90 tracking-wide">Cadastrar evento</h2>
      <p class="text-slate-100 text-sm mt-1.5 font-light leading-relaxed">
        Preencha os dados abaixo. Assim que o evento for salvo, buscamos a previsão do tempo e calculamos o risco automaticamente.
      </p>
    </header>

    <!-- Barra de Progresso Tricolor -->
    <div class="flex gap-1.5 mb-8">
      <div class="h-1 flex-1 bg-orange-500 rounded-full"></div>
      <div class="h-1 flex-1 bg-purple-600 rounded-full"></div>
      <div class="h-1 flex-1 bg-slate-600 rounded-full"></div>
    </div>

    <!-- Formulário Principal -->
    <form @submit.prevent="handleSubmit" class="flex flex-col gap-6">
      
      <!-- Bloco 1: Identificação -->
      <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
        <div class="flex items-start gap-3 mb-5">
          <span class="w-6 h-6 rounded-lg bg-purple-50 text-purple-600 text-xs flex items-center justify-center font-mono font-medium">1</span> 
          <div>
            <h3 class="font-roboto font-medium text-base text-slate-800 leading-none">Identificação</h3>
            <p class="text-slate-600 text-xs font-light mt-1">Nome e descrição usados para identificar o evento no painel.</p>
          </div>
        </div>

        <div class="flex flex-col gap-5">
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-medium text-slate-700">Nome do evento <span class="text-red-500">*</span></label>
            <input v-model="form.name" type="text" placeholder="Ex.: Festival de Música Urbana" required class="w-full bg-[#f4f4f2] border border-transparent rounded-xl px-4 py-3 text-sm outline-none focus:border-purple-500 focus:bg-white transition-all font-light text-slate-800" />
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-medium text-slate-700">Descrição</label>
            <textarea v-model="form.description" rows="4" placeholder="Detalhes sobre o evento, estrutura, público esperado…" class="w-full bg-[#f4f4f2] border border-transparent rounded-xl px-4 py-3 text-sm outline-none focus:border-purple-500 focus:bg-white transition-all font-light text-slate-800 resize-none"></textarea>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
        <div class="flex items-start gap-3 mb-5">
          <span class="w-6 h-6 rounded-lg bg-purple-50 text-purple-600 text-xs flex items-center justify-center font-mono font-medium">2</span> 
          <div>
            <h3 class="font-roboto font-medium text-base text-slate-800 leading-none">Data e ambiente</h3>
            <p class="text-slate-600 text-xs font-light mt-1">O tipo de ambiente influences diretamente o cálculo de risco.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-medium text-slate-700">Data e horário de início <span class="text-red-500">*</span></label>
            <input v-model="form.start_at" type="datetime-local" required class="w-full bg-[#f4f4f2] border border-transparent rounded-xl px-4 py-3 text-sm outline-none focus:border-purple-500 focus:bg-white transition-all font-mono font-light text-slate-800" />
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-medium text-slate-700">Tipo de ambiente <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 gap-2 bg-[#f4f4f2] p-1 rounded-xl">
              <button type="button" @click="form.event_type = 'Indoor'" :class="['py-2.5 rounded-lg text-xs font-normal transition-all flex items-center justify-center gap-2', form.event_type === 'Indoor' ? 'bg-white text-purple-600 border border-slate-200/60 shadow-sm font-medium' : 'text-slate-500 hover:text-slate-800']">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 21V9L12 3L20 9V21" stroke-linejoin="round"></path><path d="M9 21V14H15V21"></path></svg>
                Indoor
              </button>
              <button type="button" @click="form.event_type = 'Outdoor'" :class="['py-2.5 rounded-lg text-xs font-normal transition-all flex items-center justify-center gap-2', form.event_type === 'Outdoor' ? 'bg-white text-purple-600 border border-slate-200/60 shadow-sm font-medium' : 'text-slate-500 hover:text-slate-800']">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="5"></circle><path d="M12 2V4M12 20V22M22 12H20M4 12H2" stroke-linecap="round"></path></svg>
                Outdoor
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Bloco 3: Localização com Botões Embutidos -->
      <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
        <div class="flex items-start gap-3 mb-5">
          <span class="w-6 h-6 rounded-lg bg-purple-50 text-purple-600 text-xs flex items-center justify-center font-mono font-medium">3</span> 
          <div>
            <h3 class="font-roboto font-medium text-base text-slate-800 leading-none">Localização</h3>
            <p class="text-slate-600 text-xs font-light mt-1">Usada pelo WeatherService para buscar a previsão do local exato do evento.</p>
          </div>
        </div>

        <div class="flex flex-col gap-1.5 mb-5">
          <label class="text-xs font-medium text-slate-700">Buscar endereço</label>
          <div class="flex gap-2">
            <input 
              v-model="form.search_address" 
              type="text" 
              @keyup.enter="searchAddress"
              placeholder="Digite um endereço e aperte Enter ou Buscar…" 
              class="w-full bg-[#f4f4f2] border border-transparent rounded-xl px-4 py-3 text-sm outline-none focus:border-purple-500 focus:bg-white transition-all font-light text-slate-800" 
            />
            <button 
              type="button" 
              @click="searchAddress"
              :disabled="isSearching"
              class="px-5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-medium transition-all shadow-sm disabled:opacity-50"
            >
              {{ isSearching ? 'Buscando...' : 'Buscar' }}
            </button>
          </div>
          <p v-if="addressError" class="text-red-500 text-[11px] mt-1 font-light">{{ addressError }}</p>
        </div>

        <div class="bg-[#f4f4f2]/70 border border-dashed border-slate-300 rounded-xl p-4 mb-5 flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="text-purple-600 bg-purple-50 p-2 rounded-lg">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 21C12 21 5 14.5 5 9.5C5 5.9 8.1 3 12 3C15.9 3 19 5.9 19 9.5C19 14.5 12 21 12 21Z"></path><circle cx="12" cy="9.5" r="2.3"></circle></svg>
            </div>
            <div>
              <div class="text-xs font-medium text-slate-800">Localização identificada</div>
              <div class="text-[11px] font-mono text-slate-500 mt-0.5">
                <span v-if="form.city">{{ form.city }} / {{ form.state }} — {{ form.country }}</span>
                <span v-else class="text-slate-400 font-sans italic">Nenhum endereço selecionado</span>
              </div>
            </div>
          </div>
          <button 
            type="button" 
            @click="useCurrentLocation"
            class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs px-4 py-2 rounded-xl transition-all flex items-center gap-2 font-medium shadow-sm"
          >
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8V4M12 20V16M4 12H8M16 12H20" stroke-linecap="round"></path><circle cx="12" cy="12" r="3.2"></circle></svg>
            Usar minha localização
          </button>
        </div>

        <!-- Mapa Real Operacional -->
        <div id="mapContainer" class="w-full h-48 rounded-xl mb-5 border border-slate-200 z-10"></div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-medium text-slate-600">Latitude</label>
            <input :value="form.latitude" type="text" readonly class="w-full bg-[#f4f4f2] border border-transparent rounded-xl px-4 py-3 text-sm font-mono text-slate-600 cursor-not-allowed" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-medium text-slate-600">Longitude</label>
            <input :value="form.longitude" type="text" readonly class="w-full bg-[#f4f4f2] border border-transparent rounded-xl px-4 py-3 text-sm font-mono text-slate-600 cursor-not-allowed" />
          </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 border-t border-slate-100 pt-5 mt-2">
          <p class="text-[11px] text-slate-400 font-light leading-tight max-w-sm text-center sm:text-left">
            A avaliação de risco (<span class="font-mono text-[10px] text-purple-600">weather_assessment</span>) roda em background ao salvar.
          </p>
          <div class="flex justify-end gap-3 w-full sm:w-auto">
            <button type="button" @click="emit('navigate', 'dash')" class="bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs px-6 py-3 rounded-xl transition-all font-normal">
              Cancelar
            </button>
            
            <button type="submit" :disabled="isLoading" class="bg-gradient-to-r from-purple-600 to-orange-500 text-white font-normal text-xs px-7 py-3 rounded-xl shadow-md hover:opacity-95 transition-all flex items-center justify-center gap-2 select-none flex-1 sm:flex-none">
              <span v-if="isLoading" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <span>Salvar e calcular risco</span>
              <svg v-if="!isLoading" width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </button>
          </div>
        </div>

      </div>
    </form>
  </div>
</template>

<style scoped>
#mapContainer {
  outline: none;
}
</style>