<template>
  <div class="min-h-screen bg-gray-50">
    <div v-if="loading" class="flex justify-center items-center py-24">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold-500"></div>
    </div>

    <div v-else-if="!blog" class="container mx-auto px-4 py-24 text-center">
      <p class="text-gray-500">Blog post not found</p>
    </div>

    <div v-else>
      <!-- Hero Image -->
      <div class="aspect-video max-h-96 overflow-hidden">
        <img
          :src="blog.featured_image"
          :alt="blog.title"
          class="w-full h-full object-cover"
        />
      </div>

      <div class="container mx-auto px-4 py-12 max-w-4xl">
        <!-- Breadcrumb -->
        <nav class="text-sm mb-8">
          <ol class="flex items-center space-x-2">
            <li><NuxtLink to="/" class="text-gray-500 hover:text-gold-500">Home</NuxtLink></li>
            <li class="text-gray-400">/</li>
            <li><NuxtLink to="/blog" class="text-gray-500 hover:text-gold-500">Blog</NuxtLink></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-900">{{ blog.title }}</li>
          </ol>
        </nav>

        <!-- Blog Content -->
        <article>
          <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
            <span>{{ blog.author }}</span>
            <span>•</span>
            <span>{{ new Date(blog.published_at).toLocaleDateString() }}</span>
          </div>

          <h1 class="text-4xl font-serif mb-8">{{ blog.title }}</h1>

          <div class="prose prose-lg max-w-none text-gray-700" v-html="blog.content"></div>
        </article>

        <!-- Back to Blog -->
        <div class="mt-12 pt-8 border-t">
          <NuxtLink
            to="/blog"
            class="inline-flex items-center text-gold-500 hover:text-gold-600 font-medium"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Blog
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import type { Blog } from '~/types'

const route = useRoute()
const blog = ref<Blog | null>(null)
const loading = ref(true)

