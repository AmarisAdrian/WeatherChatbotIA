<template>
  <aside class="w-1/4 bg-gray-100 p-4 overflow-y-auto">
    <h2 class="text-lg font-bold mb-4">Historial</h2>
    <ul>
      <li v-for="(conv, index) in conversations" :key="index" @click="$emit('select', conv)" class="cursor-pointer hover:bg-gray-200 p-2 rounded">
        {{ conv.user_name }}
      </li>
    </ul>
  </aside>
</template>

<script setup>
defineProps(['conversations'])
</script>
