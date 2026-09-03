<template>
  <Swiper
    :modules="[SwiperAutoplay]"
    :slides-per-view="'auto'"
    :space-between="32"
    :autoplay="{ delay: 3000, disableOnInteraction: true }"
    :loop="true"
    class="brand-carousel"
  >
    <SwiperSlide v-for="brand in brands" :key="brand.id" class="!w-[180px]">
      <NuxtLink :to="`/shop?brand=${brand.id}`" class="block">
        <div class="h-24 bg-white rounded-2xl shadow-premium flex items-center justify-center p-6 hover:shadow-premium-lg transition-all duration-300 group">
          <img
            :src="brand.logo || 'https://picsum.photos/seed/brand/200/100'"
            :alt="brand.name"
            class="max-h-full max-w-full object-contain grayscale group-hover:grayscale-0 transition-all duration-300"
          />
        </div>
        <p class="text-center text-sm font-medium mt-3 text-gray-600">{{ brand.name }}</p>
      </NuxtLink>
    </SwiperSlide>
  </Swiper>
</template>

<script setup lang="ts">
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay } from 'swiper/modules'
import 'swiper/css'
import type { Brand } from '~/types'

const SwiperAutoplay = Autoplay

defineProps<{
  brands: Brand[]
}>()
</script>