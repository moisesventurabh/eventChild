<script setup>
import { ref } from 'vue'
import api from '../services/api'

const emit = defineEmits(['navigate'])
const email = ref('')
const isLoading = ref(false)
const isSent = ref(false)
const errorMessage = ref('')

async function handleReset() {
  isLoading.value = true
  errorMessage.value = ''

  try {
    await api.post('/forgot-password', { email: email.value })
    isSent.value = true
  } catch (error) {
    console.error('Erro ao solicitar recuperação de senha:', error)
    errorMessage.value = error.response?.data?.message || 'Não foi possível enviar o link de recuperação. Tente novamente.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="bg-surface border border-line-soft rounded-xl p-8 w-full max-w-[420px] shadow-lg">
    
    <div class="text-center mb-8">
      <div @click="emit('navigate', 'login')" class="cursor-pointer inline-flex items-center gap-2 mb-3 justify-center">
        <div class="w-7 h-7 rounded-lg bg-brand-grad flex items-center justify-center">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L4 8V16L12 22L20 16V8L12 2Z" stroke="#FFFFFF" stroke-width="1.8"/></svg>
        </div>
        <span class="font-display font-bold text-lg bg-brand-grad bg-clip-text text-transparent">EventChild</span>
      </div>
      <h2 class="font-display font-bold text-xl text-text-primary">Recuperar senha</h2>
      <p class="text-text-secondary text-xs mt-1">Insira seu e-mail para receber as instruções de redefinição.</p>
    </div>

    <div v-if="isSent" class="bg-risk-lowBg border border-risk-low/20 text-risk-low rounded-xl p-4 text-xs text-center leading-relaxed mb-4">
      ✓ Se este e-mail estiver cadastrado, um link de recuperação foi enviado para a sua caixa de entrada.
    </div>

    <div v-if="errorMessage" class="bg-risk-critBg border border-risk-crit/20 text-risk-crit rounded-xl p-3 text-xs text-center mb-4">
      {{ errorMessage }}
    </div>

    <form v-if="!isSent" @submit.prevent="handleReset" class="flex flex-col gap-4">
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-text-primary">E-mail corporativo</label>
        <input v-model="email" type="email" placeholder="nome@empresa.com" required class="w-full bg-elevated border border-line rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all" />
      </div>

      <button type="submit" :disabled="isLoading" class="w-full mt-2 bg-cta-grad text-white font-semibold text-sm py-3 rounded-xl shadow-md hover:opacity-95 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
        <span v-if="isLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
        {{ isLoading ? 'Enviando...' : 'Enviar Link de Recuperação' }}
      </button>
    </form>

    <div class="text-center mt-6 pt-6 border-t border-line-soft">
      <button @click="emit('navigate', 'login')" class="text-brand-violet font-semibold text-xs hover:underline">Voltar para o login</button>
    </div>

  </div>
</template>