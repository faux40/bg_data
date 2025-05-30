<script setup>

import { onMounted, ref } from 'vue'
import { router } from '@inertiajs/vue3'

import TempChart from '@/components/TempChart.vue'
import AqiChart from '@/components/AqiChart.vue'

import { formatDate } from '@/Utils/dateFormatter';


defineProps({ data: Array })


const REFRESH_INTERVAL = 180 // in seconds
const secondsToRefresh = ref(REFRESH_INTERVAL)

// Refresh every 180 seconds

onMounted(() => {
  setInterval(() => {
    secondsToRefresh.value--
    if (secondsToRefresh.value <= 0) {
      router.reload({
        preserveState: true,
        preserveScroll: true,
        only: ['data'],
      })
      secondsToRefresh.value = REFRESH_INTERVAL
    }
  }, 1000)
})
</script>

<template>
    <div class="mx-2 sm:mx-8 md:mx-16 lg:mx-24 my-6">
        <div class="flex justify-between">
            <h1 class="text-xl font-bold mb-4">Sensor Data (Latest 100) </h1>
            <span>Refreshing in {{ secondsToRefresh }}s...</span>
        </div>
    <TempChart :data="data" />
    <AqiChart :data="data" />

    <div class="overflow-x-auto mx-4 sm:mx-8 md:mx-16 lg:mx-24 my-6">
      <table class="min-w-full border text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-1 py-1 border">ID</th>
            <th class="px-1 py-1 border">Time</th>
            <th class="px-1 py-1 border">Device</th>
            <th class="px-1 py-1 border">Temp (°C)</th>
            <th class="px-1 py-1 border">Temp (°F)</th>
            <th class="px-1 py-1 border">Humidity (%)</th>
            <th class="px-1 py-1 border">Heat Idx (°F)</th>
            <th class="px-1 py-1 border">PM1.0</th>
            <th class="px-1 py-1 border">PM2.5</th>
            <th class="px-1 py-1 border">PM10</th>
            <th class="px-1 py-1 border">SID</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in data" :key="row.id">
            <td class="px-1 py-1 border">{{ row.id }}</td>
            <td class="px-1 py-1 border">{{ formatDate(row.created_at, 'datetime') }}</td>
            <td class="px-1 py-1 border">{{ row.device_id }}</td>
            <td class="px-1 py-1 border">{{ row.temperature }}</td>
            <td class="px-1 py-1 border">{{ row.temperature_f.toFixed(1) }}</td>
            <td class="px-1 py-1 border">{{ row.humidity }}</td>
            <td class="px-1 py-1 border">{{ row.heat_index_f.toFixed(1) }}</td>
            <td class="px-1 py-1 border">{{ row.pm1_0_std }}</td>
            <td class="px-1 py-1 border">{{ row.pm2_5_std }}</td>
            <td class="px-1 py-1 border">{{ row.pm10_0_std }}</td>
            <td class="px-1 py-1 border">{{ row.sid }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>