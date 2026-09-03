<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <aside class="lg:w-64 flex-shrink-0">
          <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-lg font-semibold">Filters</h2>
              <button @click="clearFilters" class="text-sm text-gold-500 hover:text-gold-600">Clear All</button>
            </div>

            <!-- Search -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search products..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent"
              />
            </div>

            <!-- Gender -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
              <select
                v-model="filters.gender"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent"
              >
                <option value="">All</option>
                <option value="men">Men</option>
                <option value="women">Women</option>
                <option value="unisex">Unisex</option>
              </select>
            </div>

            <!-- Price Range -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
              <div class="flex gap-2">
                <input
                  v-model="filters.min_price"
                  type="number"
                  placeholder="Min"
                  class="w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent"
                />
                <input
                  v-model="filters.max_price"
                  type="number"
                  placeholder="Max"
                  class="w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent"
                />
              </div>
            </div>

            <!-- Sort -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
              <select
                v-model="filters.sort"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent"
              >
                <option value="newest">Newest</option>
                <option value="popular">Popular</option>
                <option value="best_selling">Best Selling</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
              </select>
            </div>

            <button
              @click="applyFilters"
              class="w-full bg-luxury-black text-white py-2 rounded-lg hover:bg-gray-800 transition-colors"
            >
              Apply Filters
            </button>
          </div>
        </aside>

        <!-- Products Grid -->
        <main class="flex-1">
          <div class="flex items-center justify-between mb-6">
            <p class="text-gray-600">{{ pagination.total }} products found</p>
            <select
              v-model="filters.per_page"
              @change="applyFilters"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent"
            >
              <option :value="12">12 per page</option>
              <option :value="24">24 per page</option>
              <option :value="48">48 per page</option>
            </select>
          </div>

          <div v-if="loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold-500"></div>
          </div>

          <div v-else-if="products.length === 0" class="text-center py-12">
            <p class="text-gray-500">No products found</p>
          </div>

          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <ProductCard
              v-for="product in products"
              :key="product.id"
              :product="product"
            />
          </div>

          <!-- Pagination -->
          <div v-if="pagination.last_page > 1" class="flex justify-center mt-8 gap-2">
            <button
              v-for="page in pagination.last_page"
              :key="page"
              @click="goToPage(page)"
              :class="[
                'px-4 py-2 rounded-lg',
                pagination.current_page === page
                  ? 'bg-luxury-black text-white'
                  : 'bg-white text-gray-700 hover:bg-gray-100'
              ]"
            >
              {{ page }}
            </button>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { Product, ProductFilters, PaginatedResponse } from '~/types'
import ProductCard from '~/components/ProductCard.vue'
import { useAuthStore } from '~/stores/auth'
import { useWishlistStore } from '~/stores/wishlist'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const wishlistStore = useWishlistStore()

const products = ref<Product[]>([])
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
})

const filters = ref<ProductFilters>({
  search: '',
  gender: '',
  min_price: undefined,
  max_price: undefined,
  sort: 'newest',
  per_page: 12,
  page: 1
})

