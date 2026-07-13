<script setup>
import { ref } from 'vue'

const props = defineProps({
  eventId: { type: Number, default: null }
})

const emit = defineEmits(['navigate'])

// Simulação de resposta detalhada vinda do backend para o ID selecionado
const event = ref({
  id: 1,
  name: 'Festival de Música Urbana',
  date: '22 Ago 2026 · 16:00',
  type: 'Outdoor',
  latitude: '-19.9224',
  longitude: '-43.9452',
  risk: 'crit',
  riskLabel: 'Risco Crítico Identificado',
  lastUpdate: 'Atualizado há 4 minutos',
  metrics: {
    temp: '22°C',
    wind: '40 km/h',
    windStatus: 'Alerta de Linha de Instabilidade',
    windSeverity: 'crit',
    rain: '68%',
    rainStatus: 'Chuva Forte Acumulada',
    rainSeverity: 'high',
    humidity: '78%',
    humidityStatus: 'Condições Normais',
    humiditySeverity: 'low',
    lightning: 'Alta Densidade',
    lightningStatus: 'Descargas em Raio de 10km',
    lightningSeverity: 'crit'
  },
  notes: 'Recomenda-se acompanhamento rigoroso das estruturas de palco e fechamentos laterais devido às rajadas previstas para o início da noite.'
})
</script>

<template>
  <div class="flex flex-col gap-6">
    <!-- Back to Dash Action bar -->
    <div class="flex items-center justify-between">
      <button @click="emit('navigate', 'dash')" class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-text-secondary hover:text-text-primary transition-colors">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Voltar ao painel
      </button>
      <span class="font-mono text-[11px] text-text-tertiary bg-surface border border-line-soft px-3 py-1 rounded-full">{{ event.lastUpdate }}</span>
    </div>

    <!-- Main Header Card -->
    <div class="bg-surface border border-line-soft rounded-l p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <div class="flex items-center gap-3 mb-2 flex-wrap">
          <h1 class="font-display font-bold text-2xl sm:text-3xl text-text-primary leading-tight">{{ event.name }}</h1>
          <span class="font-mono text-[10px] tracking-wide uppercase px-2 py-0.5 bg-risk-lowBg text-brand-violet border border-brand-violet/20 rounded">{{ event.type }}</span>
        </div>
        <div class="font-mono text-xs text-text-secondary flex flex-wrap items-center gap-x-4 gap-y-1">
          <span>📅 {{ event.date }}</span>
          <span>📍 Lat: {{ event.latitude }} | Lon: {{ event.longitude }}</span>
        </div>
      </div>

      <!-- Alerta Geral Emblocado -->
      <div :class="['px-4 py-3 rounded-xl font-display font-bold text-sm tracking-wide inline-flex items-center gap-2 border w-full md:w-auto justify-center md:justify-start', 
                   event.risk === 'crit' ? 'bg-risk-critBg text-risk-crit border-risk-crit/20' : 'bg-risk-highBg text-risk-high border-risk-high/20']">
        <span class="w-2 h-2 rounded-full bg-current animate-pulse"></span>
        {{ event.riskLabel }}
      </div>
    </div>

    <!-- Grid de Métricas de Risco (Análise Granular) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- VENTO -->
      <div class="bg-surface border border-line-soft rounded-l p-5 shadow-sm flex flex-col justify-between">
        <div>
          <div class="flex justify-between items-center mb-3">
            <span class="font-mono text-[11px] font-bold text-text-secondary uppercase">💨 Vento Dinâmico</span>
            <span class="text-[10px] uppercase px-2 py-0.5 rounded font-mono font-bold bg-risk-critBg text-risk-crit border border-risk-crit/10">Crítico</span>
          </div>
          <div class="font-display font-bold text-3xl text-text-primary">{{ event.metrics.wind }}</div>
        </div>
        <div class="text-[11.5px] text-risk-crit font-medium mt-4 pt-2 border-t border-line-soft/60">{{ event.metrics.windStatus }}</div>
      </div>

      <!-- DISPARO DE RAIOS / DESCARGAS -->
      <div class="bg-surface border border-line-soft rounded-l p-5 shadow-sm flex flex-col justify-between">
        <div>
          <div class="flex justify-between items-center mb-3">
            <span class="font-mono text-[11px] font-bold text-text-secondary uppercase">⚡️ Raios / Descargas</span>
            <span class="text-[10px] uppercase px-2 py-0.5 rounded font-mono font-bold bg-risk-critBg text-risk-crit border border-risk-crit/10">Crítico</span>
          </div>
          <div class="font-display font-bold text-2xl text-text-primary">{{ event.metrics.lightning }}</div>
        </div>
        <div class="text-[11.5px] text-risk-crit font-medium mt-4 pt-2 border-t border-line-soft/60">{{ event.metrics.lightningStatus }}</div>
      </div>

      <!-- CHUVA / PRECIPITAÇÃO -->
      <div class="bg-surface border border-line-soft rounded-l p-5 shadow-sm flex flex-col justify-between">
        <div>
          <div class="flex justify-between items-center mb-3">
            <span class="font-mono text-[11px] font-bold text-text-secondary uppercase">🌧️ Precipitação</span>
            <span class="text-[10px] uppercase px-2 py-0.5 rounded font-mono font-bold bg-risk-highBg text-risk-high border border-risk-high/10">Alto</span>
          </div>
          <div class="font-display font-bold text-3xl text-text-primary">{{ event.metrics.rain }}</div>
        </div>
        <div class="text-[11.5px] text-risk-high font-medium mt-4 pt-2 border-t border-line-soft/60">{{ event.metrics.rainStatus }}</div>
      </div>

      <!-- UMIDADE / TEMPERATURA -->
      <div class="bg-surface border border-line-soft rounded-l p-5 shadow-sm flex flex-col justify-between">
        <div>
          <div class="flex justify-between items-center mb-3">
            <span class="font-mono text-[11px] font-bold text-text-secondary uppercase">🌡️ Umidade / Temp</span>
            <span class="text-[10px] uppercase px-2 py-0.5 rounded font-mono font-bold bg-risk-lowBg text-risk-low border border-risk-low/10">Estável</span>
          </div>
          <div class="font-display font-bold text-3xl text-text-primary">{{ event.metrics.temp }} <span class="text-lg text-text-secondary">/ {{ event.metrics.humidity }}</span></div>
        </div>
        <div class="text-[11.5px] text-risk-low font-medium mt-4 pt-2 border-t border-line-soft/60">{{ event.metrics.humidityStatus }}</div>
      </div>
    </div>

    <!-- Notas de Contingência Técnica -->
    <div class="bg-elevated border border-line rounded-l p-6">
      <h3 class="font-display font-bold text-base text-text-primary mb-2 flex items-center gap-2">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Recomendação e Análise do Engenheiro Climático (IA)
      </h3>
      <p class="text-text-secondary text-sm leading-relaxed font-normal">{{ event.notes }}</p>
    </div>
  </div>
</template>