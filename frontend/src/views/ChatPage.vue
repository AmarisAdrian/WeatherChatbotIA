<template>
  <div class="flex h-screen">
    <Sidebar :conversations="conversations" @select="selectConversation" />
    <ChatWindow :messages="messages" @send="sendMessage" />
  </div>
</template>

<script setup>
import Sidebar from '@/components/Sidebar.vue'
import ChatWindow from '@/components/ChatWindow.vue'
import { ref } from 'vue'

const conversations = ref([])
const messages = ref([])

function selectConversation(history) {
  messages.value = history.messages
}

function sendMessage(message) {
  // Lógica para enviar mensaje a Laravel y actualizar `messages`
  // Usa fetch o axios
}
</script>
