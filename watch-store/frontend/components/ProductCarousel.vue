<template>
  <div class="relative">
    <Swiper
      :modules="[SwiperAutoplay, SwiperNavigation, SwiperPagination]"
      :slides-per-view="'auto'"
      :space-between="24"
      :navigation="true"
      :pagination="{ clickable: true }"
      :autoplay="{ delay: 4000, disableOnInteraction: true, pauseOnMouseEnter: true }"
      :loop="products.length > 4"
      :breakpoints="{
        320: { slidesPerView: 1.2, spaceBetween: 16 },
        375: { slidesPerView: 1.5, spaceBetween: 16 },
        640: { slidesPerView: 2, spaceBetween: 20 },
        768: { slidesPerView: 3, spaceBetween: 24 },
        1024: { slidesPerView: 4, spaceBetween: 24 },
        1280: { slidesPerView: 5, spaceBetween: 24 },
      }"
      class="product-carousel"
    >
      <SwiperSlide v-for="product in products" :key="product.id" class="!w-auto">
        <ProductCard :product="product" />
      </SwiperSlide>
    </Swiper>
  </div>
</template>

<script setup lang="ts">
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, Navigation, Pagination } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import type { Product } from '~/types'

const SwiperAutoplay = Autoplay
const SwiperNavigation = Navigation
const SwiperPagination = Pagination

defineProps<{
  products: Product[]
}>()
</script>

<style scoped>
.product-carousel :deep(.swiper-button-next),
.product-carousel :deep(.swiper-button-prev) {
  @apply w-10 h-10 bg-white shadow-premium rounded-full;
}
.product-carousel :deep(.swiper-button-next::after),
.product-carousel :deep(.swiper-button-prev::after) {
  @apply text-sm;
}
.product-carousel :deep(.swiper-pagination) {
  @apply relative mt-6;
}
</style>