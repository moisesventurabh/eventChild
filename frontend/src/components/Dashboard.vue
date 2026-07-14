<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../services/api'

const emit = defineEmits(['navigate', 'view-event'])

const events = ref([])
const isLoading = ref(true)
const errorMessage = ref('')
const currentFilter = ref('all')

async function fetchEvents() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await api.get('/events')
    events.value = response.data.data
  } catch (error) {
    console.error('Erro ao carregar eventos:', error)
    errorMessage.value = 'Não foi possível carregar a lista de eventos.'
  } finally {
    isLoading.value = false
  }
}

function getWeatherAssessment(event) {
  return event.weather_assessment || null
}

const filteredEvents = computed(() => {
  if (currentFilter.value === 'all') return events.value
  return events.value.filter(e => {
    const risk = getWeatherAssessment(e)?.risk_level?.toLowerCase() || ''
    return risk === currentFilter.value
  })
})

function countByRisk(level) {
  return events.value.filter(e => {
    const risk = getWeatherAssessment(e)?.risk_level?.toLowerCase() || ''
    return risk === level
  }).length
}

function translateRisk(level) {
  const norm = level?.toLowerCase() || ''
  if (norm === 'critical' || norm === 'crítico') return 'crítico'
  if (norm === 'high' || norm === 'alto') return 'alto'
  if (norm === 'moderate' || norm === 'moderado') return 'moderado'
  if (norm === 'low' || norm === 'baixo') return 'baixo'
  return 'não calculado'
}

function getRiskBadgeClass(level) {
  const norm = level?.toLowerCase() || ''
  if (norm === 'critical' || norm === 'crítico') return 'bg-red-50 text-red-600 border border-red-100'
  if (norm === 'high' || norm === 'alto') return 'bg-orange-50 text-orange-600 border border-orange-100'
  if (norm === 'moderate' || norm === 'moderado') return 'bg-yellow-50 text-yellow-600 border border-yellow-100'
  return 'bg-green-50 text-green-600 border border-green-100'
}

function getRiskDotClass(level) {
  const norm = level?.toLowerCase() || ''
  if (norm === 'critical' || norm === 'crítico') return 'bg-red-500'
  if (norm === 'high' || norm === 'alto') return 'bg-orange-500'
  if (norm === 'moderate' || norm === 'moderado') return 'bg-yellow-500'
  return 'bg-green-500'
}

// Nova lógica dinamicamente atrelada à borda inteira do card
function getRiskBorderClass(level) {
  const norm = level?.toLowerCase() || ''
  if (norm === 'critical' || norm === 'crítico') return 'border-red-500/40 hover:border-red-500'
  if (norm === 'high' || norm === 'alto') return 'border-orange-500/40 hover:border-orange-500'
  if (norm === 'moderate' || norm === 'moderado') return 'border-yellow-500/40 hover:border-yellow-500'
  return 'border-green-500/40 hover:border-green-500'
}

