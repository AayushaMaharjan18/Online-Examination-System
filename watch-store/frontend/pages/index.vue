<template>
  <div>
    <!-- Hero Banner -->
    <section class="relative h-screen min-h-[600px] max-h-[900px]">
      <Swiper
        :modules="[SwiperAutoplay, SwiperEffectFade, SwiperPagination, SwiperNavigation]"
        :autoplay="{ delay: 5000, disableOnInteraction: false }"
        :effect="'fade'"
        :pagination="{ clickable: true }"
        :navigation="true"
        :loop="true"
        class="h-full"
      >
        <SwiperSlide v-for="(slide, index) in heroSlides" :key="index">
          <div class="relative h-full">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent z-10" />
            <img :src="slide.image || `https://picsum.photos/seed/watch${index}/1920/1080`" :alt="slide.title" class="w-full h-full object-cover" />
            <div class="absolute inset-0 z-20 flex items-center">
              <div class="container-premium">
                <div class="max-w-2xl animate-fade-in">
                  <p class="text-gold-500 text-lg md:text-xl font-medium mb-4 tracking-wider uppercase">{{ slide.subtitle }}</p>
                  <h1 class="text-4xl md:text-6xl lg:text-7xl font-display font-bold text-white mb-6 leading-tight">{{ slide.title }}</h1>
                  <p v-if="slide.description" class="text-gray-200 text-lg md:text-xl mb-8">{{ slide.description }}</p>
                  <NuxtLink :to="slide.button_url || '/shop'" class="btn-gold text-lg px-10 py-4">
                    {{ slide.button_text || 'Shop Now' }}
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </SwiperSlide>
      </Swiper>
    </section>

    <!-- Featured Collection -->
    <section class="section-padding">
      <div class="container-premium">
        <div class="text-center mb-12">
          <span class="text-gold-500 text-sm font-medium tracking-widest uppercase">Curated Collection</span>
          <h2 class="text-3xl md:text-4xl font-display font-bold mt-2">Featured Collection</h2>
          <p class="text-gray-500 mt-3 max-w-xl mx-auto">Discover our handpicked selection of premium timepieces</p>
        </div>
        <ProductCarousel :products="featuredProducts" />
      </div>
    </section>

    <!-- New Arrivals -->
    <section class="section-padding bg-luxury-gray">
      <div class="container-premium">
        <div class="flex items-center justify-between mb-12">
          <div>
            <span class="text-gold-500 text-sm font-medium tracking-widest uppercase">Latest</span>
            <h2 class="text-3xl md:text-4xl font-display font-bold mt-2">New Arrivals</h2>
          </div>
          <NuxtLink to="/shop?sort=newest" class="btn-outline text-sm">View All</NuxtLink>
        </div>
        <ProductCarousel :products="newArrivals" />
      </div>
    </section>

    <!-- Best Sellers -->
    <section class="section-padding">
      <div class="container-premium">
        <div class="text-center mb-12">
          <span class="text-gold-500 text-sm font-medium tracking-widest uppercase">Popular</span>
          <h2 class="text-3xl md:text-4xl font-display font-bold mt-2">Best Sellers</h2>
          <p class="text-gray-500 mt-3 max-w-xl mx-auto">Most loved watches by our customers</p>
        </div>
        <ProductCarousel :products="bestSellers" />
      </div>
    </section>

    <!-- Limited Edition -->
    <section class="section-padding bg-luxury-gray">
      <div class="container-premium">
        <div class="flex items-center justify-between mb-12">
          <div>
            <span class="text-gold-500 text-sm font-medium tracking-widest uppercase">Exclusive</span>
            <h2 class="text-3xl md:text-4xl font-display font-bold mt-2">Limited Edition</h2>
          </div>
          <NuxtLink to="/shop?sort=newest" class="btn-outline text-sm">View All</NuxtLink>
        </div>
        <ProductCarousel :products="limitedEdition" />
      </div>
    </section>

    <!-- Special Offer Banner -->
    <section class="relative py-24 overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-luxury-black via-gray-900 to-luxury-black" />
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-72 h-72 bg-gold-500 rounded-full blur-3xl" />
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-gold-500 rounded-full blur-3xl" />
      </div>
      <div class="container-premium relative z-10 text-center">
        <span class="text-gold-500 text-sm font-medium tracking-widest uppercase">Special Offer</span>
        <h2 class="text-4xl md:text-5xl font-display font-bold text-white mt-4 mb-4">Luxury Watches at <span class="text-gold-500">30% Off</span></h2>
        <p class="text-gray-300 text-lg max-w-2xl mx-auto mb-8">Limited time offer on select premium timepieces. Don't miss out on this exclusive opportunity.</p>
        <NuxtLink to="/shop" class="btn-gold text-lg px-10 py-4">Shop the Sale</NuxtLink>
      </div>
    </section>

    <!-- Top Brands -->
    <section class="section-padding">
      <div class="container-premium">
        <div class="text-center mb-12">
          <span class="text-gold-500 text-sm font-medium tracking-widest uppercase">Brands</span>
          <h2 class="text-3xl md:text-4xl font-display font-bold mt-2">Top Brands</h2>
        </div>
        <BrandCarousel :brands="brands" />
      </div>
    </section>

    <!-- Customer Reviews -->
    <section class="section-padding bg-luxury-gray">
      <div class="container-premium">
        <div class="text-center mb-12">
          <span class="text-gold-500 text-sm font-medium tracking-widest uppercase">Testimonials</span>
          <h2 class="text-3xl md:text-4xl font-display font-bold mt-2">What Our Customers Say</h2>
        </div>
        <ReviewCarousel />
      </div>
    </section>

    <!-- Latest Blog -->
    <section class="section-padding">
      <div class="container-premium">
        <div class="flex items-center justify-between mb-12">
          <div>
            <span class="text-gold-500 text-sm font-medium tracking-widest uppercase">Journal</span>
            <h2 class="text-3xl md:text-4xl font-display font-bold mt-2">Latest from Our Blog</h2>
          </div>
          <NuxtLink to="/blog" class="btn-outline text-sm">View All</NuxtLink>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <article v-for="blog in blogs" :key="blog.id" class="card-premium overflow-hidden group">
            <NuxtLink :to="`/blog/${blog.slug}`">
              <div class="aspect-[16/10] overflow-hidden">
                <img :src="blog.featured_image || 'https://picsum.photos/seed/blog/600/400'" :alt="blog.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              </div>
              <div class="p-6">
                <p class="text-sm text-gray-400 mb-2">{{ blog.published_at }}</p>
                <h3 class="text-lg font-display font-semibold mb-2 group-hover:text-gold-500 transition-colors">{{ blog.title }}</h3>
                <p class="text-gray-500 text-sm line-clamp-2">{{ blog.excerpt }}</p>
              </div>
            </NuxtLink>
          </article>
        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="section-padding bg-luxury-black">
      <div class="container-premium text-center">
        <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-4">Join Our Newsletter</h2>
        <p class="text-gray-400 max-w-xl mx-auto mb-8">Subscribe to receive exclusive offers, new arrivals, and insider access to limited editions.</p>
        <form @submit.prevent="subscribeNewsletter" class="max-w-md mx-auto flex gap-4">
          <input
            v-model="email"
            type="email"
            placeholder="Your email address"
            class="flex-1 px-4 py-3 rounded-lg border-2 border-gray-700 bg-transparent text-white placeholder-gray-500 focus:border-gold-500 focus:outline-none"
            required
          />
          <button type="submit" class="btn-gold whitespace-nowrap">Subscribe</button>
        </form>
      </div>
    </section>

    <!-- Instagram Gallery -->
    <section class="py-16">
      <div class="container-premium">
        <div class="text-center mb-12">
          <span class="text-gold-500 text-sm font-medium tracking-widest uppercase">Follow Us</span>
          <h2 class="text-3xl md:text-4xl font-display font-bold mt-2">@watchstore_nepal</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div v-for="i in 8" :key="i" class="aspect-square overflow-hidden rounded-xl group cursor-pointer">
            <img :src="`https://picsum.photos/seed/insta${i}/400/400`" alt="Instagram" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, EffectFade, Pagination, Navigation } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/effect-fade'
