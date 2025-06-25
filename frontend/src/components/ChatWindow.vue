<template>
  <div class="flex flex-col flex-1 bg-white">
    <div class="flex-1 p-4 overflow-y-auto">
      <MessageBubble v-for="(msg, index) in messages" :key="index" :text="msg.text" :from="msg.from" />
    </div>
    <form @submit.prevent="handleSend" class="p-4 border-t flex">
      <input v-model="input" class="flex-1 border rounded px-4 py-2 mr-2" placeholder="Escribe tu mensaje..." />
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Enviar</button>
    </form>
  </div>
</template>

<script setup>
import MessageBubble from './MessageBubble.vue'
import { ref } from 'vue'

defineProps(['messages'])
const emit = defineEmits(['send'])

const input = ref('')
function handleSend() {
  emit('send', input.value)
  input.value = ''
}
</script>
