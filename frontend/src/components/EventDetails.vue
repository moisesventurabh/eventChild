<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../services/api'

const props = defineProps({
  eventId: { type: [Number, String], default: null }
})

const emit = defineEmits(['navigate'])

const event = ref(null)
const isLoading = ref(true)
const errorMessage = ref('')

async function fetchEventDetails() {
  if (!props.eventId) {
    errorMessage.value = 'ID do evento inválido.'
    isLoading.value = false
    return
  }

  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await api.get(`/events/${props.eventId}`)
    event.value = response.data.data || response.data
  } catch (error) {
    console.error('Erro ao carregar detalhes do evento:', error)
    errorMessage.value = 'Não foi possível carregar os detalhes deste evento.'
  } finally {
    isLoading.value = false
  }
}

const weather = computed(() => event.value?.weather_assessment || null)

function getRiskBorderClass(level) {
  const norm = level?.toLowerCase() || ''
  if (norm === 'critical' || norm === 'crítico') return 'border-red-500/40 hover:border-red-500 hover:shadow-xl hover:ring-1 hover:ring-red-500/50'
  if (norm === 'high' || norm === 'alto') return 'border-orange-500/40 hover:border-orange-500 hover:shadow-xl hover:ring-1 hover:ring-orange-500/50'
  if (norm === 'moderate' || norm === 'moderado') return 'border-yellow-500/40 hover:border-yellow-500 hover:shadow-xl hover:ring-1 hover:ring-yellow-500/50'
  return 'border-green-500/40 hover:border-green-500 hover:shadow-xl hover:ring-1 hover:ring-green-500/50'
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

function translateRisk(level) {
  const norm = level?.toLowerCase() || ''
  if (norm === 'critical' || norm === 'crítico') return 'crítico'
  if (norm === 'high' || norm === 'alto') return 'alto'
  if (norm === 'moderate' || norm === 'moderado') return 'moderado'
  if (norm === 'low' || norm === 'baixo') return 'baixo'
  return 'não calculado'
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

onMounted(() => {
  fetchEventDetails()
})
</script>

<template>
  <div class="flex flex-col gap-6 text-white font-roboto antialiased">
    
    <div class="flex items-center justify-between">
      <button @click="emit('navigate', 'dash')" class="inline-flex items-center gap-2 text-xs text-slate-100 hover:text-slate-200 transition-colors cursor-pointer mb-6 select-none font-light">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Voltar ao painel
      </button>
      <span v-if="weather" class="text-xs text-text-tertiary bg-surface border border-line-soft px-3 py-1 rounded-full">
        Análise Climática Atualizada
      </span>
    </div>

    <div v-if="isLoading" class="flex flex-col items-center justify-center p-16 bg-surface border border-line-soft rounded-2xl">
      <span class="w-6 h-6 border-2 border-white/20 border-t-white rounded-full animate-spin mb-3"></span>
      <p class="text-white/50 text-sm font-light tracking-wide">Carregando detalhes do evento...</p>
    </div>

    <div v-else-if="errorMessage" class="flex flex-col items-center justify-center p-16 text-center bg-surface border border-line-soft rounded-2xl w-full">
      <p class="text-white/90 font-light text-base tracking-wide mb-3">{{ errorMessage }}</p>
      <button @click="fetchEventDetails" class="text-brand-violet hover:underline text-sm font-semibold transition-colors">
        Tentar novamente
      </button>
    </div>

    <div v-else-if="event" class="flex flex-col gap-6">
      
      <div 
        class="bg-surface border rounded-2xl p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 transition-all duration-300 ease-out"
        :class="getRiskBorderClass(weather?.risk_level)"
      >
        <div>
          <div class="flex items-center gap-3 mb-2 flex-wrap">
            <h1 class="font-roboto font-bold text-2xl sm:text-3xl text-text-primary leading-tight">{{ event.name || 'Evento sem Nome' }}</h1>
            <span class="text-[11px] uppercase font-semibold tracking-wider px-2.5 py-0.5 bg-brand-violet/10 text-brand-violet border border-brand-violet/20 rounded">
              {{ event.event_type }}
            </span>
          </div>
          
          <div class="text-sm text-text-secondary flex flex-wrap items-center gap-x-4 gap-y-1 font-light">
            <span class="flex items-center gap-1.5">
              <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              {{ formatEventDate(event.start_at) }}
            </span>
            <span class="flex items-center gap-1.5">
              <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              {{ event.address || 'Endereço não disponível' }}
            </span>
          </div>
        </div>

        <div class="mb-2 md:mb-0 w-full md:w-auto">
          <span class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-full" :class="getRiskBadgeClass(weather?.risk_level)">
            <span class="w-2.5 h-2.5 rounded-full animate-pulse" :class="getRiskDotClass(weather?.risk_level)"></span>
            Status: Risco {{ translateRisk(weather?.risk_level) }}
          </span>
        </div>
      </div>

      <div v-if="weather" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-surface border rounded-2xl p-5 flex flex-col justify-between transition-all duration-300 ease-out" :class="getRiskBorderClass(weather.risk_level)">
          <div>
            <div class="flex justify-between items-center mb-3">
              <span class="text-xs font-semibold text-text-secondary uppercase tracking-wider">💨 Velocidade do Vento</span>
            </div>
            <div class="font-roboto font-bold text-3xl text-text-primary">{{ Math.round(weather.wind_speed) }} <span class="text-lg font-normal text-text-secondary">km/h</span></div>
          </div>
          <div class="text-xs text-text-secondary font-light mt-4 pt-2 border-t border-line-soft/60">Velocidade monitorada na estação local.</div>
        </div>

        <div class="bg-surface border rounded-2xl p-5 flex flex-col justify-between transition-all duration-300 ease-out" :class="getRiskBorderClass(weather.risk_level)">
          <div>
            <div class="flex justify-between items-center mb-3">
              <span class="text-xs font-semibold text-text-secondary uppercase tracking-wider">🌧️ Prob. de Chuva</span>
            </div>
            <div class="font-roboto font-bold text-3xl text-text-primary">{{ weather.rain_probability }}%</div>
          </div>
          <div class="text-xs text-text-secondary font-light mt-4 pt-2 border-t border-line-soft/60">Probabilidade de precipitação acumulada.</div>
        </div>

        <div class="bg-surface border rounded-2xl p-5 flex flex-col justify-between transition-all duration-300 ease-out" :class="getRiskBorderClass(weather.risk_level)">
          <div>
            <div class="flex justify-between items-center mb-3">
              <span class="text-xs font-semibold text-text-secondary uppercase tracking-wider">💧 Umidade Relativa</span>
            </div>
            <div class="font-roboto font-bold text-3xl text-text-primary">{{ weather.humidity }}%</div>
          </div>
          <div class="text-xs text-text-secondary font-light mt-4 pt-2 border-t border-line-soft/60">Índice de umidade atmosférica atual.</div>
        </div>

        <div class="bg-surface border rounded-2xl p-5 flex flex-col justify-between transition-all duration-300 ease-out" :class="getRiskBorderClass(weather.risk_level)">
          <div>
            <div class="flex justify-between items-center mb-3">
              <span class="text-xs font-semibold text-text-secondary uppercase tracking-wider">🌡️ Temperatura</span>
            </div>
            <div class="font-roboto font-bold text-3xl text-text-primary">{{ Math.round(weather.temperature) }}°C</div>
          </div>
          <div class="text-xs text-text-secondary font-light mt-4 pt-2 border-t border-line-soft/60">Sensação térmica estimada para o local.</div>
        </div>
      </div>

      <div v-else class="bg-surface border border-line-soft rounded-2xl p-6 text-center text-text-secondary text-sm font-light">
        Nenhuma avaliação meteorológica automatizada foi processada para este local.
      </div>

      <div 
        class="bg-elevated border rounded-2xl p-6 transition-all duration-300 ease-out"
        :class="getRiskBorderClass(weather?.risk_level)"
      >
        <h3 class="font-roboto font-bold text-lg text-text-primary mb-3 flex items-center gap-2">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          Parecer Técnico e Recomendações (IA)
        </h3>
        <p class="text-text-secondary text-base leading-relaxed font-light">
          {{ weather?.recommendation || 'Aguardando geração de insights ou recomendações técnicas de contingência para este evento.' }}
        </p>
      </div>

    </div>
  </div>
</template>

<style scoped>
.font-roboto {
  font-family: 'Roboto', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
</style>