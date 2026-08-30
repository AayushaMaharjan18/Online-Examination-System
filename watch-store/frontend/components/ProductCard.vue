<template>
  <div class="group w-[280px] sm:w-[300px] relative">
    <NuxtLink :to="`/product/${product.slug}`" class="block card-premium overflow-hidden">
      <!-- Image -->
      <div class="relative aspect-square overflow-hidden bg-gray-50">
        <img
          :src="product.thumbnail || product.images?.[0] || 'https://picsum.photos/seed/watch/400/400'"
          :alt="product.name"
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
          loading="lazy"
        />
        <!-- Badges -->
        <div class="absolute top-3 left-3 flex flex-col gap-2">
          <span v-if="product.discount_percentage > 0" class="bg-red-500 text-white text-xs font-medium px-2.5 py-1 rounded-full">
            -{{ product.discount_percentage }}%
          </span>
          <span v-if="product.is_new" class="bg-gold-500 text-white text-xs font-medium px-2.5 py-1 rounded-full">
            New
          </span>
          <span v-if="product.is_limited_edition" class="bg-luxury-black text-white text-xs font-medium px-2.5 py-1 rounded-full">
            Limited
          </span>
        </div>
      </div>
      <!-- Info -->
      <div class="p-4">
        <p v-if="product.brand" class="text-xs text-gray-400 uppercase tracking-wider mb-1">{{ product.brand.name }}</p>
        <h3 class="font-medium text-sm mb-2 line-clamp-1 group-hover:text-gold-500 transition-colors">{{ product.name }}</h3>
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-lg font-semibold">Rs. {{ formatPrice(product.final_price) }}</span>
            <span v-if="product.compare_price" class="text-sm text-gray-400 line-through">Rs. {{ formatPrice(product.compare_price) }}</span>
          </div>
          <div class="flex items-center gap-1">
            <svg v-for="i in 5" :key="i" class="w-3 h-3" :class="i <= Math.round(product.average_rating) ? 'text-gold-500' : 'text-gray-200'" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
          </div>
        </div>
      </div>
    </NuxtLink>

    <!-- Quick Actions -->
    <div class="absolute top-3 right-3 flex flex-col gap-2">
      <button @click.stop.prevent="toggleWishlist" :class="wishlistStore.isInWishlist(product.id) ? 'text-red-500' : 'text-gray-400 hover:text-red-500'" class="w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full shadow-premium flex items-center justify-center transition-colors">
        <svg class="w-4 h-4" :fill="wishlistStore.isInWishlist(product.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
      </button>
    </div>

    <!-- Add to Cart Overlay -->
    <div class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 group-hover:pointer-events-auto pointer-events-none transition-opacity duration-300 translate-y-2 group-hover:translate-y-0">
      <button @click.stop.prevent="addToCart" class="w-full bg-white text-luxury-black font-medium py-2.5 rounded-lg hover:bg-gold-500 hover:text-white transition-all duration-300 text-sm">
        Add to Cart
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Product } from '~/types'
import { useCartStore } from '~/stores/cart'
import { useAuthStore } from '~/stores/auth'
import { useWishlistStore } from '~/stores/wishlist'

const props = defineProps<{
  product: Product
}>()

const cartStore = useCartStore()
const authStore = useAuthStore()
const wishlistStore = useWishlistStore()

function formatPrice(price: number): string {
  return price.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

function addToCart() {
  if (!authStore.isAuthenticated) {
    navigateTo('/auth/login?redirect=/shop')
    return
  }
  cartStore.addItem(props.product, 1)
}

async function toggleWishlist() {
  if (!authStore.isAuthenticated) {
    navigateTo('/auth/login')
    return
  }
  try {
    await wishlistStore.toggle(props.product)
  } catch (error) {
    console.error('Failed to toggle wishlist:', error)
  }
}
</script>