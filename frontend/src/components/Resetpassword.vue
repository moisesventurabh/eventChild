<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const props = defineProps({
  token: { type: String, default: '' },
  resetEmail: { type: String, default: '' }
})

const emit = defineEmits(['navigate'])

const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const isLoading = ref(false)
const isDone = ref(false)
const errorMessage = ref('')

onMounted(() => {
  email.value = props.resetEmail || ''
})

async function handleSubmit() {
  errorMessage.value = ''
  isLoading.value = true

  try {
    await api.post('/reset-password', {
      token: props.token,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })

    isDone.value = true
  } catch (error) {
    console.error('Erro ao redefinir senha:', error)
    errorMessage.value = error.response?.data?.message || 'Não foi possível redefinir a senha. O link pode ter expirado.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="bg-surface border border-line-soft rounded-xl p-8 w-full max-w-[420px] shadow-lg">

    <div class="text-center mb-8">
      <div class="inline-flex items-center gap-2 mb-3 justify-center">
        <div class="w-7 h-7 rounded-lg bg-brand-grad flex items-center justify-center">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L4 8V16L12 22L20 16V8L12 2Z" stroke="#FFFFFF" stroke-width="1.8"/></svg>
        </div>
        <span class="font-display font-bold text-lg bg-brand-grad bg-clip-text text-transparent">EventChild</span>
      </div>
      <h2 class="font-display font-bold text-xl text-text-primary">Definir nova senha</h2>
      <p class="text-text-secondary text-xs mt-1">Escolha uma nova senha para sua conta.</p>
    </div>

    <div v-if="isDone" class="bg-risk-lowBg border border-risk-low/20 text-risk-low rounded-xl p-4 text-xs text-center leading-relaxed mb-4">
      ✓ Senha redefinida com sucesso.
    </div>

    <div v-if="errorMessage" class="bg-risk-critBg border border-risk-crit/20 text-risk-crit rounded-xl p-3 text-xs text-center mb-4">
      {{ errorMessage }}
    </div>

    <form v-if="!isDone" @submit.prevent="handleSubmit" class="flex flex-col gap-4">
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-text-primary">E-mail</label>
        <input v-model="email" type="email" placeholder="nome@empresa.com" required class="w-full bg-elevated border border-line rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all" />
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-text-primary">Nova senha</label>
        <input v-model="password" type="password" placeholder="Mínimo de 8 caracteres" required minlength="8" class="w-full bg-elevated border border-line rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all" />
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-text-primary">Confirmar nova senha</label>
        <input v-model="passwordConfirmation" type="password" placeholder="Repita a nova senha" required minlength="8" class="w-full bg-elevated border border-line rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-brand-violet focus:bg-surface focus:ring-4 focus:ring-brand-violet/10 transition-all" />
      </div>

      <button type="submit" :disabled="isLoading" class="w-full mt-2 bg-cta-grad text-white font-semibold text-sm py-3 rounded-xl shadow-md hover:opacity-95 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
        <span v-if="isLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
        {{ isLoading ? 'Salvando...' : 'Redefinir Senha' }}
      </button>
    </form>

    <div class="text-center mt-6 pt-6 border-t border-line-soft">
      <button @click="emit('navigate', 'login')" class="text-brand-violet font-semibold text-xs hover:underline">Voltar para o login</button>
    </div>

  </div>
</template>