const sampleProducts: Product[] = [
  {
    id: 1,
    name: 'Rolex Submariner Date',
    slug: 'rolex-submariner-date',
    description: 'The iconic diver\'s watch with Cerachrom bezel and Chromalight display.',
    short_description: 'Iconic diving watch with 41mm case and automatic movement.',
    sku: 'RLX-126610LN',
    price: 450000,
    compare_price: 480000,
    final_price: 450000,
    discount_percentage: 6,
    stock_quantity: 5,
    in_stock: true,
    is_featured: true,
    is_new: false,
    is_best_seller: true,
    is_limited_edition: false,
    status: 'active',
    gender: 'men',
    movement: 'Automatic',
    strap: 'Oystersteel',
    case_material: 'Oystersteel',
    case_diameter: '41mm',
    case_thickness: '12.5mm',
    water_resistance: '300m',
    dial_color: 'Black',
    glass_type: 'Sapphire Crystal',
    warranty_period: '5 years',
    average_rating: 4.8,
    reviews_count: 124,
    brand: { id: 1, name: 'Rolex', slug: 'rolex', description: '', logo: '', is_featured: true, products_count: 45 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-01',
    updated_at: '2024-01-01'
  },
  {
    id: 2,
    name: 'Omega Speedmaster Professional',
    slug: 'omega-speedmaster-professional',
    description: 'The legendary Moonwatch that has been part of all six moon landings.',
    short_description: 'Iconic chronograph with Hesalite crystal and manual movement.',
    sku: 'OMG-31130423001006',
    price: 380000,
    compare_price: null,
    final_price: 380000,
    discount_percentage: 0,
    stock_quantity: 8,
    in_stock: true,
    is_featured: true,
    is_new: false,
    is_best_seller: true,
    is_limited_edition: false,
    status: 'active',
    gender: 'men',
    movement: 'Manual',
    strap: 'Leather',
    case_material: 'Stainless Steel',
    case_diameter: '42mm',
    case_thickness: '13.8mm',
    water_resistance: '50m',
    dial_color: 'Black',
    glass_type: 'Hesalite Crystal',
    warranty_period: '3 years',
    average_rating: 4.9,
    reviews_count: 89,
    brand: { id: 2, name: 'Omega', slug: 'omega', description: '', logo: '', is_featured: true, products_count: 38 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-02',
    updated_at: '2024-01-02'
  },
  {
    id: 3,
    name: 'Nepal Time Himalayan Classic',
    slug: 'nepal-time-himalayan-classic',
    description: 'Proudly Nepali timepiece inspired by the majestic Himalayas.',
    short_description: 'Handcrafted watch with traditional Nepali motifs and Swiss movement.',
    sku: 'NT-HC-001',
    price: 25000,
    compare_price: 30000,
    final_price: 25000,
    discount_percentage: 17,
    stock_quantity: 20,
    in_stock: true,
    is_featured: true,
    is_new: true,
    is_best_seller: false,
    is_limited_edition: false,
    status: 'active',
    gender: 'unisex',
    movement: 'Automatic',
    strap: 'Leather',
    case_material: 'Stainless Steel',
    case_diameter: '40mm',
    case_thickness: '10mm',
    water_resistance: '50m',
    dial_color: 'White',
    glass_type: 'Sapphire Crystal',
    warranty_period: '2 years',
    average_rating: 4.5,
    reviews_count: 23,
    brand: { id: 9, name: 'Nepal Time', slug: 'nepal-time', description: '', logo: '', is_featured: true, products_count: 15 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-10',
    updated_at: '2024-01-10'
  },
  {
    id: 4,
    name: 'Kathmandu Heritage Edition',
    slug: 'kathmandu-heritage-edition',
    description: 'Elegant timepiece celebrating Kathmandu\'s rich cultural heritage.',
    short_description: 'Limited edition watch with traditional Nepali artistry.',
    sku: 'KW-HE-002',
    price: 35000,
    compare_price: null,
    final_price: 35000,
    discount_percentage: 0,
    stock_quantity: 10,
    in_stock: true,
    is_featured: true,
    is_new: true,
    is_best_seller: false,
    is_limited_edition: true,
    status: 'active',
    gender: 'unisex',
    movement: 'Automatic',
    strap: 'Leather',
    case_material: 'Stainless Steel',
    case_diameter: '38mm',
    case_thickness: '9mm',
    water_resistance: '30m',
    dial_color: 'Blue',
    glass_type: 'Sapphire Crystal',
    warranty_period: '3 years',
    average_rating: 4.7,
    reviews_count: 15,
    brand: { id: 10, name: 'Kathmandu Watches', slug: 'kathmandu-watches', description: '', logo: '', is_featured: true, products_count: 12 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-08',
    updated_at: '2024-01-08'
  },
  {
    id: 5,
    name: 'Tag Heuer Carrera',
    slug: 'tag-heuer-carrera',
    description: 'Inspired by motor racing, the Carrera is a symbol of speed and precision.',
    short_description: 'Sporty chronograph with tachymeter bezel.',
    sku: 'TH-CAR-011',
    price: 185000,
    compare_price: 200000,
    final_price: 185000,
    discount_percentage: 8,
    stock_quantity: 12,
    in_stock: true,
    is_featured: true,
    is_new: false,
    is_best_seller: true,
    is_limited_edition: false,
    status: 'active',
    gender: 'men',
    movement: 'Automatic',
    strap: 'Leather',
    case_material: 'Stainless Steel',
    case_diameter: '44mm',
    case_thickness: '13mm',
    water_resistance: '100m',
    dial_color: 'Black',
    glass_type: 'Sapphire Crystal',
    warranty_period: '2 years',
    average_rating: 4.6,
    reviews_count: 67,
    brand: { id: 3, name: 'Tag Heuer', slug: 'tag-heuer', description: '', logo: '', is_featured: true, products_count: 32 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1548171915-e79a380a2a4b?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1548171915-e79a380a2a4b?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-05',
    updated_at: '2024-01-05'
  },
  {
    id: 6,
    name: 'Seiko Prospex Diver',
    slug: 'seiko-prospex-diver',
    description: 'Professional diving watch with exceptional water resistance.',
    short_description: '200m diver with automatic movement and LumiBrite dial.',
    sku: 'SKX-007',
    price: 45000,
    compare_price: 50000,
    final_price: 45000,
    discount_percentage: 10,
    stock_quantity: 25,
    in_stock: true,
    is_featured: false,
    is_new: false,
    is_best_seller: true,
    is_limited_edition: false,
    status: 'active',
    gender: 'men',
    movement: 'Automatic',
    strap: 'Rubber',
    case_material: 'Stainless Steel',
    case_diameter: '42mm',
    case_thickness: '13mm',
    water_resistance: '200m',
    dial_color: 'Black',
    glass_type: 'Hardlex Crystal',
    warranty_period: '2 years',
    average_rating: 4.4,
    reviews_count: 156,
    brand: { id: 4, name: 'Seiko', slug: 'seiko', description: '', logo: '', is_featured: true, products_count: 56 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-03',
    updated_at: '2024-01-03'
  },
  {
    id: 7,
    name: 'Himalayan Horology Everest Edition',
    slug: 'himlayan-horology-everest-edition',
    description: 'Limited edition timepiece inspired by the world\'s highest peak.',
    short_description: 'Luxury watch with mountain-inspired design and Swiss movement.',
    sku: 'HH-EV-001',
    price: 85000,
    compare_price: 100000,
    final_price: 85000,
    discount_percentage: 15,
    stock_quantity: 5,
    in_stock: true,
    is_featured: true,
    is_new: true,
    is_best_seller: false,
    is_limited_edition: true,
    status: 'active',
    gender: 'unisex',
    movement: 'Automatic',
    strap: 'Leather',
    case_material: 'Titanium',
    case_diameter: '42mm',
    case_thickness: '11mm',
    water_resistance: '100m',
    dial_color: 'Silver',
    glass_type: 'Sapphire Crystal',
    warranty_period: '5 years',
    average_rating: 4.9,
    reviews_count: 8,
    brand: { id: 11, name: 'Himalayan Horology', slug: 'himlayan-horology', description: '', logo: '', is_featured: false, products_count: 8 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-12',
    updated_at: '2024-01-12'
  },
  {
    id: 8,
    name: 'Citizen Eco-Drive',
    slug: 'citizen-eco-drive',
    description: 'Solar-powered watch that never needs a battery replacement.',
    short_description: 'Eco-Drive technology with perpetual calendar.',
    sku: 'CTZ-ED-001',
    price: 28000,
    compare_price: 32000,
    final_price: 28000,
    discount_percentage: 13,
    stock_quantity: 30,
    in_stock: true,
    is_featured: false,
    is_new: false,
    is_best_seller: true,
    is_limited_edition: false,
    status: 'active',
    gender: 'unisex',
    movement: 'Solar',
    strap: 'Stainless Steel',
    case_material: 'Stainless Steel',
    case_diameter: '40mm',
    case_thickness: '9mm',
    water_resistance: '100m',
    dial_color: 'Blue',
    glass_type: 'Mineral Crystal',
    warranty_period: '5 years',
    average_rating: 4.3,
    reviews_count: 92,
    brand: { id: 5, name: 'Citizen', slug: 'citizen', description: '', logo: '', is_featured: false, products_count: 42 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-04',
    updated_at: '2024-01-04'
  },
  {
    id: 9,
    name: 'Casio G-Shock',
    slug: 'casio-g-shock',
    description: 'Virtually indestructible watch with shock resistance.',
    short_description: '200m water resistance with digital and analog display.',
    sku: 'GS-GA-2100',
    price: 15000,
    compare_price: 18000,
    final_price: 15000,
    discount_percentage: 17,
    stock_quantity: 50,
    in_stock: true,
    is_featured: false,
    is_new: false,
    is_best_seller: true,
    is_limited_edition: false,
    status: 'active',
    gender: 'unisex',
    movement: 'Quartz',
    strap: 'Resin',
    case_material: 'Resin',
    case_diameter: '45mm',
    case_thickness: '11mm',
    water_resistance: '200m',
    dial_color: 'Black',
    glass_type: 'Mineral Crystal',
    warranty_period: '2 years',
    average_rating: 4.5,
    reviews_count: 234,
    brand: { id: 6, name: 'Casio', slug: 'casio', description: '', logo: '', is_featured: false, products_count: 67 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-06',
    updated_at: '2024-01-06'
  },
  {
    id: 10,
    name: 'Tissot PRX',
    slug: 'tissot-prx',
    description: 'Modern reinterpretation of the 1978 classic.',
    short_description: 'Integrated bracelet with automatic movement.',
    sku: 'TS-PRX-001',
    price: 55000,
    compare_price: 60000,
    final_price: 55000,
    discount_percentage: 8,
    stock_quantity: 18,
    in_stock: true,
    is_featured: true,
    is_new: true,
    is_best_seller: false,
    is_limited_edition: false,
    status: 'active',
    gender: 'unisex',
    movement: 'Automatic',
    strap: 'Stainless Steel',
    case_material: 'Stainless Steel',
    case_diameter: '40mm',
    case_thickness: '10mm',
    water_resistance: '100m',
    dial_color: 'Silver',
    glass_type: 'Sapphire Crystal',
    warranty_period: '2 years',
    average_rating: 4.6,
    reviews_count: 45,
    brand: { id: 8, name: 'Tissot', slug: 'tissot', description: '', logo: '', is_featured: true, products_count: 39 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-09',
    updated_at: '2024-01-09'
  },
  {
    id: 11,
    name: 'Everest Timepieces Summit',
    slug: 'everest-timepieces-summit',
    description: 'Luxury timepiece representing the pinnacle of Nepali watchmaking.',
    short_description: 'Gold-plated watch with Swiss automatic movement.',
    sku: 'ET-SUM-001',
    price: 120000,
    compare_price: 150000,
    final_price: 120000,
    discount_percentage: 20,
    stock_quantity: 3,
    in_stock: true,
    is_featured: true,
    is_new: true,
    is_best_seller: false,
    is_limited_edition: true,
    status: 'active',
    gender: 'men',
    movement: 'Automatic',
    strap: 'Leather',
    case_material: 'Gold Plated',
    case_diameter: '40mm',
    case_thickness: '10mm',
    water_resistance: '50m',
    dial_color: 'White',
    glass_type: 'Sapphire Crystal',
    warranty_period: '5 years',
    average_rating: 5.0,
    reviews_count: 5,
    brand: { id: 12, name: 'Everest Timepieces', slug: 'everest-timepieces', description: '', logo: '', is_featured: false, products_count: 6 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1548171915-e79a380a2a4b?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1548171915-e79a380a2a4b?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-11',
    updated_at: '2024-01-11'
  },
  {
    id: 12,
    name: 'Fossil Minimalist',
    slug: 'fossil-minimalist',
    description: 'Clean and simple design for everyday elegance.',
    short_description: 'Minimalist watch with leather strap.',
    sku: 'FS-MIN-001',
    price: 12000,
    compare_price: 15000,
    final_price: 12000,
    discount_percentage: 20,
    stock_quantity: 40,
    in_stock: true,
    is_featured: false,
    is_new: false,
    is_best_seller: false,
    is_limited_edition: false,
    status: 'active',
    gender: 'unisex',
    movement: 'Quartz',
    strap: 'Leather',
    case_material: 'Stainless Steel',
    case_diameter: '36mm',
    case_thickness: '8mm',
    water_resistance: '30m',
    dial_color: 'White',
    glass_type: 'Mineral Crystal',
    warranty_period: '2 years',
    average_rating: 4.2,
    reviews_count: 78,
    brand: { id: 7, name: 'Fossil', slug: 'fossil', description: '', logo: '', is_featured: false, products_count: 51 },
    categories: [],
    images: ['https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-07',
    updated_at: '2024-01-07'
  }
]

function fetchProducts() {
  loading.value = true
  setTimeout(() => {
    let filteredProducts = sampleProducts
    
    // Filter by brand
    if (filters.value.brand) {
      const brandId = filters.value.brand
      filteredProducts = filteredProducts.filter(p => p.brand?.id === brandId)
    }
    
    // Filter by gender
    if (filters.value.gender) {
      filteredProducts = filteredProducts.filter(p => p.gender === filters.value.gender)
    }
    
    // Filter by search
    if (filters.value.search) {
      const searchLower = filters.value.search.toLowerCase()
      filteredProducts = filteredProducts.filter(p => 
        p.name.toLowerCase().includes(searchLower) ||
        p.description.toLowerCase().includes(searchLower)
      )
    }
    
    // Filter by price range
    if (filters.value.min_price) {
      filteredProducts = filteredProducts.filter(p => p.final_price >= filters.value.min_price!)
    }
    if (filters.value.max_price) {
      filteredProducts = filteredProducts.filter(p => p.final_price <= filters.value.max_price!)
    }
    
    products.value = filteredProducts
    pagination.value = {
      current_page: 1,
      last_page: 1,
      total: filteredProducts.length
    }
    loading.value = false
  }, 500)
}

function applyFilters() {
  filters.value.page = 1
  fetchProducts()
  updateURL()
}

function clearFilters() {
  filters.value = {
    search: '',
    gender: '',
    min_price: undefined,
    max_price: undefined,
    sort: 'newest',
    per_page: 12,
    page: 1
  }
  fetchProducts()
  updateURL()
}

function goToPage(page: number) {
  filters.value.page = page
  fetchProducts()
  updateURL()
}

function updateURL() {
  const query: any = { ...filters.value }
  Object.keys(query).forEach(key => {
    if (query[key] === undefined || query[key] === '') {
      delete query[key]
    }
    if (key === 'in_stock' && query[key] === true) {
      query[key] = '1'
    }
  })
  router.push({ query })
}

function loadFiltersFromURL() {
  if (route.query.search) filters.value.search = route.query.search as string
  if (route.query.gender) filters.value.gender = route.query.gender as string
  if (route.query.brand) {
    const brandId = Number(route.query.brand)
    if (!isNaN(brandId)) {
      filters.value.brand = brandId
    }
  }
  if (route.query.min_price) filters.value.min_price = Number(route.query.min_price)
  if (route.query.max_price) filters.value.max_price = Number(route.query.max_price)
  if (route.query.sort) filters.value.sort = route.query.sort as any
  if (route.query.per_page) filters.value.per_page = Number(route.query.per_page)
  if (route.query.page) filters.value.page = Number(route.query.page)
}

onMounted(() => {
  loadFiltersFromURL()
  fetchProducts()
  if (authStore.isAuthenticated) {
    wishlistStore.load()
  }
})
</script>
