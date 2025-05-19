<script setup>
import { onMounted, ref, watch, computed } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({ data: Array })

const canvas = ref(null)
let chart = null

const badAir = 4
const crapAir = 8

// Trim data based on screen size
const isSmall = window.innerWidth < 640
const displayCount = isSmall ? 20 : 100
const trimmed = computed(() => [...props.data.slice(-displayCount)].reverse())

const latestPm25 = computed(() => trimmed.value.at(-1)?.pm2_5_std ?? 0.1)
const isBadAir = computed(() => latestPm25.value >= badAir)
const isCrapAir = computed(() => latestPm25.value >= crapAir)

onMounted(renderChart)
watch(() => props.data, renderChart, { deep: true })

function renderChart() {
  if (chart) chart.destroy()

  const labels = trimmed.value.map(row => new Date(row.created_at).toLocaleTimeString())
  const pm1 = trimmed.value.map(row => Math.max(row.pm1_0_std || 0.1, 0.1))
  const pm25 = trimmed.value.map(row => Math.max(row.pm2_5_std || 0.1, 0.1))
  const pm10 = trimmed.value.map(row => Math.max(row.pm10_0_std || 0.1, 0.1))

  const backgroundPlugin = {
    id: 'aqiBackground',
    beforeDraw(chart) {
      const ctx = chart.canvas.getContext('2d')
      ctx.save()
      ctx.fillStyle = isCrapAir.value
        ? 'rgba(255, 0, 0, 0.1)'
        : isBadAir.value
        ? 'rgba(255, 165, 0, 0.1)'
        : 'white'
      ctx.fillRect(0, 0, chart.width, chart.height)
      ctx.restore()
    },
  }

  chart = new Chart(canvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'PM1.0',
          data: pm1,
          borderColor: 'purple',
          borderWidth: 1,
          tension: 0.4,
          yAxisID: 'logY',
        },
        {
          label: 'PM2.5',
          data: pm25,
          borderColor: 'green',
          borderWidth: 4,
          tension: 0.4,
          yAxisID: 'logY',
        },
        {
          label: 'PM10',
          data: pm10,
          borderColor: 'brown',
          borderWidth: 1,
          tension: 0.4,
          yAxisID: 'logY',
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      scales: {
        logY: {
          type: 'logarithmic',
          position: 'left',
          min: 0.1,
          title: { display: true, text: 'AQI PM (log scale)' },
          ticks: {
            callback: (value) => ([0.1, 1, 10, 100, 1000].includes(value) ? value : ''),
          },
        },

        x: {
  type: 'category',
  title: {
    display: true,
    text: 'Time',
  },
  ticks: {
    autoSkip: true,
    maxRotation: 0,
    maxTicksLimit: isSmall ? 6 : 12,
    callback: function (val, index, ticks) {
      return labels[index]; // Shows formatted time string from `labels` array
    },
  },
},

      },
    },
    plugins: [backgroundPlugin],
  })
}
</script>

<template>
  <div class="w-full my-8 border-b border-black pb-4">
    <span>Latest AQI PM2.5 = {{ latestPm25 }} <span class="text-sm ml-4 italic">(Bad Air Trigger: {{ badAir }}, Super Bad Air Trigger: {{ crapAir }})</span></span>

    <div v-if="isCrapAir" class="bg-red-600 text-white text-center font-bold py-2 mb-2">
      ⚠️ P100 Required – Poor Air Quality
    </div>
    <div v-else-if="isBadAir" class="bg-orange-500 text-white text-center font-bold py-2 mb-2">
      ⚠️ N95 Required – Elevated Air Quality Risk
    </div>

    <div class="h-64 w-full">
      <canvas ref="canvas"></canvas>
    </div>
  </div>
</template>