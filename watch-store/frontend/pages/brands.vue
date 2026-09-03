<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold-500"></div>
      </div>

      <div v-else-if="brands.length === 0" class="text-center py-12 text-gray-500">
        No brands available
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="brand in brands"
          :key="brand.id"
          class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-lg transition-shadow"
        >
          <div class="aspect-square bg-gray-100 flex items-center justify-center p-8">
            <img
              :src="brand.logo"
              :alt="brand.name"
              class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300"
            />
          </div>
          <div class="p-6 text-center">
            <h3 class="text-xl font-semibold mb-2">{{ brand.name }}</h3>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ brand.description }}</p>
            <div class="flex items-center justify-between">
              <span class="text-gray-500 text-sm">{{ brand.products_count }} products</span>
              <NuxtLink
                :to="`/shop?brand=${brand.id}`"
                class="text-gold-500 hover:text-gold-600 font-medium text-sm"
              >
                View Products →
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { Brand } from '~/types'

const loading = ref(false)
const brands = ref<Brand[]>([
  {
    id: 1,
    name: 'Rolex',
    slug: 'rolex',
    description: 'The world\'s most recognized watch brand, known for precision, durability, and timeless design.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Rolex_logo.svg/200px-Rolex_logo.svg.png',
    is_featured: true,
    products_count: 45
  },
  {
    id: 2,
    name: 'Omega',
    slug: 'omega',
    description: 'Swiss luxury watchmaker famous for the Speedmaster Moonwatch and Seamaster diving watches.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Omega_Logo.svg/200px-Omega_Logo.svg.png',
    is_featured: true,
    products_count: 38
  },
  {
    id: 3,
    name: 'Tag Heuer',
    slug: 'tag-heuer',
    description: 'Renowned for its sports watches and chronographs, combining precision with style.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/42/Tag_Heuer_Logo.svg/200px-Tag_Heuer_Logo.svg.png',
    is_featured: true,
    products_count: 32
  },
  {
    id: 4,
    name: 'Seiko',
    slug: 'seiko',
    description: 'Japanese watchmaker known for innovation, from affordable quartz to high-end mechanical watches.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Seiko_logo.svg/200px-Seiko_logo.svg.png',
    is_featured: true,
    products_count: 56
  },
  {
    id: 5,
    name: 'Citizen',
    slug: 'citizen',
    description: 'Pioneer in eco-friendly watch technology with their innovative Eco-Drive solar-powered watches.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Citizen_Watch_logo.svg/200px-Citizen_Watch_logo.svg.png',
    is_featured: false,
    products_count: 42
  },
  {
    id: 6,
    name: 'Casio',
    slug: 'casio',
    description: 'Famous for G-Shock watches and affordable digital timepieces with advanced features.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Casio_logo.svg/200px-Casio_logo.svg.png',
    is_featured: false,
    products_count: 67
  },
  {
    id: 7,
    name: 'Fossil',
    slug: 'fossil',
    description: 'American lifestyle brand offering stylish and affordable watches for everyday wear.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5a/Fossil_Logo.svg/200px-Fossil_Logo.svg.png',
    is_featured: false,
    products_count: 51
  },
  {
    id: 8,
    name: 'Tissot',
    slug: 'tissot',
    description: 'Swiss watchmaker offering quality timepieces at accessible price points.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Tissot_Logo.svg/200px-Tissot_Logo.svg.png',
    is_featured: true,
    products_count: 39
  },
  {
    id: 9,
    name: 'Nepal Time',
    slug: 'nepal-time',
    description: 'Proudly Nepali watch brand offering traditional and modern timepieces inspired by Himalayan heritage.',
    logo: 'https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=200&h=200&fit=crop',
    is_featured: true,
    products_count: 15
  },
  {
    id: 10,
    name: 'Kathmandu Watches',
    slug: 'kathmandu-watches',
    description: 'Local Nepali brand crafting elegant watches that blend traditional craftsmanship with modern design.',
    logo: 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=200&h=200&fit=crop',
    is_featured: true,
    products_count: 12
  },
  {
    id: 11,
    name: 'Himalayan Horology',
    slug: 'himlayan-horology',
    description: 'Premium Nepali watchmaker specializing in limited edition timepieces inspired by mountain landscapes.',
    logo: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=200&h=200&fit=crop',
    is_featured: false,
    products_count: 8
  },
  {
    id: 12,
    name: 'Everest Timepieces',
    slug: 'everest-timepieces',
    description: 'Nepali luxury watch brand named after the world\'s highest peak, representing excellence and precision.',
    logo: 'https://images.unsplash.com/photo-1548171915-e79a380a2a4b?w=200&h=200&fit=crop',
    is_featured: false,
    products_count: 6
  }
])
</script>
