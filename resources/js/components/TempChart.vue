<script setup>
import { onMounted, ref, watch } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
  data: Array, // Expecting array of sensor records
})

const canvas = ref(null)
let chart = null

const toF = (c) => (c * 9) / 5 + 32

onMounted(() => renderChart())
watch(() => props.data, () => renderChart(), { deep: true })

function renderChart() {
  if (chart) chart.destroy()

  const labels = props.data.map(row => new Date(row.created_at).toLocaleTimeString())
  const temperatureF = props.data.map(row => toF(row.temperature))
  const humidity = props.data.map(row => row.humidity)
  const heatIndex = props.data.map(row => row.heat_index_f)

  chart = new Chart(canvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Temp (°F)',
          data: temperatureF,
          borderColor: 'orange',
    borderDash: [], // solid line
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
    borderDash: [6, 4], // dashed line: 6px dash, 4px gap
          yAxisID: 'y1',
          tension: 0.4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      scales: {
        y1: {
          type: 'linear',
          position: 'left',
          title: {
            display: true,
            text: 'Temperature / Heat Index (°F)',
          },
        },
        y2: {
          type: 'linear',
          position: 'right',
          title: {
            display: true,
            text: 'Humidity (%)',
          },
          grid: {
            drawOnChartArea: false, // prevents overlap
          },
        },
      },
    },
  })
}
</script>

<template>
  <div class="w-full h-64">
    <canvas ref="canvas"></canvas>
  </div>
</template>