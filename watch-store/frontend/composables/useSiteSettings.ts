import { ref, computed } from 'vue'

/**
 * Shared site configuration loaded from the website settings API (`/v1/footer`).
 *
 * A single module-level cache ensures the settings are fetched once and reused
 * across components (footer, product page, account page, etc.). The WhatsApp
 * number used for order/product enquiries is managed here and comes dynamically
 * from the backend settings.
 */

export interface SiteFooterData {
  brand_name: string
  brand_description: string
  facebook_url: string | null
  instagram_url: string | null
  twitter_url: string | null
  quick_links: { label: string; url: string }[]
  customer_service_links: { label: string; url: string }[]
  phone: string | null
  email: string | null
  address: string | null
  copyright_text: string | null
  payment_methods: { name: string }[]
  whatsapp_number: string | null
}

const defaultFooter: SiteFooterData = {
  brand_name: 'WATCHSTORE',
  brand_description: 'Your premier destination for luxury watches in Nepal. Authentic timepieces from world-renowned brands.',
  facebook_url: null,
  instagram_url: null,
  twitter_url: null,
  quick_links: [],
  customer_service_links: [],
  phone: null,
  email: null,
  address: null,
  copyright_text: null,
  payment_methods: [],
  whatsapp_number: null,
}

// Module-level singleton state so every consumer (footer, product page, account
// page, …) shares a single fetch and the same reactive data.
const footer = ref<SiteFooterData>({ ...defaultFooter })
const loading = ref(false)
let loadingPromise: Promise<void> | null = null

export const useSiteSettings = () => {
  const config = useRuntimeConfig()

  const fetchSettings = async () => {
    if (loadingPromise) {
      return loadingPromise
    }
    loading.value = true
    loadingPromise = (async () => {
      try {
        const { get } = useApi()
        const data = await get<SiteFooterData>('/v1/footer')
        footer.value = data
      } catch (error) {
        console.error('Failed to load site settings:', error)
      } finally {
        loading.value = false
        loadingPromise = null
      }
    })()
    return loadingPromise
  }

  /** Strip everything except digits to build a valid international wa.me number. */
  const normalizeWhatsAppNumber = (number?: string | null): string => {
    return (number || '').replace(/[^\d]/g, '')
  }

  /**
   * Build a `https://wa.me/<number>?text=<message>` link using the WhatsApp
   * number configured in the website settings. Returns null when no number is
   * configured, so callers can hide the button gracefully.
   */
  const buildWhatsAppLink = (message: string): string | null => {
    const number = normalizeWhatsAppNumber(footer.value.whatsapp_number)
    if (!number) {
      return null
    }
    return `https://wa.me/${number}?text=${encodeURIComponent(message)}`
  }

  /** Current absolute URL, only available on the client. */
  const currentUrl = () => {
    if (import.meta.client && typeof window !== 'undefined') {
      return window.location.href
    }
    return config.public.siteUrl
  }

  const whatsappNumber = computed(() => footer.value.whatsapp_number)

  return {
    footer,
    loading,
    fetchSettings,
    buildWhatsAppLink,
    whatsappNumber,
    currentUrl,
  }
}
