<template>
  <div class="min-h-screen bg-gray-50">
    <div v-if="loading" class="flex justify-center items-center py-24">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold-500"></div>
    </div>

    <div v-else-if="!product" class="container mx-auto px-4 py-24 text-center">
      <p class="text-gray-500">Product not found</p>
    </div>

    <div v-else class="container mx-auto px-4 py-8">
      <!-- Breadcrumb -->
      <nav class="text-sm mb-8">
        <ol class="flex items-center space-x-2">
          <li><NuxtLink to="/" class="text-gray-500 hover:text-gold-500">Home</NuxtLink></li>
          <li class="text-gray-400">/</li>
          <li><NuxtLink to="/shop" class="text-gray-500 hover:text-gold-500">Shop</NuxtLink></li>
          <li class="text-gray-400">/</li>
          <li class="text-gray-900">{{ product.name }}</li>
        </ol>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Product Images -->
        <div class="space-y-4">
          <div class="aspect-square bg-white rounded-lg overflow-hidden">
            <img
              :src="selectedImage || product.thumbnail"
              :alt="product.name"
              class="w-full h-full object-cover"
            />
          </div>
          <div class="grid grid-cols-4 gap-2">
            <button
              v-for="(image, index) in product.images"
              :key="index"
              @click="selectedImage = image"
              :class="[
                'aspect-square rounded-lg overflow-hidden border-2',
                selectedImage === image ? 'border-gold-500' : 'border-transparent'
              ]"
            >
              <img :src="image" :alt="`${product.name} ${index + 1}`" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <!-- Product Details -->
        <div>
          <div v-if="product.brand" class="mb-2">
            <NuxtLink :to="`/shop?brand=${product.brand.id}`" class="text-gold-500 hover:text-gold-600">
              {{ product.brand.name }}
            </NuxtLink>
          </div>

          <!-- Categories -->
          <div v-if="product.categories && product.categories.length" class="flex flex-wrap gap-2 mb-4">
            <NuxtLink
              v-for="cat in product.categories"
              :key="cat.id"
              :to="`/shop?category=${cat.id}`"
              class="px-3 py-1 bg-gray-100 hover:bg-gold-500 hover:text-white text-xs font-medium rounded-full text-gray-600 transition-colors"
            >
              {{ cat.name }}
            </NuxtLink>
          </div>

          <h1 class="text-3xl font-serif mb-4">{{ product.name }}</h1>

          <div class="flex items-center gap-4 mb-4">
            <div class="flex items-center">
              <span class="text-yellow-400">★</span>
              <span class="ml-1 text-gray-600">{{ product.average_rating.toFixed(1) }}</span>
              <span class="ml-1 text-gray-400">({{ product.reviews_count }} reviews)</span>
            </div>
            <span v-if="product.in_stock" class="text-green-600">In Stock</span>
            <span v-else class="text-red-600">Out of Stock</span>
          </div>

          <div class="mb-6">
            <div class="flex items-center gap-3">
              <span class="text-3xl font-bold text-luxury-black">Rs. {{ product.final_price.toLocaleString() }}</span>
              <span v-if="product.compare_price" class="text-xl text-gray-400 line-through">
                Rs. {{ product.compare_price.toLocaleString() }}
              </span>
              <span v-if="product.discount_percentage > 0" class="bg-red-500 text-white px-2 py-1 rounded text-sm">
                -{{ product.discount_percentage }}%
              </span>
            </div>
          </div>

          <p class="text-gray-600 mb-6">{{ product.short_description }}</p>

          <!-- Quantity Selector -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
            <div class="flex items-center gap-2">
              <button
                @click="quantity > 1 && quantity--"
                class="w-10 h-10 border border-gray-300 rounded-lg hover:bg-gray-100"
              >
                -
              </button>
              <input
                v-model.number="quantity"
                type="number"
                min="1"
                :max="product.stock_quantity"
                class="w-20 h-10 text-center border border-gray-300 rounded-lg"
              />
              <button
                @click="quantity < product.stock_quantity && quantity++"
                class="w-10 h-10 border border-gray-300 rounded-lg hover:bg-gray-100"
              >
                +
              </button>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-4 mb-6">
            <button
              @click="addToCart"
              :disabled="!product.in_stock"
              class="flex-1 bg-luxury-black text-white py-3 rounded-lg hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Add to Cart
            </button>
            <button
              @click="toggleWishlist"
              class="w-12 h-12 border border-gray-300 rounded-lg hover:bg-gray-100 flex items-center justify-center"
            >
              <svg class="w-6 h-6" :class="wishlistStore.isInWishlist(product.id) ? 'text-red-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
              </svg>
            </button>
          </div>

          <!-- Ask on WhatsApp -->
          <a
            v-if="productWhatsAppLink"
            :href="productWhatsAppLink"
            target="_blank"
            rel="noopener noreferrer"
            class="w-full flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1fb958] text-white font-medium py-3 rounded-lg transition-colors mb-2"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            Ask on WhatsApp
          </a>

          <!-- Product Specifications -->
          <div class="border-t pt-6">
            <h3 class="font-semibold mb-4">Specifications</h3>
            <dl class="grid grid-cols-2 gap-4 text-sm">
              <template v-if="product.movement">
                <dt class="text-gray-500">Movement</dt>
                <dd class="text-gray-900">{{ product.movement }}</dd>
              </template>
              <template v-if="product.case_material">
                <dt class="text-gray-500">Case Material</dt>
                <dd class="text-gray-900">{{ product.case_material }}</dd>
              </template>
              <template v-if="product.case_diameter">
                <dt class="text-gray-500">Case Diameter</dt>
                <dd class="text-gray-900">{{ product.case_diameter }}</dd>
              </template>
              <template v-if="product.water_resistance">
                <dt class="text-gray-500">Water Resistance</dt>
                <dd class="text-gray-900">{{ product.water_resistance }}</dd>
              </template>
              <template v-if="product.strap">
                <dt class="text-gray-500">Strap</dt>
                <dd class="text-gray-900">{{ product.strap }}</dd>
              </template>
              <template v-if="product.dial_color">
                <dt class="text-gray-500">Dial Color</dt>
                <dd class="text-gray-900">{{ product.dial_color }}</dd>
              </template>
              <template v-if="product.warranty_period">
                <dt class="text-gray-500">Warranty</dt>
                <dd class="text-gray-900">{{ product.warranty_period }}</dd>
              </template>
              <dt class="text-gray-500">SKU</dt>
              <dd class="text-gray-900">{{ product.sku }}</dd>
            </dl>
          </div>
        </div>
      </div>

      <!-- Product Description -->
      <div class="mt-12">
        <h2 class="text-2xl font-serif mb-4">Description</h2>
        <div class="prose max-w-none text-gray-600" v-html="product.description"></div>
      </div>

      <!-- Reviews Section -->
      <div class="mt-12">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-serif">Reviews</h2>
          <div class="flex items-center gap-2 text-sm text-gray-600">
            <span class="text-yellow-400">★</span>
            <span class="font-semibold">{{ product.average_rating.toFixed(1) }}</span>
            <span class="text-gray-400">({{ product.reviews_count }} reviews)</span>
          </div>
        </div>

        <!-- Not logged in -->
        <div v-if="!authStore.isAuthenticated" class="bg-white rounded-lg p-6 shadow-sm mb-8">
          <p class="text-gray-600 mb-3">Have you purchased this product? Share your experience.</p>
          <NuxtLink to="/auth/login" class="inline-block bg-gold-500 text-white px-5 py-2 rounded-lg hover:bg-gold-600 transition-colors">
            Login to write a review
          </NuxtLink>
        </div>

        <!-- Write / Edit Review (authenticated) -->
        <div v-else-if="eligibility" class="bg-white rounded-lg p-6 shadow-sm mb-8">
          <template v-if="eligibility.can_review">
            <!-- "Write a Review" button for eligible purchasers; reveals the form -->
            <template v-if="!showReviewForm">
              <h3 class="font-semibold mb-3">Purchased this product? Share your experience.</h3>
              <button
                type="button"
                @click="showReviewForm = true"
                class="bg-gold-500 text-white px-5 py-2 rounded-lg hover:bg-gold-600 transition-colors"
              >
                Write a Review / Rate Product
              </button>
            </template>

            <template v-else>
              <h3 class="font-semibold mb-4">{{ editingReviewId ? 'Edit your review' : 'Write a Review' }}</h3>
              <form @submit.prevent="submitReview">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Your Rating</label>
                <div class="flex gap-1">
                  <button
                    type="button"
                    v-for="n in 5"
                    :key="n"
                    @click="reviewForm.rating = n"
                    :class="n <= reviewForm.rating ? 'text-gold-500' : 'text-gray-300'"
                    class="text-3xl leading-none focus:outline-none transition-colors"
                    :aria-label="`${n} star${n > 1 ? 's' : ''}`"
                  >★</button>
                </div>
                <p v-if="reviewErrors.rating" class="text-red-500 text-sm mt-1">{{ reviewErrors.rating }}</p>
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-gray-400">(optional)</span></label>
                <input v-model="reviewForm.title" type="text" maxlength="255" placeholder="Great quality watch" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent" />
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                <textarea v-model="reviewForm.comment" rows="4" required minlength="3" placeholder="Tell us about your experience with this product..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent"></textarea>
                <p v-if="reviewErrors.comment" class="text-red-500 text-sm mt-1">{{ reviewErrors.comment }}</p>
              </div>
              <div class="flex items-center gap-3">
                <button
                  type="submit"
                  :disabled="reviewSubmitting"
                  class="bg-luxury-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ reviewSubmitting ? 'Submitting...' : (editingReviewId ? 'Update Review' : 'Submit Review') }}
                </button>
                <button v-if="editingReviewId" type="button" @click="cancelEditReview" class="text-sm text-gray-500 hover:text-gray-700">
                  Cancel
                </button>
              </div>
              <p v-if="reviewMessage" :class="reviewMessageType === 'error' ? 'text-red-500' : 'text-green-600'" class="mt-3 text-sm">{{ reviewMessage }}</p>
            </form>
            </template>
          </template>

          <template v-else-if="eligibility.has_reviewed && eligibility.review">
            <h3 class="font-semibold mb-2">Your review</h3>
            <div class="flex items-center gap-1 mb-2">
              <span v-for="i in 5" :key="i" class="text-lg" :class="i <= (eligibility.review?.rating || 0) ? 'text-gold-500' : 'text-gray-300'">★</span>
              <span :class="reviewStatusBadge(eligibility.review?.status)" class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium">
                {{ reviewStatusLabel(eligibility.review?.status) }}
              </span>
            </div>
            <p class="text-gray-600 mb-1">&ldquo;{{ eligibility.review.comment }}&rdquo;</p>
            <div class="mt-3 flex gap-4">
              <button @click="startEditReview(eligibility.review)" class="text-sm text-gold-500 hover:text-gold-600">Edit</button>
              <button @click="deleteMyReview(eligibility.review.id)" class="text-sm text-red-500 hover:text-red-600">Delete</button>
            </div>
          </template>

          <template v-else>
            <p class="text-gray-600">Buy this product to share your experience. Only verified purchasers can leave a review.</p>
          </template>
        </div>

        <!-- Approved customer reviews -->
        <div v-if="reviews.length === 0" class="text-gray-500">No reviews yet</div>
        <div v-else class="space-y-6">
          <div v-for="review in reviews" :key="review.id" class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-3">
                <div class="flex items-center gap-0.5">
                  <svg v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= review.rating ? 'text-gold-500' : 'text-gray-200'" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </div>
                <span class="font-medium">{{ review.user?.name || 'Verified Customer' }}</span>
                <span v-if="review.is_verified_purchase" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Verified Purchase</span>
              </div>
              <span class="text-xs text-gray-400">{{ formatReviewDate(review.created_at) }}</span>
            </div>
            <p v-if="review.title" class="font-medium text-gray-800 mb-1">{{ review.title }}</p>
            <p class="text-gray-600">{{ review.comment }}</p>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div class="mt-12">
        <h2 class="text-2xl font-serif mb-4">Related Products</h2>
        <ProductCarousel v-if="relatedProducts.length > 0" :products="relatedProducts" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import type { Product, Review, ReviewEligibility } from '~/types'
