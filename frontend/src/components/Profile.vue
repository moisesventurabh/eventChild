<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '../services/api'

const emit = defineEmits(['navigate'])

const form = reactive({
  name: '',
  email: '',
  current_password: '',
  password: '',
  password_confirmation: ''
})

const wantsPasswordChange = ref(false)
const isLoading = ref(false)
const isFetching = ref(true)
const errorMessage = ref('')
const successMessage = ref('')
const fieldErrors = ref({})

async function fetchUser() {
  isFetching.value = true
  try {
    const response = await api.get('/user')
    form.name = response.data.name || ''
    form.email = response.data.email || ''
  } catch (error) {
    console.error('Erro ao carregar dados do usuário:', error)
    errorMessage.value = 'Não foi possível carregar seus dados.'
  } finally {
    isFetching.value = false
  }
}

function togglePasswordChange() {
  wantsPasswordChange.value = !wantsPasswordChange.value
  if (!wantsPasswordChange.value) {
    form.current_password = ''
    form.password = ''
    form.password_confirmation = ''
    delete fieldErrors.value.current_password
    delete fieldErrors.value.password
  }
}

async function handleSubmit() {
  errorMessage.value = ''
  successMessage.value = ''
  fieldErrors.value = {}

  if (wantsPasswordChange.value && form.password !== form.password_confirmation) {
    fieldErrors.value.password = ['A confirmação de senha não corresponde à nova senha.']
    return
  }

  isLoading.value = true

  try {
    const payload = {
      name: form.name,
      email: form.email
    }

    if (wantsPasswordChange.value && form.password) {
      payload.current_password = form.current_password
      payload.password = form.password
      payload.password_confirmation = form.password_confirmation
    }

    const response = await api.put('/profile', payload)

    successMessage.value = response.data.message || 'Perfil atualizado com sucesso.'

    form.current_password = ''
    form.password = ''
    form.password_confirmation = ''
    wantsPasswordChange.value = false
  } catch (error) {
    console.error('Erro ao atualizar perfil:', error)
    if (error.response?.status === 422) {
      fieldErrors.value = error.response.data.errors || {}
      errorMessage.value = error.response.data.message || 'Verifique os dados informados.'
    } else {
      errorMessage.value = 'Não foi possível atualizar o perfil. Tente novamente.'
    }
  } finally {
    isLoading.value = false
  }
}