import 'swiper/css/pagination'
import 'swiper/css/navigation'
import type { Product, Brand, Blog, HeroSlider } from '~/types'
import { useAuthStore } from '~/stores/auth'
import { useWishlistStore } from '~/stores/wishlist'

const SwiperAutoplay = Autoplay
const SwiperEffectFade = EffectFade
const SwiperPagination = Pagination
const SwiperNavigation = Navigation

const email = ref('')
const authStore = useAuthStore()
const wishlistStore = useWishlistStore()

const heroSlides = ref<HeroSlider[]>([
  { 
    id: 1, 
    title: 'Timeless Elegance', 
    subtitle: 'Premium Collection 2024', 
    description: 'Discover watches that define luxury', 
    button_text: 'Shop Now', 
    button_url: '/shop', 
    image: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=1920&h=1080&fit=crop' 
  },
  { 
    id: 2, 
    title: 'Luxury Redefined', 
    subtitle: 'New Arrivals', 
    description: 'Explore the latest timepieces', 
    button_text: 'Explore', 
    button_url: '/shop', 
    image: 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=1920&h=1080&fit=crop' 
  },
])

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
  }
]

const sampleBrands: Brand[] = [
  {
    id: 1,
    name: 'Rolex',
    slug: 'rolex',
    description: 'The world\'s most recognized watch brand.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Rolex_logo.svg/200px-Rolex_logo.svg.png',
    is_featured: true,
    products_count: 45
  },
  {
    id: 2,
    name: 'Omega',
    slug: 'omega',
    description: 'Swiss luxury watchmaker famous for the Speedmaster.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Omega_Logo.svg/200px-Omega_Logo.svg.png',
    is_featured: true,
    products_count: 38
  },
  {
    id: 3,
    name: 'Tag Heuer',
    slug: 'tag-heuer',
    description: 'Renowned for sports watches and chronographs.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/42/Tag_Heuer_Logo.svg/200px-Tag_Heuer_Logo.svg.png',
    is_featured: true,
    products_count: 32
  },
  {
    id: 4,
    name: 'Seiko',
    slug: 'seiko',
    description: 'Japanese watchmaker known for innovation.',
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Seiko_logo.svg/200px-Seiko_logo.svg.png',
    is_featured: true,
    products_count: 56
  },
  {
    id: 9,
    name: 'Nepal Time',
    slug: 'nepal-time',
    description: 'Proudly Nepali watch brand.',
    logo: 'https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=200&h=200&fit=crop',
    is_featured: true,
    products_count: 15
  },
  {
    id: 10,
    name: 'Kathmandu Watches',
    slug: 'kathmandu-watches',
    description: 'Local Nepali brand crafting elegant watches.',
    logo: 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=200&h=200&fit=crop',
    is_featured: true,
    products_count: 12
  }
]

