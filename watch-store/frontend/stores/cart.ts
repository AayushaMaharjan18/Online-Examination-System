import { defineStore } from 'pinia'
import type { CartItem, Product } from '~/types'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [] as CartItem[],
    sessionId: null as string | null,
  }),

  getters: {
    totalItems: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
    subtotal: (state) => state.items.reduce((sum, item) => sum + (item.product.final_price * item.quantity), 0),
    isEmpty: (state) => state.items.length === 0,
  },

  actions: {
    setSessionId() {
      if (!this.sessionId && import.meta.client) {
        let sid = localStorage.getItem('cart_session_id')
        if (!sid) {
          sid = 'sess_' + Math.random().toString(36).substring(2, 15)
          localStorage.setItem('cart_session_id', sid)
        }
        this.sessionId = sid
      }
    },

    addItem(product: Product, quantity: number = 1) {
      const existing = this.items.find(i => i.product_id === product.id)
      if (existing) {
        existing.quantity += quantity
      } else {
        this.items.push({
          id: Date.now(),
          product_id: product.id,
          quantity,
          product,
        })
      }
    },

    updateQuantity(productId: number, quantity: number) {
      const item = this.items.find(i => i.product_id === productId)
      if (item) {
        item.quantity = quantity
      }
    },

    removeItem(itemId: number) {
      this.items = this.items.filter(i => i.id !== itemId)
    },

    clearCart() {
      this.items = []
    },
  },
})