import ProductCard from '~/components/ProductCard.vue'
import ProductCarousel from '~/components/ProductCarousel.vue'
import { useApi } from '~/composables/useApi'
import { useSiteSettings } from '~/composables/useSiteSettings'
import { useCartStore } from '~/stores/cart'
import { useAuthStore } from '~/stores/auth'
import { useWishlistStore } from '~/stores/wishlist'

const route = useRoute()
const api = useApi()
const cartStore = useCartStore()
const authStore = useAuthStore()
const wishlistStore = useWishlistStore()
const { fetchSettings, buildWhatsAppLink, currentUrl } = useSiteSettings()

const product = ref<Product | null>(null)
const loading = ref(true)
const selectedImage = ref('')
const quantity = ref(1)
const reviews = ref<Review[]>([])
const relatedProducts = ref<Product[]>([])

// WhatsApp enquiry link built from the product details and the WhatsApp number
// configured in the website settings. Null when no number is configured.
const productWhatsAppLink = computed(() => {
  if (!product.value) return null
  const p = product.value
  const message = [
    'Hello! I\'m interested in the following product:',
    '',
    `• Product: ${p.name}`,
    `• SKU: ${p.sku}`,
    `• Price: Rs. ${p.final_price.toLocaleString()}`,
    `• Product page: ${currentUrl()}`,
  ].join('\n')
  return buildWhatsAppLink(message)
})

