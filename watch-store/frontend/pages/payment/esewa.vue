<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center py-16 px-4">
    <div class="bg-white rounded-lg shadow-sm p-8 max-w-md w-full text-center">
      <div v-if="loading" class="py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold-500 mx-auto mb-4"></div>
        <p class="text-gray-600">Verifying your payment...</p>
      </div>

      <div v-else-if="verified">
        <div class="text-6xl mb-4">✓</div>
        <h1 class="text-2xl font-semibold mb-2">Payment Successful</h1>
        <p class="text-gray-600 mb-6">Your eSewa payment was verified and your order has been confirmed.</p>
        <NuxtLink to="/account" class="inline-block bg-luxury-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition-colors">
          View My Orders
        </NuxtLink>
      </div>

      <div v-else>
        <div class="text-6xl mb-4">✕</div>
        <h1 class="text-2xl font-semibold mb-2">Payment Failed</h1>
        <p class="text-gray-600 mb-6">{{ errorMessage }}</p>
        <NuxtLink to="/checkout" class="inline-block bg-luxury-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition-colors">
          Back to Checkout
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const route = useRoute()
const api = useApi()
const cartStore = useCartStore()

const loading = ref(true)
const verified = ref(false)
const errorMessage = ref('')

onMounted(async () => {
  const status = route.query.status

  if (status === 'success') {
    try {
      await api.post('/v1/payments/esewa/verify', {
        pid: typeof route.query.oid === 'string' ? route.query.oid : route.query.pid,
        total: route.query.total || route.query.amt || 0,
        refId: route.query.refId || route.query.rid || '',
      })
      cartStore.clearCart()
      verified.value = true
    } catch (e: any) {
      errorMessage.value = e?.message || 'We could not verify your payment. Please contact support.'
    }
  } else {
    errorMessage.value = 'Your payment was not completed. Please try again.'
  }

  loading.value = false
})
</script>