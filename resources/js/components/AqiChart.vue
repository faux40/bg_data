<script setup>
import { onMounted, ref, watch } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
  data: Array,
})

const canvas = ref(null)
let chart = null

const renderChart = () => {
  if (chart) chart.destroy()

  const labels = props.data.map(row => new Date(row.created_at).toLocaleTimeString())
  const pm1 = props.data.map(row => row.pm1_0_std ?? 0)
  const pm25 = props.data.map(row => row.pm2_5_std ?? 0)
  const pm10 = props.data.map(row => row.pm10_0_std ?? 0)

  chart = new Chart(canvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'PM1.0 (std)',
          data: pm1,
          borderColor: 'green',
          tension: 0.3,
        },
        {
          label: 'PM2.5 (std)',
          data: pm25,
          borderColor: 'purple',
          tension: 0.3,
        },
        {
          label: 'PM10.0 (std)',
          data: pm10,
          borderColor: 'brown',
          tension: 0.3,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          title: { display: true, text: 'μg/m³' },
        },
      },
    },
  })
}

onMounted(renderChart)
watch(() => props.data, renderChart, { deep: true })
</script>

<template>
  <div class="w-full h-64 mt-10">
    <canvas ref="canvas"></canvas>
  </div>
</template>