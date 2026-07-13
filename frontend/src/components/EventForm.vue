<script setup>
import { ref } from 'vue'

const emit = defineEmits(['navigate'])

const form = ref({
  name: '',
  date: '',
  time: '',
  type: 'Outdoor', // Default focado em maior risco
  latitude: '',
  longitude: '',
  description: ''
})

const isLoading = ref(false)

function handleSubmit() {
  isLoading.value = true
  // Simulação de envio para a API Laravel
  setTimeout(() => {
    isLoading.value = false
    emit('navigate', 'dash')
  }, 1200)
}
</script>

<template>
  <div class="max-w-[680px] mx-auto bg-surface border border-line-soft rounded-l p-7 sm:p-9 shadow-lg">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8 pb-5 border-b border-line-soft">
      <div>
        <h2 class="font-display font-bold text-2xl text-text-primary">Cadastrar Novo Evento</h2>
        <p class="text-text-secondary text-xs mt-1">Insira as coordenadas exatas para o cálculo preditivo de risco.</p>
      </div>
      <button @click="emit('navigate', 'dash')" class="font-mono text-[11px] tracking-wider uppercase bg-elevated hover:bg-elevated-2 text-text-secondary px-3.5 py-2 rounded-xl border border-line-soft transition-colors">
        Cancelar
      </button>
    </div>

    <!-- Form -->
    <form @submit.prevent="handleSubmit" class="flex flex-col gap-5.5">
      <!-- Nome do Evento -->
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-bold text-text-primary uppercase tracking-wide">Nome do Evento</label>
        <input v-model="form.name" type="text" placeholder="Ex: Festival de Música Urbana 2026" required class="w-full bg-elevated border border-line rounded-xl px-4 py-3 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all" />
      </div>

      <!-- Data e Hora -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-text-primary uppercase tracking-wide">Data do Evento</label>
          <input v-model="form.date" type="date" required class="w-full bg-elevated border border-line rounded-xl px-4 py-3 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all" />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-text-primary uppercase tracking-wide">Horário de Início</label>
          <input v-model="form.time" type="time" required class="w-full bg-elevated border border-line rounded-xl px-4 py-3 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all" />
        </div>
      </div>

      <!-- Tipo de Ambiente (Segmented Control para UX fluida) -->
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-bold text-text-primary uppercase tracking-wide">Tipo de Ambiente</label>
        <div class="grid grid-cols-2 gap-2 bg-elevated p-1 rounded-xl border border-line-soft">
          <button type="button" @click="form.type = 'Outdoor'" :class="['py-2.5 text-xs font-mono uppercase tracking-wider rounded-lg font-semibold transition-all', form.type === 'Outdoor' ? 'bg-surface text-brand-violet shadow-sm border border-line-soft' : 'text-text-secondary hover:text-text-primary']">
            ⛺️ Outdoor (Céu Aberto)
          </button>
          <button type="button" @click="form.type = 'Indoor'" :class="['py-2.5 text-xs font-mono uppercase tracking-wider rounded-lg font-semibold transition-all', form.type === 'Indoor' ? 'bg-surface text-brand-violet shadow-sm border border-line-soft' : 'text-text-secondary hover:text-text-primary']">
            🏢 Indoor (Espaço Fechado)
          </button>
        </div>
      </div>

      <!-- Coordenadas Geográficas de Precisão -->
      <div class="bg-elevated/50 border border-line-soft rounded-xl p-4.5">
        <div class="flex items-center gap-2 mb-3">
          <svg class="w-4 h-4 text-brand-violet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span class="font-mono text-xs font-bold text-text-primary uppercase tracking-wide">Coordenadas do Palco / Estrutura</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-medium text-text-secondary">Latitude</label>
            <input v-model="form.latitude" type="text" placeholder="Ex: -19.9224" required class="w-full bg-surface border border-line rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-brand-violet focus:ring-4 focus:ring-brand-violet/10 transition-all font-mono" />
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-medium text-text-secondary">Longitude</label>
            <input v-model="form.longitude" type="text" placeholder="Ex: -43.9452" required class="w-full bg-surface border border-line rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-brand-violet focus:ring-4 focus:ring-brand-violet/10 transition-all font-mono" />
          </div>
        </div>
        <p class="text-[10.5px] text-text-tertiary mt-2.5 leading-relaxed">Nota: Use a latitude/longitude exata obtida no mapa para garantir que os alertas de rajadas de vento e células de tempestade cubram o perímetro correto.</p>
      </div>

      <!-- Observações Técnicas -->
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-bold text-text-primary uppercase tracking-wide">Notas de Infraestrutura (Opcional)</label>
        <textarea v-model="form.description" rows="3" placeholder="Ex: Cobertura em lona tensionada, gerador principal posicionado na face norte..." class="w-full bg-elevated border border-line rounded-xl px-4 py-3 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all resize-none"></textarea>
      </div>

      <!-- Submit Action -->
      <button type="submit" :disabled="isLoading" class="w-full mt-2 bg-cta-grad text-white font-semibold text-sm py-4 rounded-xl shadow-[0_12px_24px_-10px_rgba(124,47,224,0.45)] hover:scale-[1.005] active:scale-[0.995] transition-all flex items-center justify-center gap-2">
        <span v-if="isLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
        {{ isLoading ? 'Salvando no Banco...' : 'Ativar Monitoramento do Evento' }}
      </button>
    </form>
  </div>
</template>