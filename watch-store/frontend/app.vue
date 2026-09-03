<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'
import { useCartStore } from '~/stores/cart'
import { useWishlistStore } from '~/stores/wishlist'

const authStore = useAuthStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

onMounted(() => {
  authStore.loadFromStorage()
  cartStore.setSessionId()
  if (authStore.isAuthenticated) {
    wishlistStore.load()
  }
})

useHead({
  titleTemplate: '%s | WatchStore Nepal',
  htmlAttrs: {
    lang: 'en',
  },
})
</script>