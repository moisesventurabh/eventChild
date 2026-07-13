<script setup>
import { ref } from 'vue'
import Login from './components/Login.vue'
import Register from './components/Register.vue'
import ForgotPassword from './components/ForgotPassword.vue'
import Dashboard from './components/Dashboard.vue'
import EventDetails from './components/EventDetails.vue'
import EventForm from './components/EventForm.vue'

const currentScreen = ref('login')
const selectedEventId = ref(null)

function navigateTo(screen, id = null) {
  selectedEventId.value = id
  currentScreen.value = screen
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<template>
  <main class="min-h-screen w-full flex items-center justify-center p-4">
    <transition name="fade" mode="out-in">
      <component 
        :is="currentScreen === 'login' ? Login : 
             currentScreen === 'register' ? Register : 
             currentScreen === 'forgot' ? ForgotPassword :
             currentScreen === 'dash' ? Dashboard : 
             currentScreen === 'detail' ? EventDetails : EventForm"
        :eventId="selectedEventId"
        @navigate="navigateTo"
      />
    </transition>
  </main>
</template>

<style>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>