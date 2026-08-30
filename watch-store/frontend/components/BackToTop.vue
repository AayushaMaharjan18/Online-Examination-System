<template>
  <Transition name="fade">
    <button
      v-if="visible"
      @click="scrollToTop"
      class="fixed bottom-20 lg:bottom-8 right-4 z-40 w-12 h-12 bg-gold-500 text-white rounded-full shadow-gold flex items-center justify-center hover:bg-gold-600 transition-all duration-300"
      aria-label="Back to top"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
      </svg>
    </button>
  </Transition>
</template>

<script setup lang="ts">
import { ref, inject, watch } from 'vue'

const visible = ref(false)
const scrollY = inject('scrollY', ref(0))

watch(scrollY, (y: number) => {
  visible.value = y > 400
})

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>