// Review form / eligibility state
const eligibility = ref<ReviewEligibility | null>(null)
const reviewForm = ref({ rating: 5, title: '', comment: '' })
const reviewErrors = ref<{ rating?: string; comment?: string }>({})
const reviewSubmitting = ref(false)
const reviewMessage = ref('')
const reviewMessageType = ref<'success' | 'error'>('success')
const editingReviewId = ref<number | null>(null)
const showReviewForm = ref(false)

// Local fallback data so the detail page still renders without a running API,
// consistent with the homepage and shop pages.
const sampleProducts: Product[] = [
  {
    id: 1,
    name: 'Rolex Submariner Date',
    slug: 'rolex-submariner-date',
    description: '<p>The iconic diver\u2019s watch with Cerachrom bezel and Chromalight display.</p>',
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
    reviews_count: 112,
    brand: { id: 1, name: 'Rolex', slug: 'rolex', description: '', logo: '', is_featured: true, products_count: 42 },
    categories: [
      { id: 1, name: 'Luxury Watches', slug: 'luxury-watches', description: '', image: '', parent_id: null, parent: null, children: [], products_count: 40, sort_order: 1 },
      { id: 3, name: 'Diving Watches', slug: 'diving-watches', description: '', image: '', parent_id: null, parent: null, children: [], products_count: 12, sort_order: 3 },
    ],
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
    description: '<p>The legendary Moonwatch that has been part of all six moon landings.</p>',
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
    categories: [
      { id: 1, name: 'Luxury Watches', slug: 'luxury-watches', description: '', image: '', parent_id: null, parent: null, children: [], products_count: 40, sort_order: 1 },
    ],
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
    description: '<p>Proudly Nepali timepiece inspired by the majestic Himalayas.</p>',
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
    categories: [
      { id: 5, name: 'New Arrivals', slug: 'new-arrivals', description: '', image: '', parent_id: null, parent: null, children: [], products_count: 18, sort_order: 5 },
    ],
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
    description: '<p>Elegant timepiece celebrating Kathmandu\u2019s rich cultural heritage.</p>',
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
    case_material: 'Brass',
    case_diameter: '39mm',
    case_thickness: '9.5mm',
    water_resistance: '30m',
    dial_color: 'Gold',
    glass_type: 'Mineral Glass',
    warranty_period: '2 years',
    average_rating: 4.7,
    reviews_count: 41,
    brand: { id: 10, name: 'Kathmandu Watches', slug: 'kathmandu-watches', description: '', logo: '', is_featured: true, products_count: 12 },
    categories: [
      { id: 4, name: 'Limited Edition', slug: 'limited-edition', description: '', image: '', parent_id: null, parent: null, children: [], products_count: 6, sort_order: 4 },
    ],
    images: ['https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?w=800'],
    thumbnail: 'https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?w=400',
    meta_title: null,
    meta_description: null,
    created_at: '2024-01-15',
    updated_at: '2024-01-15'
  },
]

