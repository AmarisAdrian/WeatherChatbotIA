<template>
  <div :class="from === 'user' ? 'text-right' : 'text-left'" class="mb-2">
    <span :class="from === 'user' ? 'bg-blue-200' : 'bg-gray-300'" class="inline-block px-4 py-2 rounded">
      {{ text }}
    </span>
  </div>
</template>

<script setup>
defineProps(['text', 'from'])
</script>
