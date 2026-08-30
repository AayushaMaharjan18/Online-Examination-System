import { defineStore } from 'pinia'
import type { Product } from '~/types'
import { useApi } from '~/composables/useApi'

const STORAGE_KEY = 'wishlist_state'

interface WishlistStorage {
  productIds: number[]
  products: Product[]
}

export const useWishlistStore = defineStore('wishlist', {
  state: () => ({
    itemIds: [] as number[],
    products: [] as Product[],
    loading: false,
    error: null as string | null,
  }),

  getters: {
    count: (state) => state.itemIds.length,
    isEmpty: (state) => state.itemIds.length === 0,
    isInWishlist: (state) => (productId: number) => state.itemIds.includes(productId),
  },

  actions: {
    // Persist both the ids and full products so the UI can render instantly
    // after a refresh without waiting on the network.
    persist() {
      if (!import.meta.client) return
      try {
        const payload: WishlistStorage = {
          productIds: this.itemIds,
          products: this.products,
        }
        localStorage.setItem(STORAGE_KEY, JSON.stringify(payload))
      } catch (error) {
        console.warn('Failed to persist wishlist state:', error)
      }
    },

    // Restore the cached copy on the client (before/without the async load).
    hydrateFromStorage() {
      if (!import.meta.client) return
      try {
        const raw = localStorage.getItem(STORAGE_KEY)
        if (!raw) return
        const parsed = JSON.parse(raw) as WishlistStorage
        if (Array.isArray(parsed?.productIds)) this.itemIds = parsed.productIds
        if (Array.isArray(parsed?.products)) this.products = parsed.products
      } catch (error) {
        console.warn('Failed to hydrate wishlist state:', error)
      }
    },

    clear() {
      this.itemIds = []
      this.products = []
      this.error = null
      if (import.meta.client) {
        try {
          localStorage.removeItem(STORAGE_KEY)
        } catch (error) {
          console.warn('Failed to clear wishlist state:', error)
        }
      }
    },

    async load() {
      if (this.loading) return
      this.loading = true
      this.error = null

      // Seed from cache first so the badge/page aren't blank during fetch.
      if (this.itemIds.length === 0) {
        this.hydrateFromStorage()
      }

      try {
        const api = useApi()
        const response = await api.get<{ data: Product[] }>('/v1/wishlist')
        this.products = Array.isArray(response?.data) ? response.data : []
        this.itemIds = this.products.map((p) => p.id)
        this.persist()
        return this.products
      } catch (error: any) {
        this.error = error?.message || 'Failed to load wishlist'
        // Keep the cached copy so the UI isn't blanked out on a transient failure.
        if (this.products.length === 0) this.hydrateFromStorage()
        console.error('Failed to load wishlist:', error)
        return this.products
      } finally {
        this.loading = false
      }
    },

    async toggle(product: Product) {
      if (this.isInWishlist(product.id)) {
        return this.remove(product.id)
      }
      return this.add(product)
    },

    async add(product: Product) {
      // Client-side duplicate guard (mirrors the DB unique index).
      if (this.isInWishlist(product.id)) return

      this.error = null

      // Optimistic update: give instant UI feedback (badge fills, count bumps).
      this.itemIds.push(product.id)
      if (!this.products.some((p) => p.id === product.id)) {
        this.products.push(product)
      }
      this.persist()

      try {
        const api = useApi()
        await api.post('/v1/wishlist', { product_id: product.id })
        return true
      } catch (error: any) {
        // Roll back optimistic update on failure so state stays truthful.
        this.itemIds = this.itemIds.filter((id) => id !== product.id)
        this.products = this.products.filter((p) => p.id !== product.id)
        this.persist()
        this.error = error?.message || 'Failed to add to wishlist'
        console.error('Failed to add to wishlist:', error)
        throw error
      }
    },

    async remove(productId: number) {
      if (!this.isInWishlist(productId)) return

      this.error = null
      const removedProduct = this.products.find((p) => p.id === productId)

      // Optimistic update: item disappears from UI instantly.
      this.itemIds = this.itemIds.filter((id) => id !== productId)
      this.products = this.products.filter((p) => p.id !== productId)
      this.persist()

      try {
        const api = useApi()
        await api.delete(`/v1/wishlist/${productId}`)
        return true
      } catch (error: any) {
        // Roll back on failure.
        if (!this.isInWishlist(productId)) this.itemIds.push(productId)
        if (removedProduct && !this.products.some((p) => p.id === productId)) {
          this.products.push(removedProduct)
        }
        this.persist()
        this.error = error?.message || 'Failed to remove from wishlist'
        console.error('Failed to remove from wishlist:', error)
        throw error
      }
    },
  },
})

