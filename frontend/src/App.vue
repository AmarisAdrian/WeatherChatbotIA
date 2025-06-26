<template>
  <div class="h-screen w-screen bg-gray-900 text-white flex">
    <!-- Sidebar historial -->
    <aside class="w-1/4 border-r border-gray-700 p-4 overflow-y-auto">
      <h2 class="text-xl font-semibold mb-4">Historial</h2>
      <div v-if="loadingHistory" class="text-center py-4">
      <span class="text-gray-400">Cargando historial...</span>
    </div>
     <div v-else-if="apiMessage" class="text-center py-4">
      <span class="text-gray-400">{{ apiMessage }}</span>
    </div>
      <ul v-else class="space-y-2">
      <li 
        v-for="(conv, i) in conversations" 
        :key="i"
        class="p-3 bg-gray-800 hover:bg-gray-700 rounded-lg cursor-pointer transition-colors"
        @click="selectConversation(conv)"
      >
        <div class="flex items-center">
          <ChatBubbleLeftRightIcon class="w-5 h-5 text-blue-400 mr-2" />
          <span class="truncate">{{ conv.title || `Conversación ${i+1}` }}</span>
        </div>
        <div class="text-xs text-gray-500 mt-1">
          {{ formatDate(conv.created_at) }}
        </div>
      </li>
    </ul>
    </aside>

    <!-- Ventana de chat -->
    <main class="flex-1 flex flex-col">
      <div class="flex-1 overflow-y-auto p-4 space-y-4">
       <div v-for="(msg, index) in messages" :key="index" 
         :class="['mb-4', msg.from === 'user' ? 'text-right' : 'text-left']">
      
        <!-- Mensaje principal -->
        <div :class="[
          'inline-block px-4 py-2 rounded-lg max-w-md',
          msg.from === 'user' ? 'bg-blue-600 text-white' : 
          msg.isError ? 'bg-red-500 text-white' : 'bg-gray-800 text-gray-200'
        ]">
          {{ msg.text }}
        </div>
        
        <!-- Datos meteorológicos -->
        <div v-if="msg.weather" class="mt-2 text-xs text-gray-400">
          <div v-if="msg.weather.city">
            <span class="font-semibold">Clima en {{ msg.weather.city }}:</span>
            <span> {{ msg.weather.temperature }}°C</span>
            <span v-if="msg.weather.precipitation">, Precipitación: {{ msg.weather.precipitation }}mm</span>
          </div>
        </div>
        
        <!-- Timestamp -->
        <div class="text-xs text-gray-500 mt-1">
          {{ formatDate(msg.timestamp) }}
        </div>
    </div>
      </div>
      <form @submit.prevent="sendMessage" class="flex p-4 border-t border-gray-700">
        <input
          v-model="newMessage"
          type="text"
          placeholder="Escribe tu mensaje..."
          class="flex-1 px-4 py-2 rounded-l-lg bg-gray-800 text-white border border-gray-600 focus:outline-none"
        />
        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-500 transition"
        >
          Enviar
        </button>
      </form>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted,computed } from 'vue'
import axios from 'axios'

  const formatDate = (timestamp) => {
    return new Date(timestamp).toLocaleTimeString([], {
      hour: '2-digit',
      minute: '2-digit'
    })
  }

const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

const state = ref({
  loading: false,
  response: null,
  error: null
})

// const conversations = ref([])
const messages = ref([])
const newMessage = ref('')
const loading = ref(false)
const currentUser = ref('usuario_demo') 
const weatherData = ref(null); 
// const apiMessage = ref(null)
const conversations = computed(() => state.value.response?.data?.data || [])
const apiMessage = computed(() => state.value.response?.data?.message || '')
const isEmpty = computed(() => state.value.response?.status === 200 && conversations.value.length === 0)
const isSuccess = computed(() => state.value.response?.status === 201)


onMounted(async () => {
  try {
    const response = await api.get('/chat', {
      params: { user_name: currentUser.value }
    })
   state.value = { 
      loading: false, 
      response, 
      error: null 
    }
  } catch (error) {
    console.error('Error cargando historial:', error)
  }
})


function loadConversationMessages(conversation) {
  messages.value = [
    { text: conversation.user_message, from: 'user' },
    { text: conversation.ai_response, from: 'ai' }
  ]
}


function selectConversation(conv) {
  loadConversationMessages(conv)
}


    async function sendMessage() {
    if (!newMessage.value.trim() || loading.value) return
    
    try {
      loading.value = true
      
      const userMessage = newMessage.value
      messages.value.push({ 
        text: userMessage, 
        from: 'user',
        timestamp: new Date().toISOString()
      })
      newMessage.value = ''
      
      const response = await api.post('/chat', {
        user_name: currentUser.value,
        message: userMessage
      })
      
      weatherData.value = response.data.weatherData || null
      
      messages.value.push({ 
        text: response.data.response, 
        from: 'ai',
        timestamp: new Date().toISOString(),
        weather: weatherData.value
      })
      
      await updateConversationHistory()
      
    } catch (error) {
      console.error('Error enviando mensaje:', error)
      let errorMessage = 'Lo siento, ocurrió un error al procesar tu mensaje.'
      
      if (error.response?.data?.error) {
        errorMessage = error.response.data.error
      }
      
      messages.value.push({ 
        text: errorMessage, 
        from: 'ai',
        isError: true,
        timestamp: new Date().toISOString()
      })
    } finally {
      loading.value = false
    }
  }
  async function updateConversationHistory() {
    try {
      const historyResponse = await api.get('/chat', {
        params: { user_name: currentUser.value }
      })
      state.value.response = historyResponse
    } catch (error) {
      console.error('Error actualizando historial:', error)
    }
  }
</script>
