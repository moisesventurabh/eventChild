<script setup>
import { ref } from 'vue'
import api from '../services/api'

const emit = defineEmits(['navigate'])
const name = ref('')
const company = ref('')
const email = ref('')
const password = ref('')
const isLoading = ref(false)
const errorMessage = ref('')

async function handleRegister() {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const response = await api.post('/register', {
      name: name.value,
      company: company.value,
      email: email.value,
      password: password.value
    })
    
    if (response.data.token) {
      localStorage.setItem('token', response.data.token)
    }
    
    emit('navigate', 'dash')
  } catch (error) {
    console.error('Erro no registro:', error)
    errorMessage.value = error.response?.data?.message || 'Erro ao criar conta. Verifique os dados.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="bg-surface border border-line-soft rounded-xl p-8 w-full max-w-[520px] shadow-lg">
    
    <div class="text-center mb-8">
      <div class="inline-flex items-center gap-2 mb-3 justify-center">
        <div class="w-7 h-7 rounded-lg bg-brand-grad flex items-center justify-center">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L4 8V16L12 22L20 16V8L12 2Z" stroke="#FFFFFF" stroke-width="1.8"/></svg>
        </div>
        <span class="font-display font-bold text-lg bg-brand-grad bg-clip-text text-transparent">EventChild</span>
      </div>
      <h2 class="font-display font-bold text-xl text-text-primary">Crie sua conta</h2>
      <p class="text-text-secondary text-xs mt-1">Comece a monitorar os riscos do seu evento.</p>
    </div>

    <div v-if="errorMessage" class="bg-risk-critBg border border-risk-crit/20 text-risk-crit rounded-xl p-3 text-xs text-center mb-4">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="handleRegister" class="flex flex-col gap-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-text-primary">Nome Completo</label>
          <input v-model="name" type="text" placeholder="Seu nome" required class="w-full bg-elevated border border-line rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all" />
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-semibold text-text-primary">Empresa/Produtora</label>
          <input v-model="company" type="text" placeholder="Sua empresa" required class="w-full bg-elevated border border-line rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all" />
        </div>
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-text-primary">E-mail corporativo</label>
        <input v-model="email" type="email" placeholder="nome@empresa.com" required class="w-full bg-elevated border border-line rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all" />
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-text-primary">Senha</label>
        <input v-model="password" type="password" placeholder="Mínimo de 8 caracteres" required minlength="8" class="w-full bg-elevated border border-line rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all" />
      </div>

      <button type="submit" :disabled="isLoading" class="w-full mt-2 bg-cta-grad text-white font-semibold text-sm py-3 rounded-xl shadow-md hover:opacity-95 transition-all flex items-center justify-center gap-2">
        <span v-if="isLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
        {{ isLoading ? 'Criando Conta...' : 'Criar Conta Grátis' }}
      </button>
    </form>

    <div class="text-center mt-6 pt-6 border-t border-line-soft">
      <p class="text-xs text-text-secondary">
        Já possui uma conta? 
        <button @click="emit('navigate', 'login')" class="text-brand-violet font-semibold hover:underline">Faça login</button>
      </p>
    </div>

  </div>
</template>