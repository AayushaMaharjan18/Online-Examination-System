<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-12">
      <h1 class="text-3xl font-serif mb-8">Shipping Information</h1>

      <div class="max-w-3xl mx-auto space-y-8">
        <!-- Shipping Areas -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-xl font-semibold mb-4">Shipping Areas</h2>
          <p class="text-gray-600 mb-4">We ship to all major cities and towns across Nepal:</p>
          <ul class="list-disc list-inside text-gray-600 space-y-2">
            <li>Kathmandu Valley (1-2 business days)</li>
            <li>Pokhara (2-3 business days)</li>
            <li>Chitwan (2-3 business days)</li>
            <li>Biratnagar (3-4 business days)</li>
            <li>Other major cities (3-5 business days)</li>
            <li>Remote areas (5-7 business days)</li>
          </ul>
        </div>

        <!-- Shipping Costs -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-xl font-semibold mb-4">Shipping Costs</h2>
          <table class="w-full text-left">
            <thead>
              <tr class="border-b">
                <th class="py-2 font-medium">Area</th>
                <th class="py-2 font-medium">Cost</th>
              </tr>
            </thead>
            <tbody class="text-gray-600">
              <tr class="border-b">
                <td class="py-2">Kathmandu Valley</td>
                <td class="py-2">Free</td>
              </tr>
              <tr class="border-b">
                <td class="py-2">Major Cities</td>
                <td class="py-2">Rs. 150</td>
              </tr>
              <tr class="border-b">
                <td class="py-2">Other Areas</td>
                <td class="py-2">Rs. 250</td>
              </tr>
              <tr>
                <td class="py-2">Remote Areas</td>
                <td class="py-2">Rs. 350</td>
              </tr>
            </tbody>
          </table>
          <p class="text-sm text-gray-500 mt-4">Free shipping on orders above Rs. 10,000</p>
        </div>

        <!-- Delivery Process -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-xl font-semibold mb-4">Delivery Process</h2>
          <ol class="list-decimal list-inside text-gray-600 space-y-2">
            <li>Order confirmation via email/SMS</li>
            <li>Order processing (1-2 business days)</li>
            <li>Shipping via courier partner</li>
            <li>Tracking information sent to customer</li>
            <li>Delivery to your address</li>
          </ol>
        </div>

        <!-- Important Notes -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-xl font-semibold mb-4">Important Notes</h2>
          <ul class="list-disc list-inside text-gray-600 space-y-2">
            <li>Delivery times are estimates and may vary</li>
            <li>Someone must be available to receive the package</li>
            <li>Please provide accurate contact information</li>
            <li>For remote areas, additional delivery time may be required</li>
            <li>We are not responsible for delays caused by courier partners</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// No additional script needed
</script>
