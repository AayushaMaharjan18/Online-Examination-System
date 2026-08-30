<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold-500"></div>
      </div>

      <div v-else-if="blogs.length === 0" class="text-center py-12 text-gray-500">
        No blog posts available
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <article
          v-for="blog in blogs"
          :key="blog.id"
          class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-lg transition-shadow"
        >
          <div class="aspect-video overflow-hidden">
            <img
              :src="blog.featured_image"
              :alt="blog.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
          </div>
          <div class="p-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
              <span>{{ blog.author }}</span>
              <span>•</span>
              <span>{{ new Date(blog.published_at).toLocaleDateString() }}</span>
            </div>
            <h2 class="text-xl font-semibold mb-3 group-hover:text-gold-500 transition-colors">
              <NuxtLink :to="`/blog/${blog.slug}`">
                {{ blog.title }}
              </NuxtLink>
            </h2>
            <p class="text-gray-600 line-clamp-3 mb-4">{{ blog.excerpt }}</p>
            <NuxtLink
              :to="`/blog/${blog.slug}`"
              class="inline-flex items-center text-gold-500 hover:text-gold-600 font-medium"
            >
              Read More
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </NuxtLink>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { Blog } from '~/types'

const loading = ref(false)
const blogs = ref<Blog[]>([
  {
    id: 1,
    title: 'The Art of Watch Collecting: A Beginner\'s Guide',
    slug: 'the-art-of-watch-collecting',
    content: 'Watch collecting is a fascinating hobby that combines history, engineering, and style. Whether you\'re just starting or looking to expand your collection, understanding the basics is essential.',
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
    content: 'This year brings exciting new releases from top watch brands. We\'ve curated a list of the most anticipated luxury watches that are making waves in the horology world.',
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
    content: 'The heart of any watch is its movement. Understanding the difference between mechanical and quartz movements is crucial for any watch enthusiast.',
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
    content: 'Proper maintenance is essential to keep your luxury watch running perfectly for years to come. Here are expert tips on watch care and maintenance.',
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
    content: 'Switzerland has been the epicenter of watchmaking for centuries. Discover the rich history and traditions that make Swiss watches the gold standard in horology.',
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
    content: 'Some watches are not just accessories but investments. Learn about timepieces that have historically held or increased in value over time.',
    excerpt: 'Discover luxury watches that are considered good investments.',
    featured_image: 'https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?w=800',
    author: 'Bikash Gurung',
    meta_title: 'Investment Watches Guide',
    meta_description: 'A guide to watches that hold their value over time.',
    published_at: '2023-12-15'
  }
])
</script>
