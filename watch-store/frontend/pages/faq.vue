<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-12">
      <h1 class="text-3xl font-serif mb-8">Frequently Asked Questions</h1>

      <div class="max-w-3xl mx-auto space-y-4">
        <div
          v-for="(faq, index) in faqs"
          :key="index"
          class="bg-white rounded-lg shadow-sm overflow-hidden"
        >
          <button
            @click="toggleFaq(index)"
            class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
          >
            <span class="font-medium">{{ faq.question }}</span>
            <svg
              class="w-5 h-5 transition-transform"
              :class="openFaq === index ? 'rotate-180' : ''"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div
            v-show="openFaq === index"
            class="px-6 pb-4 text-gray-600"
          >
            {{ faq.answer }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const openFaq = ref<number | null>(null)

const faqs = [
  {
    question: 'Are your watches authentic?',
    answer: 'Yes, all our watches are 100% authentic and come with manufacturer warranty. We source directly from authorized distributors and brand representatives.'
  },
  {
    question: 'What payment methods do you accept?',
    answer: 'We accept eSewa, Khalti, major credit/debit cards, and Cash on Delivery (COD) for orders within Kathmandu valley.'
  },
  {
    question: 'How long does delivery take?',
    answer: 'Delivery within Kathmandu valley takes 1-2 business days. For other major cities, it takes 2-5 business days depending on your location.'
  },
  {
    question: 'Do you offer international shipping?',
    answer: 'Currently, we only ship within Nepal. We are working on expanding our shipping capabilities to international destinations.'
  },
  {
    question: 'What is your return policy?',
    answer: 'We accept returns within 7 days of delivery for unused items in original packaging. Please refer to our Returns & Exchanges page for detailed information.'
  },
  {
    question: 'Do you provide warranty on watches?',
    answer: 'Yes, all watches come with manufacturer warranty ranging from 1-5 years depending on the brand. We also provide our own 1-year store warranty.'
  },
  {
    question: 'Can I track my order?',
    answer: 'Yes, once your order is shipped, you will receive a tracking number via email and SMS to track your package.'
  },
  {
    question: 'Do you offer watch servicing and repair?',
    answer: 'Yes, we have certified watchmakers who provide professional servicing, repair, and maintenance for all major watch brands.'
  },
  {
    question: 'How can I contact customer support?',
    answer: 'You can reach us via phone at +977 1-4423456, email at support@watchstore.com.np, or visit our store in Kathmandu during business hours.'
  },
  {
    question: 'Are the prices shown inclusive of VAT?',
    answer: 'Yes, all prices shown on our website are inclusive of applicable taxes and VAT.'
  }
]

function toggleFaq(index: number) {
  openFaq.value = openFaq.value === index ? null : index
}
</script>
