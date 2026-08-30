<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-12">
      <h1 class="text-3xl font-serif mb-8">Customer Service</h1>
      
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Contact Options -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-xl font-semibold mb-4">Contact Us</h2>
          <div class="space-y-4">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 bg-gold-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <div>
                <h3 class="font-medium">Phone</h3>
                <p class="text-gray-600">+977 1-4423456</p>
                <p class="text-gray-600">+977 9800000000</p>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 bg-gold-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <h3 class="font-medium">Email</h3>
                <p class="text-gray-600">info@watchstore.com.np</p>
                <p class="text-gray-600">support@watchstore.com.np</p>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 bg-gold-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h3 class="font-medium">Business Hours</h3>
                <p class="text-gray-600">Sunday - Friday: 10:00 AM - 8:00 PM</p>
                <p class="text-gray-600">Saturday: 10:00 AM - 6:00 PM</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-xl font-semibold mb-4">Quick Links</h2>
          <ul class="space-y-3">
            <li>
              <NuxtLink to="/faq" class="flex items-center justify-between text-gray-700 hover:text-gold-500 transition-colors">
                <span>Frequently Asked Questions</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/shipping" class="flex items-center justify-between text-gray-700 hover:text-gold-500 transition-colors">
                <span>Shipping Information</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/returns" class="flex items-center justify-between text-gray-700 hover:text-gold-500 transition-colors">
                <span>Returns & Exchanges</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/privacy-policy" class="flex items-center justify-between text-gray-700 hover:text-gold-500 transition-colors">
                <span>Privacy Policy</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/terms" class="flex items-center justify-between text-gray-700 hover:text-gold-500 transition-colors">
                <span>Terms & Conditions</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </NuxtLink>
            </li>
          </ul>
        </div>
      </div>

      <!-- Services -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
          <div class="w-16 h-16 bg-gold-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <h3 class="font-semibold mb-2">Authentic Products</h3>
          <p class="text-gray-600 text-sm">100% genuine watches with manufacturer warranty</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
          <div class="w-16 h-16 bg-gold-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="font-semibold mb-2">Fast Delivery</h3>
          <p class="text-gray-600 text-sm">Quick shipping across Nepal within 2-5 business days</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
          <div class="w-16 h-16 bg-gold-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="font-semibold mb-2">Expert Support</h3>
          <p class="text-gray-600 text-sm">Professional watchmakers for maintenance and repair</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// No additional script needed
</script>
