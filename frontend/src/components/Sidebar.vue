<template>
  <aside class="w-64 bg-gray-100 p-4 overflow-y-auto border-r">
    <h2 class="text-lg font-bold mb-4">Historial</h2>
    <ul class="space-y-2">
      <li 
        v-for="(conv, index) in conversations" 
        :key="index" 
        @click="$emit('select', conv)" 
        class="cursor-pointer hover:bg-gray-200 p-2 rounded transition-colors"
      >
        {{ conv.user_name }}
      </li>
    </ul>
  </aside>
</template>