async function fetchProduct() {
  loading.value = true
  const slug = route.params.slug as string
  try {
    const response = await api.get<{ data: Product }>(`/v1/products/${slug}`)
    product.value = response.data
  } catch (error) {
    // Fall back to local sample data so the page still renders without the API.
    console.warn('API unavailable, using sample product data:', error)
    product.value = sampleProducts.find((p) => p.slug === slug) || null
  }

  if (product.value) {
    selectedImage.value = product.value.thumbnail
    await fetchReviews()
    await fetchRelatedProducts()
    if (authStore.isAuthenticated) {
      await wishlistStore.load()
      await fetchEligibility()
    }
  }
  loading.value = false
}

async function fetchReviews() {
  try {
    if (!product.value) return
    const response = await api.get<{ data: Review[] }>(`/v1/products/${product.value.id}/reviews`)
    reviews.value = response.data || []
  } catch (error) {
    console.error('Failed to fetch reviews:', error)
  }
}

async function fetchEligibility() {
  if (!authStore.isAuthenticated || !product.value) {
    eligibility.value = null
    return
  }
  try {
    const response = await api.get<{ data: ReviewEligibility }>(`/v1/products/${product.value.id}/review-eligibility`)
    eligibility.value = response.data
  } catch (error) {
    console.error('Failed to load review eligibility:', error)
    eligibility.value = null
  }
}

