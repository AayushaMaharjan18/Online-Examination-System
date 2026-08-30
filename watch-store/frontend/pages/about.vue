<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Story Section -->
    <div class="container mx-auto px-4 py-16">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
          <h2 class="text-3xl font-serif mb-6">Our Story</h2>
          <p class="text-gray-600 mb-4">
            Founded in 2015, WatchStore Nepal has been the premier destination for luxury watch enthusiasts across the country. What started as a small passion project has grown into Nepal's most trusted retailer of authentic timepieces.
          </p>
          <p class="text-gray-600 mb-4">
            We believe that a watch is more than just a timekeeping device – it's a statement of style, a piece of engineering art, and often a cherished heirloom. That's why we're committed to offering only genuine, certified watches from the world's most prestigious brands.
          </p>
          <p class="text-gray-600">
            Our team of watch experts shares your passion for horology and is dedicated to helping you find the perfect timepiece that matches your style and budget.
          </p>
        </div>
        <div class="aspect-video bg-gray-200 rounded-lg overflow-hidden">
          <img src="https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=800" alt="Our Store" class="w-full h-full object-cover" />
        </div>
      </div>
    </div>

    <!-- Values Section -->
    <div class="bg-white py-16">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-serif text-center mb-12">Our Values</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center p-6">
            <div class="w-16 h-16 bg-gold-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Authenticity Guaranteed</h3>
            <p class="text-gray-600">Every watch we sell is 100% authentic and comes with manufacturer warranty and certification.</p>
          </div>
          <div class="text-center p-6">
            <div class="w-16 h-16 bg-gold-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Expert Service</h3>
            <p class="text-gray-600">Our certified watchmakers provide professional maintenance and repair services.</p>
          </div>
          <div class="text-center p-6">
            <div class="w-16 h-16 bg-gold-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Customer First</h3>
            <p class="text-gray-600">Your satisfaction is our priority. We offer personalized service and support.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-luxury-black text-white py-16">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
          <div>
            <div class="text-4xl font-bold text-gold-500 mb-2">50+</div>
            <div class="text-gray-400">Premium Brands</div>
          </div>
          <div>
            <div class="text-4xl font-bold text-gold-500 mb-2">10,000+</div>
            <div class="text-gray-400">Happy Customers</div>
          </div>
          <div>
            <div class="text-4xl font-bold text-gold-500 mb-2">8 Years</div>
            <div class="text-gray-400">In Business</div>
          </div>
          <div>
            <div class="text-4xl font-bold text-gold-500 mb-2">100%</div>
            <div class="text-gray-400">Authentic</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Team Section -->
    <div class="container mx-auto px-4 py-16">
      <h2 class="text-3xl font-serif text-center mb-12">Meet Our Team</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
          <div class="aspect-square bg-gray-200">
            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400" alt="CEO" class="w-full h-full object-cover" />
          </div>
          <div class="p-6 text-center">
            <h3 class="font-semibold text-lg">Rajesh Sharma</h3>
            <p class="text-gray-500">Founder & CEO</p>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
          <div class="aspect-square bg-gray-200">
            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400" alt="Manager" class="w-full h-full object-cover" />
          </div>
          <div class="p-6 text-center">
            <h3 class="font-semibold text-lg">Priya Thapa</h3>
            <p class="text-gray-500">Store Manager</p>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
          <div class="aspect-square bg-gray-200">
            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400" alt="Watchmaker" class="w-full h-full object-cover" />
          </div>
          <div class="p-6 text-center">
            <h3 class="font-semibold text-lg">Bikash Gurung</h3>
            <p class="text-gray-500">Master Watchmaker</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// No additional script needed for static content
</script>