const sampleBlogs: Blog[] = [
  {
    id: 1,
    title: 'The Art of Watch Collecting: A Beginner\'s Guide',
    slug: 'the-art-of-watch-collecting',
    content: `<p>Watch collecting is a fascinating hobby that combines history, engineering, and style. Whether you're just starting or looking to expand your collection, understanding the basics is essential.</p>
    <h3>Why Collect Watches?</h3>
    <p>Collecting watches is more than just acquiring timepieces. It's about appreciating the craftsmanship, the history behind each brand, and the technical innovations that have shaped the industry over centuries.</p>
    <h3>Starting Your Collection</h3>
    <p>Begin with watches that resonate with you personally. Consider your lifestyle, budget, and what draws you to horology. Some collectors focus on specific brands, while others prefer certain complications or eras.</p>
    <h3>Key Considerations</h3>
    <ul>
      <li>Condition and authenticity are paramount</li>
      <li>Research the market and provenance</li>
      <li>Buy from reputable dealers</li>
      <li>Consider long-term value and enjoyment</li>
    </ul>`,
    excerpt: 'Discover the fundamentals of building a meaningful watch collection that reflects your personal style and appreciation for horology.',
    featured_image: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=800',
    author: 'Rajesh Sharma',
    meta_title: 'The Art of Watch Collecting',
    meta_description: 'A comprehensive guide for beginners interested in starting a watch collection.',
    published_at: '2024-01-15'
  },
  {
    id: 2,
    title: 'Top 5 Luxury Watches for 2024',
    slug: 'top-5-luxury-watches-2024',
    content: `<p>This year brings exciting new releases from top watch brands. We've curated a list of the most anticipated luxury watches that are making waves in the horology world.</p>
    <h3>1. Rolex Submariner Date</h3>
    <p>The iconic diver's watch continues to be a favorite among collectors with its timeless design and robust construction.</p>
    <h3>2. Omega Speedmaster Professional</h3>
    <p>The legendary Moonwatch remains a must-have for any serious collector, combining history with exceptional performance.</p>
    <h3>3. Patek Philippe Nautilus</h3>
    <p>With its distinctive design and prestigious heritage, the Nautilus represents the pinnacle of luxury sports watches.</p>
    <h3>4. Audemars Piguet Royal Oak</h3>
    <p>The revolutionary integrated bracelet design continues to influence watch design decades after its introduction.</p>
    <h3>5. Cartier Tank</h3>
    <p>A timeless classic that has adorned the wrists of style icons for generations.</p>`,
    excerpt: 'Explore the most sought-after luxury timepieces of 2024 from renowned watchmakers.',
    featured_image: 'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?w=800',
    author: 'Priya Thapa',
    meta_title: 'Top 5 Luxury Watches 2024',
    meta_description: 'Our picks for the best luxury watches released in 2024.',
    published_at: '2024-01-10'
  },
  {
    id: 3,
    title: 'Understanding Watch Movements: Mechanical vs Quartz',
    slug: 'understanding-watch-movements',
    content: `<p>The heart of any watch is its movement. Understanding the difference between mechanical and quartz movements is crucial for any watch enthusiast.</p>
    <h3>Mechanical Movements</h3>
    <p>Mechanical watches are powered by a wound spring that drives a series of gears and springs. They can be manual (wound by hand) or automatic (wound by the motion of the wearer's wrist).</p>
    <h3>Quartz Movements</h3>
    <p>Quartz watches use a battery to send an electrical current through a quartz crystal, causing it to vibrate at a precise frequency. This vibration powers the watch movement.</p>
    <h3>Key Differences</h3>
    <ul>
      <li>Mechanical: More craftsmanship, higher maintenance, longer lifespan</li>
      <li>Quartz: More accurate, lower maintenance, battery replacement</li>
    </ul>`,
    excerpt: 'Learn about the different types of watch movements and what makes each unique.',
    featured_image: 'https://images.unsplash.com/photo-1587836374828-4dbafa94cf0e?w=800',
    author: 'Bikash Gurung',
    meta_title: 'Watch Movements Explained',
    meta_description: 'A detailed comparison of mechanical and quartz watch movements.',
    published_at: '2024-01-05'
  },
  {
    id: 4,
    title: 'How to Care for Your Luxury Watch',
    slug: 'how-to-care-for-luxury-watch',
    content: `<p>Proper maintenance is essential to keep your luxury watch running perfectly for years to come. Here are expert tips on watch care and maintenance.</p>
    <h3>Daily Care</h3>
    <ul>
      <li>Avoid exposing your watch to extreme temperatures</li>
      <li>Keep it away from magnets that can affect accuracy</li>
      <li>Clean with a soft cloth regularly</li>
    </ul>
    <h3>Regular Servicing</h3>
    <p>Mechanical watches should be serviced every 3-5 years by a qualified watchmaker. This includes cleaning, oiling, and checking for wear.</p>
    <h3>Water Resistance</h3>
    <p>Have water resistance tested annually, especially if you swim with your watch. Replace gaskets and seals as needed.</p>`,
    excerpt: 'Essential tips for maintaining and caring for your luxury timepiece.',
    featured_image: 'https://images.unsplash.com/photo-1548171915-e79a380a2a4b?w=800',
    author: 'Rajesh Sharma',
    meta_title: 'Luxury Watch Care Guide',
    meta_description: 'Expert advice on how to care for and maintain your luxury watch.',
    published_at: '2024-01-01'
  },
  {
    id: 5,
    title: 'The History of Swiss Watchmaking',
    slug: 'history-of-swiss-watchmaking',
    content: `<p>Switzerland has been the epicenter of watchmaking for centuries. Discover the rich history and traditions that make Swiss watches the gold standard in horology.</p>
    <h3>Early Beginnings</h3>
    <p>Swiss watchmaking began in the 16th century in Geneva, influenced by Huguenot refugees who brought their craftsmanship skills.</p>
    <h3>The Golden Age</h3>
    <p>The 19th and early 20th centuries saw Swiss watchmaking flourish, with brands like Patek Philippe, Vacheron Constantin, and Audemars Piguet establishing their legacies.</p>
    <h3>Modern Era</h3>
    <p>Today, Switzerland continues to lead the industry with innovations in materials, complications, and precision engineering.</p>`,
    excerpt: 'Explore the fascinating history and heritage of Swiss watchmaking.',
    featured_image: 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=800',
    author: 'Priya Thapa',
    meta_title: 'History of Swiss Watchmaking',
    meta_description: 'A journey through the history of Swiss watchmaking traditions.',
    published_at: '2023-12-20'
  },
  {
    id: 6,
    title: 'Investment Watches: Timepieces That Hold Value',
    slug: 'investment-watches',
    content: `<p>Some watches are not just accessories but investments. Learn about timepieces that have historically held or increased in value over time.</p>
    <h3>What Makes a Watch an Investment?</h3>
    <ul>
      <li>Limited production numbers</li>
      <li>Brand heritage and prestige</li>
      <li>Historical significance</li>
      <li>Condition and provenance</li>
    </ul>
    <h3>Notable Investment Pieces</h3>
    <p>Certain models from Rolex, Patek Philippe, and Audemars Piguet have consistently shown strong appreciation over decades.</p>
    <h3>Risks and Considerations</h3>
    <p>While some watches appreciate, others may depreciate. Research and expert advice are essential before making investment decisions.</p>`,
    excerpt: 'Discover luxury watches that are considered good investments.',
    featured_image: 'https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?w=800',
    author: 'Bikash Gurung',
    meta_title: 'Investment Watches Guide',
    meta_description: 'A guide to watches that hold their value over time.',
    published_at: '2023-12-15'
  }
]

async function fetchBlog() {
  loading.value = true
  try {
    const slug = route.params.slug as string
    blog.value = sampleBlogs.find(b => b.slug === slug) || null
  } catch (error) {
    console.error('Failed to fetch blog:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchBlog()
})
</script>