function validateReviewForm(): boolean {
  reviewErrors.value = {}
  if (!reviewForm.value.rating || reviewForm.value.rating < 1 || reviewForm.value.rating > 5) {
    reviewErrors.value.rating = 'Please select a rating between 1 and 5.'
  }
  if (!reviewForm.value.comment || reviewForm.value.comment.trim().length < 3) {
    reviewErrors.value.comment = 'Please write a review (at least 3 characters).'
  }
  return !reviewErrors.value.rating && !reviewErrors.value.comment
}

async function submitReview() {
  if (!product.value) return
  if (!validateReviewForm()) return

  reviewSubmitting.value = true
  reviewMessage.value = ''
  try {
    if (editingReviewId.value) {
      await api.put(`/v1/reviews/${editingReviewId.value}`, {
        rating: reviewForm.value.rating,
        title: reviewForm.value.title,
        comment: reviewForm.value.comment,
      })
      reviewMessage.value = 'Your review has been updated and is pending approval.'
    } else {
      await api.post('/v1/reviews', {
        product_id: product.value.id,
        rating: reviewForm.value.rating,
        title: reviewForm.value.title,
        comment: reviewForm.value.comment,
      })
      reviewMessage.value = 'Thank you! Your review is pending approval.'
    }
    reviewMessageType.value = 'success'
    resetReviewForm()
    await Promise.all([fetchReviews(), fetchEligibility(), refreshProductRating()])
  } catch (error: any) {
    reviewMessage.value = error?.message || 'Failed to submit your review.'
    reviewMessageType.value = 'error'
  } finally {
    reviewSubmitting.value = false
  }
}

