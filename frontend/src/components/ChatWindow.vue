<template>
  <div class="flex flex-col h-full">
    <div class="flex-1 p-4 overflow-y-auto space-y-4">
      <MessageBubble 
        v-for="(msg, index) in state.messages" 
        :key="index" 
        :text="msg.text" 
        :from="msg.from" 
      />
    </div>
    <form @submit.prevent="handleSend" class="p-4 border-t flex">
      <input 
        v-model="input" 
        class="flex-1 border rounded-lg px-4 py-2 mr-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
        placeholder="Escribe tu mensaje..." 
      />
      <button 
        type="submit" 
        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors"
      >
        Enviar
      </button>
    </form>
  </div>
</template>