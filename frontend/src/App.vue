<script setup>
import { ref, onMounted } from 'vue'
import api, { initializeCSRF } from './services/api'
import Login from './components/Login.vue'
import Register from './components/Register.vue'
import ForgotPassword from './components/ForgotPassword.vue'
import Dashboard from './components/Dashboard.vue'
import EventDetails from './components/EventDetails.vue'
import EventForm from './components/EventForm.vue'
import Profile from './components/Profile.vue'
import ResetPassword from './components/ResetPassword.vue'

const currentScreen = ref('loading')
const selectedEventId = ref(null)
const resetToken = ref('')
const resetEmail = ref('')

function navigateTo(screen, id = null) {
  selectedEventId.value = id
  currentScreen.value = screen
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(async () => {
  // Link de recuperação de senha: /?token=xxx&email=xxx
  const params = new URLSearchParams(window.location.search)
  const tokenParam = params.get('token')
  if (tokenParam) {
    resetToken.value = tokenParam
    resetEmail.value = params.get('email') || ''
    currentScreen.value = 'reset-password'
    return
  }

  try{
    await initializeCSRF() // Obtendo o cookie CSRF
    await api.get('/user') // Verificando se o usuário está autenticado
    currentScreen.value = 'dash'
  }
  catch(error){
    currentScreen.value = 'login'
  } 
  /*const token = localStorage.getItem('token');
  if (token) {
    try {
      await api.get('/user');
      currentScreen.value = 'dash';
    } catch (error) {
      localStorage.removeItem('token');
      currentScreen.value = 'login';
    }
  } else {
    currentScreen.value = 'login';
  }*/
});
</script>

<template>
  <main 
    class="min-h-screen w-full flex justify-center p-4 transition-all duration-300"
    :class="['login', 'register', 'forgot', 'reset-password'].includes(currentScreen) ? 'items-center py-4' : 'items-start pt-12'"
  >
    <div v-if="currentScreen === 'loading'" class="flex items-center justify-center text-text-secondary">
      Carregando sistema...
    </div>

    <transition v-else name="fade" mode="out-in">
      <component 
        :is="currentScreen === 'login' ? Login : 
             currentScreen === 'register' ? Register : 
             currentScreen === 'forgot' ? ForgotPassword :
             currentScreen === 'reset-password' ? ResetPassword :
             currentScreen === 'profile' ? Profile:
             currentScreen === 'dash' ? Dashboard : 
             currentScreen === 'detail' ? EventDetails : EventForm"
        :eventId="selectedEventId"
        :token="resetToken"
        :resetEmail="resetEmail"
        @navigate="navigateTo"
        @view-event="(id) => navigateTo('detail', id)"
      />
    </transition>
  </main>
</template>

<style>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>