function startEditReview(review: Review) {
  editingReviewId.value = review.id
  showReviewForm.value = true
  reviewForm.value = { rating: review.rating, title: review.title || '', comment: review.comment }
  reviewMessage.value = ''
  // Show the form by granting "can_review" while we're editing.
  eligibility.value = {
    purchased: true,
    has_reviewed: false,
    can_review: true,
    review: null,
  }
}

function cancelEditReview() {
  editingReviewId.value = null
  resetReviewForm()
  reviewMessage.value = ''
  fetchEligibility()
}

function resetReviewForm() {
  reviewForm.value = { rating: 5, title: '', comment: '' }
  editingReviewId.value = null
  reviewErrors.value = {}
  showReviewForm.value = false
}

async function deleteMyReview(reviewId: number) {
  if (!window.confirm('Delete your review? This cannot be undone.')) return
  try {
    await api.delete(`/v1/reviews/${reviewId}`)
    reviewMessage.value = 'Your review was deleted.'
    reviewMessageType.value = 'success'
    await Promise.all([fetchReviews(), fetchEligibility(), refreshProductRating()])
  } catch (error: any) {
    reviewMessage.value = error?.message || 'Failed to delete your review.'
    reviewMessageType.value = 'error'
  }
}

async function refreshProductRating() {
  if (!product.value) return
  try {
    const response = await api.get<{ data: Product }>(`/v1/products/${product.value.slug}`)
    if (response.data) {
      product.value.average_rating = response.data.average_rating
      product.value.reviews_count = response.data.reviews_count
    }
  } catch (error) {
    console.error('Failed to refresh product rating:', error)
  }
}

function formatReviewDate(value: string): string {
  if (!value) return ''
  const d = new Date(value)
  if (Number.isNaN(d.getTime())) return value
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

function reviewStatusLabel(status?: string): string {
  switch (status) {
    case 'approved': return 'Approved'
    case 'rejected': return 'Rejected'
    default: return 'Pending approval'
  }
}

function reviewStatusBadge(status?: string): string {
  switch (status) {
    case 'approved': return 'bg-green-100 text-green-700'
    case 'rejected': return 'bg-red-100 text-red-700'
    default: return 'bg-yellow-100 text-yellow-700'
  }
}

async function fetchRelatedProducts() {
  try {
    if (!product.value) return
    relatedProducts.value = await api.get<Product[]>(`/v1/products/${product.value.id}/related`)
  } catch (error) {
    // Fall back to other sample products when the API is unavailable.
    console.warn('API unavailable, using sample related products:', error)
    if (product.value) {
      relatedProducts.value = sampleProducts.filter((p) => p.id !== product.value?.id).slice(0, 4)
    }
  }
}

function addToCart() {
  if (!product.value) return
  if (!authStore.isAuthenticated) {
    navigateTo('/auth/login?redirect=/shop')
    return
  }
  cartStore.addItem(product.value, quantity.value)
}

async function toggleWishlist() {
  if (!authStore.isAuthenticated) {
    navigateTo('/auth/login')
    return
  }
  if (!product.value) return

  try {
    await wishlistStore.toggle(product.value)
  } catch (error) {
    console.error('Failed to update wishlist:', error)
  }
}

onMounted(() => {
  // Refetch eligibility whenever the user logs in / out while on the page.
  watch(() => authStore.isAuthenticated, () => {
    if (!authStore.isAuthenticated) {
      eligibility.value = null
      resetReviewForm()
    } else {
      fetchEligibility()
    }
  })

  // When navigating from one product to another (e.g. via a related-product
  // card), the page component is reused, so refetch based on the new slug.
  watch(() => route.params.slug, () => {
    product.value = null
    reviews.value = []
    relatedProducts.value = []
    quantity.value = 1
    eligibility.value = null
    editingReviewId.value = null
    showReviewForm.value = false
    resetReviewForm()
    fetchProduct()
  })

  fetchProduct()
  // Load site settings (WhatsApp number) so the enquiry button can render.
  fetchSettings()
})
</script>
