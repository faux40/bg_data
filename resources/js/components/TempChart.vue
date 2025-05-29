<script setup>
import { onMounted, ref, watch, computed } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({ data: Array })

const canvas = ref(null)
let chart = null

// const toF = (c) => (c * 9) / 5 + 32
const HEAT_WARNING_THRESHOLD = 78

// Dynamically limit data points
const isSmall = window.innerWidth < 640
const displayCount = isSmall ? 20 : 100
const trimmed = computed(() => [...props.data.slice(-displayCount)].reverse())

const latestHeatIndex = computed(() => trimmed.value.at(-1)?.heat_index_f ?? null)
const showHeatWarning = computed(() => latestHeatIndex.value !== null && latestHeatIndex.value > HEAT_WARNING_THRESHOLD)

onMounted(renderChart)
watch(() => props.data, renderChart, { deep: true })

function renderChart() {
  if (chart) chart.destroy()

  const labels = trimmed.value.map(row => new Date(row.created_at).toLocaleTimeString())
  const temperatureF = trimmed.value.map(row => row.temperature_f)
  const humidity = trimmed.value.map(row => row.humidity)
  const heatIndex = trimmed.value.map(row => row.heat_index_f)

  const backgroundPlugin = {
    id: 'customBackgroundColor',
    beforeDraw(chart) {
      const ctx = chart.canvas.getContext('2d')
      ctx.save()
      ctx.fillStyle = showHeatWarning.value ? 'rgba(255, 0, 0, 0.1)' : 'white'
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
          label: 'Temp (°F)',
          data: temperatureF,
          borderColor: 'orange',
          borderDash: [],
          yAxisID: 'y1',
          tension: 0.4,
        },
        {
          label: 'Humidity (%)',
          data: humidity,
          borderColor: 'blue',
          yAxisID: 'y2',
          tension: 0.4,
        },
        {
          label: 'Heat Index (°F)',
          data: heatIndex,
          borderColor: 'red',
          borderDash: [6, 4],
          yAxisID: 'y1',
          tension: 0.4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      scales: {
        y1: {
          type: 'linear',
          position: 'left',
          title: { display: true, text: 'Temp / Heat Index (°F)' },
        },
        y2: {
          type: 'linear',
          position: 'right',
          title: { display: true, text: 'Humidity (%)' },
          grid: { drawOnChartArea: false },
        },
      },
    },
    plugins: [backgroundPlugin],
  })
}
</script>

<template>
  <div class="w-full my-8 border-b border-black pb-4">
    <span>Latest Heat Index = {{ latestHeatIndex }} <span class="text-sm ml-4 italic">(Trigger: {{ HEAT_WARNING_THRESHOLD }})</span></span>
    <div v-if="showHeatWarning" class="bg-red-600 text-white text-center font-bold py-2 mb-2">
      ⚠️ High Indoor Heat Rules In Effect
    </div>
    <div class="h-64 w-full">
      <canvas ref="canvas"></canvas>
    </div>
  </div>
</template>