onMounted(() => { fetchUser() })
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
      <h2 class="font-roboto font-light text-3xl text-white/90 tracking-wide">Meu perfil</h2>
      <p class="text-slate-100 text-sm mt-1.5 font-light leading-relaxed">
        Atualize seus dados de nome, e-mail e senha de acesso.
      </p>
    </header>

    <div v-if="isFetching" class="flex flex-col items-center justify-center p-16">
      <span class="w-6 h-6 border-2 border-white/20 border-t-white rounded-full animate-spin mb-3"></span>
      <p class="text-white/50 text-xs font-light tracking-wide">Carregando seus dados...</p>
    </div>

    <form v-else @submit.prevent="handleSubmit" class="flex flex-col gap-6">

      <div v-if="successMessage" class="bg-green-50 border border-green-100 text-green-600 rounded-xl p-3 text-xs text-center">
        {{ successMessage }}
      </div>

      <div v-if="errorMessage" class="bg-red-50 border border-red-100 text-red-600 rounded-xl p-3 text-xs text-center">
        {{ errorMessage }}
      </div>

      <!-- Bloco 1: Dados pessoais -->
      <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
        <div class="flex items-start gap-3 mb-5">
          <span class="w-6 h-6 rounded-lg bg-purple-50 text-purple-600 text-xs flex items-center justify-center font-mono font-medium">1</span>
          <div>
            <h3 class="font-roboto font-medium text-base text-slate-800 leading-none">Dados pessoais</h3>
            <p class="text-slate-600 text-xs font-light mt-1">Nome e e-mail usados para identificação e login.</p>
          </div>
        </div>

        <div class="flex flex-col gap-5">
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-medium text-slate-700">Nome completo <span class="text-red-500">*</span></label>
            <input
              v-model="form.name"
              type="text"
              placeholder="Seu nome"
              required
              class="w-full bg-[#f4f4f2] border border-transparent rounded-xl px-4 py-3 text-sm outline-none focus:border-purple-500 focus:bg-white transition-all font-light text-slate-800"
              :class="fieldErrors.name ? 'border-red-400' : ''"
            />
            <span v-if="fieldErrors.name" class="text-[11px] text-red-500">{{ fieldErrors.name[0] }}</span>
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-medium text-slate-700">E-mail <span class="text-red-500">*</span></label>
            <input
              v-model="form.email"
              type="email"
              placeholder="nome@empresa.com"
              required
              class="w-full bg-[#f4f4f2] border border-transparent rounded-xl px-4 py-3 text-sm outline-none focus:border-purple-500 focus:bg-white transition-all font-light text-slate-800"
              :class="fieldErrors.email ? 'border-red-400' : ''"
            />
            <span v-if="fieldErrors.email" class="text-[11px] text-red-500">{{ fieldErrors.email[0] }}</span>
          </div>
        </div>
      </div>

      <!-- Bloco 2: Segurança -->
      <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
        <div class="flex items-start justify-between gap-3 mb-5">
          <div class="flex items-start gap-3">
            <span class="w-6 h-6 rounded-lg bg-purple-50 text-purple-600 text-xs flex items-center justify-center font-mono font-medium">2</span>
            <div>
              <h3 class="font-roboto font-medium text-base text-slate-800 leading-none">Segurança</h3>
              <p class="text-slate-600 text-xs font-light mt-1">Altere sua senha de acesso, se necessário.</p>
            </div>
          </div>
          <button
            type="button"
            @click="togglePasswordChange"
            class="text-xs font-medium text-purple-600 hover:underline whitespace-nowrap"
          >
            {{ wantsPasswordChange ? 'Cancelar' : 'Alterar senha' }}
          </button>
        </div>

        <div v-if="wantsPasswordChange" class="flex flex-col gap-5">
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-medium text-slate-700">Senha atual <span class="text-red-500">*</span></label>
            <input
              v-model="form.current_password"
              type="password"
              placeholder="Digite sua senha atual"
              required
              class="w-full bg-[#f4f4f2] border border-transparent rounded-xl px-4 py-3 text-sm outline-none focus:border-purple-500 focus:bg-white transition-all font-light text-slate-800"
              :class="fieldErrors.current_password ? 'border-red-400' : ''"
            />
            <span v-if="fieldErrors.current_password" class="text-[11px] text-red-500">{{ fieldErrors.current_password[0] }}</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-700">Nova senha <span class="text-red-500">*</span></label>
              <input
                v-model="form.password"
                type="password"
                placeholder="Mínimo de 8 caracteres"
                required
                minlength="8"
                class="w-full bg-[#f4f4f2] border border-transparent rounded-xl px-4 py-3 text-sm outline-none focus:border-purple-500 focus:bg-white transition-all font-light text-slate-800"
                :class="fieldErrors.password ? 'border-red-400' : ''"
              />
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-medium text-slate-700">Confirmar nova senha <span class="text-red-500">*</span></label>
              <input
                v-model="form.password_confirmation"
                type="password"
                placeholder="Repita a nova senha"
                required
                minlength="8"
                class="w-full bg-[#f4f4f2] border border-transparent rounded-xl px-4 py-3 text-sm outline-none focus:border-purple-500 focus:bg-white transition-all font-light text-slate-800"
              />
            </div>
          </div>
          <span v-if="fieldErrors.password" class="text-[11px] text-red-500 -mt-2">{{ fieldErrors.password[0] }}</span>
        </div>
      </div>

      <!-- Ações -->
      <div class="flex flex-col sm:flex-row justify-end items-center gap-3 border-t border-white/10 pt-5">
        <button type="button" @click="emit('navigate', 'dash')" class="bg-white/5 border border-white/10 hover:bg-white/10 text-white/80 text-xs px-6 py-3 rounded-xl transition-all font-light w-full sm:w-auto">
          Cancelar
        </button>

        <button type="submit" :disabled="isLoading" class="bg-gradient-to-r from-purple-600 to-orange-500 text-white font-normal text-xs px-7 py-3 rounded-xl shadow-md hover:opacity-95 transition-all flex items-center justify-center gap-2 select-none w-full sm:w-auto">
          <span v-if="isLoading" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
          <span>{{ isLoading ? 'Salvando...' : 'Salvar alterações' }}</span>
          <svg v-if="!isLoading" width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
        </button>
      </div>

    </form>
  </div>
</template>

<style scoped>
.font-roboto {
  font-family: 'Roboto', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-hash-smoothing: grayscale;
}
</style>