const sampleBlogs: Blog[] = [
  {
    id: 1,
    title: 'The Art of Watch Collecting: A Beginner\'s Guide',
    slug: 'the-art-of-watch-collecting',
    content: 'Watch collecting is a fascinating hobby.',
    excerpt: 'Discover the fundamentals of building a meaningful watch collection.',
    featured_image: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=800',
    author: 'Rajesh Sharma',
    meta_title: 'The Art of Watch Collecting',
    meta_description: 'A comprehensive guide for beginners.',
    published_at: '2024-01-15'
  },
  {
    id: 2,
    title: 'Top 5 Luxury Watches for 2024',
    slug: 'top-5-luxury-watches-2024',
    content: 'This year brings exciting new releases.',
    excerpt: 'Explore the most sought-after luxury timepieces of 2024.',
    featured_image: 'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?w=800',
    author: 'Priya Thapa',
    meta_title: 'Top 5 Luxury Watches 2024',
    meta_description: 'Our picks for the best luxury watches.',
    published_at: '2024-01-10'
  },
  {
    id: 3,
    title: 'Understanding Watch Movements',
    slug: 'understanding-watch-movements',
    content: 'The heart of any watch is its movement.',
    excerpt: 'Learn about different types of watch movements.',
    featured_image: 'https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=800',
    author: 'Bikash Gurung',
    meta_title: 'Watch Movements Explained',
    meta_description: 'A detailed comparison of movements.',
    published_at: '2024-01-05'
  }
]

const featuredProducts = ref<Product[]>(sampleProducts.filter(p => p.is_featured).slice(0, 4))
const newArrivals = ref<Product[]>(sampleProducts.filter(p => p.is_new).slice(0, 4))
const bestSellers = ref<Product[]>(sampleProducts.filter(p => p.is_best_seller).slice(0, 4))
const limitedEdition = ref<Product[]>(sampleProducts.filter(p => p.is_limited_edition).slice(0, 4))
const brands = ref<Brand[]>(sampleBrands)
const blogs = ref<Blog[]>(sampleBlogs)

function subscribeNewsletter() {
  email.value = ''
}

onMounted(() => {
  if (authStore.isAuthenticated) {
    wishlistStore.load()
  }
})
</script>