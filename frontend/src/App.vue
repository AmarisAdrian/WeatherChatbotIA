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

      <ul v-if="conversations.length > 0" class="space-y-2">
        <li
          v-for="(conv, i) in selectedConversation ? conversations : conversations.slice(-1)"
          :key="conv.id || i"
          class="p-3 bg-gray-800 hover:bg-gray-700 rounded-lg cursor-pointer transition-colors"
          @click="selectConversation(conv)"
        >
          <div class="flex items-center">
            <ChatBubbleLeftRightIcon class="w-5 h-5 text-blue-400 mr-2" />
            <span class="truncate">
              {{ truncateText(conv.user_message || 'Conversación', 40) }}
            </span>
          </div>
          <div class="text-xs text-gray-500 mt-1">
            {{ formatDate(conv.created_at) }}
          </div>
        </li>
      </ul>
    </aside>

    <!-- Ventana de chat -->
    <main class="flex-1 flex flex-col">
      <!-- Botón volver -->
      <div v-if="selectedConversation" class="px-4 pt-4">
        <button @click="resetConversation" class="text-sm text-blue-400 hover:underline">
          ← Volver al chat principal
        </button>
      </div>

      <!-- Mensajes -->
      <div class="flex-1 overflow-y-auto p-4 space-y-4">
        <div
          v-for="(msg, index) in messages"
          :key="index"
          :class="['mb-4', msg.from === 'user' ? 'text-right' : 'text-left']"
        >
          <div
            :class="[
              'inline-block px-4 py-2 rounded-lg max-w-md',
              msg.from === 'user'
                ? 'bg-blue-600 text-white'
                : msg.isError
                ? 'bg-red-500 text-white'
                : 'bg-gray-800 text-gray-200',
            ]"
          >
            {{ msg.text }}
          </div>
          <div class="text-xs text-gray-500 mt-1">
            {{ formatDate(msg.timestamp) }}
          </div>
        </div>
      </div>

      <!-- Input -->
      <form @submit.prevent="sendMessage" class="flex p-4 border-t border-gray-700">
        <input
          v-model="newMessage"
          :disabled="!currentUser"
          type="text"
          placeholder="Escribe tu mensaje..."
          class="flex-1 px-4 py-2 rounded-l-lg bg-gray-800 text-white border border-gray-600 focus:outline-none disabled:opacity-50"
        />
        <button
          type="submit"
          :disabled="!currentUser"
          class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-500 transition disabled:opacity-50"
        >
          Enviar
        </button>
      </form>
    </main>
  </div>
  <div v-if="!currentUser" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-70 z-50">
  <div class="bg-gray-800 p-6 rounded-lg text-white w-80">
    <h2 class="text-lg font-semibold mb-4">Ingresa tu nombre</h2>
    <input
      v-model="tempUser"
      type="text"
      class="w-full px-3 py-2 rounded bg-gray-700 text-white focus:outline-none"
      placeholder="Nombre de usuario"
    />
    <button
      @click="saveUser"
      class="mt-4 w-full bg-blue-600 hover:bg-blue-500 py-2 rounded transition"
    >
      Guardar
    </button>
  </div>
</div>

</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const newMessage = ref('')
const loading = ref(false)
const messages = ref([])
const conversations = ref([])
const selectedConversation = ref(null)
const weatherData = ref(null)
const currentUser = ref('')
const apiMessage = ref('')
const loadingHistory = ref(false)
const tempUser = ref('')


const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

const formatDate = (timestamp) => {
  return new Date(timestamp).toLocaleTimeString([], {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const truncateText = (text, length) => {
  return text.length > length ? text.slice(0, length) + '...' : text
}

const loadConversationMessages = (conversation) => {
  messages.value = [
    {
      text: conversation.user_message,
      from: 'user',
      timestamp: conversation.created_at,
    },
    {
      text: conversation.ai_response,
      from: 'ai',
      timestamp: conversation.created_at,
      weather: tryParseWeather(conversation.api_response),
    },
  ]
}

const selectConversation = (conv) => {
  selectedConversation.value = conv
  loadConversationMessages(conv)
}

const resetConversation = () => {
  selectedConversation.value = null
  loadHistory()
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || loading.value) return

  try {
    loading.value = true

    const userMessage = newMessage.value
    messages.value.push({
      text: userMessage,
      from: 'user',
      timestamp: new Date().toISOString(),
    })

    newMessage.value = ''

    const response = await api.post('/chat', {
      user_name: currentUser.value,
      message: userMessage,
    })

    weatherData.value = response.data.weatherData || null

    messages.value.push({
      text: response.data.response,
      from: 'ai',
      timestamp: new Date().toISOString(),
      weather: weatherData.value,
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
      timestamp: new Date().toISOString(),
    })
  } finally {
    loading.value = false
  }
}

const updateConversationHistory = async () => {
  try {
    const response = await api.get('/chat', {
      params: { user_name: currentUser.value },
    })
    conversations.value = response.data.conversations || []
  } catch (error) {
    console.error('Error actualizando historial:', error)
  }
}

const loadHistory = async () => {
  try {
    loadingHistory.value = true
    const response = await api.get('/chat', {
      params: { user_name: currentUser.value },
    })

    conversations.value = response.data.conversations || []

    const mergedMessages = []
    for (const conv of conversations.value) {
      mergedMessages.push({
        text: conv.user_message,
        from: 'user',
        timestamp: conv.created_at,
      })
      mergedMessages.push({
        text: conv.ai_response,
        from: 'ai',
        timestamp: conv.created_at,
        weather: tryParseWeather(conv.api_response),
      })
    }

    messages.value = mergedMessages
    apiMessage.value = ''
  } catch (error) {
    console.error('Error cargando historial:', error)
    apiMessage.value = 'Error al cargar el historial'
  } finally {
    loadingHistory.value = false
  }
}

const tryParseWeather = (data) => {
  try {
    const parsed = JSON.parse(data)
    return typeof parsed === 'object' ? parsed : null
  } catch {
    return null
  }
}

async function fetchUser() {
  try {
    const response = await api.get('/user')
    if (response.data.user_name) {
      currentUser.value = response.data.user_name
    }
  } catch (error) {
    console.error('Error obteniendo usuario desde Redis:', error)
  }
}

async function saveUser() {
  if (!tempUser.value.trim()) return
  try {
    await api.post('/user', { user_name: tempUser.value })
    currentUser.value = tempUser.value
    await loadHistory()
  } catch (err) {
    console.error('Error guardando usuario:', err)
  }
}

onMounted(async () => {
  await fetchUser()
  if (currentUser.value) {
    await loadHistory()
  }
})

</script>
