<script setup>
import { ref, onMounted } from 'vue'
import api from './services/api'
import Login from './components/Login.vue'
import Register from './components/Register.vue'
import ForgotPassword from './components/ForgotPassword.vue'
import Dashboard from './components/Dashboard.vue'
import EventDetails from './components/EventDetails.vue'
import EventForm from './components/EventForm.vue'

const currentScreen = ref('loading')
const selectedEventId = ref(null)

function navigateTo(screen, id = null) {
  selectedEventId.value = id
  currentScreen.value = screen
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(async () => {
  try {
    await api.get('/user')
    currentScreen.value = 'dash'
  } catch (error) {
    currentScreen.value = 'login'
  }
})
</script>

<template>
  <main 
    class="min-h-screen w-full flex justify-center p-4 transition-all duration-300"
    :class="['login', 'register', 'forgot'].includes(currentScreen) ? 'items-center py-4' : 'items-start pt-12'"
  >
    <div v-if="currentScreen === 'loading'" class="flex items-center justify-center text-text-secondary">
      Carregando sistema...
    </div>

    <transition v-else name="fade" mode="out-in">
      <component 
        :is="currentScreen === 'login' ? Login : 
             currentScreen === 'register' ? Register : 
             currentScreen === 'forgot' ? ForgotPassword :
             currentScreen === 'dash' ? Dashboard : 
             currentScreen === 'detail' ? EventDetails : EventForm"
        :eventId="selectedEventId"
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