function formatEventDate(dateString) {
  if (!dateString) return 'A definir data'
  const date = new Date(dateString)
  return date.toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  }) + '  ·  ' + date.toLocaleTimeString('pt-BR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

function handleCreateEvent() { emit('navigate', 'event-form') }
function handleViewDetails(id) { emit('view-event', id) }

async function handleLogout() {
  try { await api.post('/logout') } catch (e) { console.error(e) }
  finally {
    localStorage.removeItem('token')
    emit('navigate', 'login')
  }
}

onMounted(() => { fetchEvents() })
</script>

<template>
  <div class="w-full max-w-[1280px] mx-auto -mt-8 pt-0 px-6 pb-6 text-white font-roboto antialiased">
    
    <header class="flex justify-between items-center mb-10 pt-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-brand-grad flex items-center justify-center shadow-md">
          <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L4 8V16L12 22L20 16V8L12 2Z" stroke="currentColor" stroke-width="1.8"/>
          </svg>
        </div>
        <div>
          <h1 class="font-display font-bold text-xl leading-tight bg-brand-grad bg-clip-text text-transparent">EventChild</h1>
          <p class="text-[10px] tracking-wider uppercase font-semibold text-white/70">Clima & Risco para o seu Evento</p>
        </div>
      </div>
      
      <div class="flex items-center gap-3">
        <button @click="handleCreateEvent" class="bg-cta-grad text-white font-normal text-xs px-5 py-2.5 rounded-xl shadow-md hover:opacity-95 transition-all flex items-center gap-2">
          <span>+ Novo evento</span>
        </button>
        <button @click="handleLogout" class="border border-white/10 bg-white/5 hover:bg-white/10 text-white/80 text-xs px-4 py-2.5 rounded-xl transition-all font-light">
          Sair
        </button>
      </div>
    </header>

    <div class="mb-8">
      <h2 class="font-roboto font-light text-3xl text-white/90 tracking-wide">Seus eventos</h2>
      <p class="text-white/60 text-sm mt-1 font-light">Acompanhe a previsão e o risco climático de cada evento em um só lugar.</p>
    </div>

  <section class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <!-- Todos -->
    <div 
      @click="currentFilter = 'all'" 
      class="bg-surface border rounded-2xl p-4 shadow-sm text-text-primary cursor-pointer transition-all duration-200 select-none"
      :class="currentFilter === 'all' ? 'border-brand-violet ring-2 ring-brand-violet/10 scale-[1.01]' : 'border-line-soft opacity-90 hover:opacity-100'"
    >
      <span class="block font-roboto font-light text-3xl text-brand-violet">{{ events.length }}</span>
      <span class="text-xs font-normal text-text-secondary">Ativos</span>
    </div>

    <!-- Baixo -->
    <div 
      @click="currentFilter = 'low'" 
      class="bg-surface border rounded-2xl p-4 shadow-sm text-text-primary cursor-pointer transition-all duration-200 select-none"
      :class="currentFilter === 'low' ? 'border-green-500 ring-2 ring-green-500/10 scale-[1.01]' : 'border-line-soft opacity-90 hover:opacity-100'"
    >
      <span class="block font-roboto font-light text-3xl text-green-500">{{ countByRisk('low') }}</span>
      <span class="text-xs font-normal text-text-secondary">Baixos</span>
    </div>

    <!-- Moderado -->
    <div 
      @click="currentFilter = 'moderate'" 
      class="bg-surface border rounded-2xl p-4 shadow-sm text-text-primary cursor-pointer transition-all duration-200 select-none"
      :class="currentFilter === 'moderate' ? 'border-yellow-500 ring-2 ring-yellow-500/10 scale-[1.01]' : 'border-line-soft opacity-90 hover:opacity-100'"
    >
      <span class="block font-roboto font-light text-3xl text-yellow-500">{{ countByRisk('moderate') }}</span>
      <span class="text-xs font-normal text-text-secondary">Moderados</span>
    </div>

    <!-- Alto -->
    <div 
      @click="currentFilter = 'high'" 
      class="bg-surface border rounded-2xl p-4 shadow-sm text-text-primary cursor-pointer transition-all duration-200 select-none"
      :class="currentFilter === 'high' ? 'border-orange-500 ring-2 ring-orange-500/10 scale-[1.01]' : 'border-line-soft opacity-90 hover:opacity-100'"
    >
      <span class="block font-roboto font-light text-3xl text-orange-500">{{ countByRisk('high') }}</span>
      <span class="text-xs font-normal text-text-secondary">Altos</span>
    </div>

    <!-- Crítico -->
    <div 
      @click="currentFilter = 'critical'" 
      class="bg-surface border rounded-2xl p-4 shadow-sm text-text-primary cursor-pointer transition-all duration-200 select-none"
      :class="currentFilter === 'critical' ? 'border-red-500 ring-2 ring-red-500/10 scale-[1.01]' : 'border-line-soft opacity-90 hover:opacity-100'"
    >
      <span class="block font-roboto font-light text-3xl text-red-500">{{ countByRisk('critical') }}</span>
      <span class="text-xs font-normal text-text-secondary">Críticos</span>
    </div>
  </section>

    <div v-if="isLoading" class="flex flex-col items-center justify-center p-16">
      <span class="w-6 h-6 border-2 border-white/20 border-t-white rounded-full animate-spin mb-3"></span>
      <p class="text-white/50 text-xs font-light tracking-wide">Carregando dados dos eventos...</p>
    </div>

    <div v-else-if="errorMessage" class="flex flex-col items-center justify-center p-16 text-center w-full">
      <p class="text-white/90 font-roboto font-light text-sm tracking-wide mb-2">
        Não foi possível carregar a lista de eventos.
      </p>
      <button @click="fetchEvents" class="text-white/50 hover:text-white text-xs underline transition-colors font-light">
        Tentar novamente
      </button>
    </div>

    <div v-else-if="filteredEvents.length === 0" class="flex flex-col items-center justify-center p-16 text-center w-full">
      <p class="text-white/50 text-sm font-light tracking-wide">Nenhum evento corresponde ao risco selecionado.</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="event in filteredEvents" 
        :key="event.id" 
        class="bg-surface border-1 rounded-2xl overflow-hidden flex flex-col group text-text-primary transition-all duration-300 ease-out hover:shadow-xl hover:-translate-y-0.5 hover:ring-opacity-50"
        :class="getRiskBorderClass(getWeatherAssessment(event)?.risk_level)"
      >
        <div class="p-5 flex-1 flex flex-col">
          <div class="flex justify-between items-start gap-4 mb-2">
            <h3 class="font-roboto font-semibold text-lg text-text-primary tracking-wide leading-snug">{{ event.name || 'Evento sem Nome' }}</h3>
            <span class="text-[11px] uppercase font-semibold tracking-wider px-2.5 py-0.5 rounded bg-brand-violet/10 text-brand-violet border border-brand-violet/20">
              {{ event.event_type }}
            </span>
          </div>

          <div class="flex items-center gap-1.5 text-text-secondary text-sm mb-3 font-light">
            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>{{ formatEventDate(event.start_at) }}</span>
          </div>

          <div class="mb-4">
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full" :class="getRiskBadgeClass(getWeatherAssessment(event)?.risk_level)">
              <span class="w-2 h-2 rounded-full" :class="getRiskDotClass(getWeatherAssessment(event)?.risk_level)"></span>
              Risco {{ translateRisk(getWeatherAssessment(event)?.risk_level) }}
            </span>
          </div>

          <div v-if="getWeatherAssessment(event)" class="bg-[#F8F9FA] border border-[#E9ECEF] rounded-xl p-4 mb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="text-3xl">
                {{ getWeatherAssessment(event).rain_probability > 40 ? '🌧️' : '☀️' }}
              </span>
              <div>
                <span class="block font-roboto font-bold text-2xl text-text-primary">{{ Math.round(getWeatherAssessment(event).temperature) }}°C</span>
                <span class="text-xs text-text-secondary block font-light">Condições locais</span>
              </div>
            </div>
            <div class="text-right">
              <span class="block font-roboto font-bold text-base text-brand-violet">
                {{ Math.round(getWeatherAssessment(event).wind_speed) }}km/h
              </span>
              <span class="text-[10px] uppercase tracking-wider text-text-secondary font-semibold block">Vento</span>
            </div>
          </div>

          <div v-if="getWeatherAssessment(event)" class="grid grid-cols-3 gap-2 text-xs text-[#6C757D] border-t border-line-soft pt-3 mt-auto font-light">
            <div class="flex items-center gap-1">⚡ {{ Math.round(getWeatherAssessment(event).wind_speed) }} km/h</div>
            <div class="flex items-center gap-1">💧 {{ getWeatherAssessment(event).humidity }}%</div>
            <div class="flex items-center gap-1">🌧️ {{ getWeatherAssessment(event).rain_probability }}%</div>
          </div>
          
          <div v-else class="text-sm text-text-secondary mt-auto font-light">
            Lat: {{ event.latitude }} | Lon: {{ event.longitude }}
          </div>
        </div>

        <button @click="handleViewDetails(event.id)" class="w-full bg-elevated border-t border-line hover:bg-surface/50 text-brand-violet font-semibold text-sm py-3 text-center flex items-center justify-between px-5 transition-all">
          <span>Ver detalhes completos</span>
          <span>→</span>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-roboto {
  font-family: 'Roboto', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-hash-smoothing: grayscale;
}
</style>