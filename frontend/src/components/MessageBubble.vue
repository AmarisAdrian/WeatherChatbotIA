<template>
  <form @submit.prevent="handleSend" class="p-4 border-t border-gray-200 bg-white flex items-center">
    <input 
      v-model="input"
      class="flex-1 px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all"
      placeholder="Escribe tu mensaje...."
    />
    <button
      type="submit"
      class="ml-3 bg-blue-500 hover:bg-blue-600 text-white rounded-full p-3 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>
    </button>
  </form>
</template>

<script setup>
const input = ref('')
const emit = defineEmits(['send'])

function handleSend() {
  if (input.value.trim()) {
    emit('send', input.value)
    input.value = ''
  }
}
</script>