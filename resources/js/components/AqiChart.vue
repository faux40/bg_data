<script setup>
import { onMounted, ref, watch, computed } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
  data: Array,
})

const reversed = [...props.data].reverse()

const canvas = ref(null)
let chart = null

const badAir = 3;
const crapAir = 5;

// const latestPm25 = computed(() => {
//   if (!props.data?.length) return null
//   return props.data[props.data.length - 1].pm2_5_std ?? null
// })

// const latestPm25 = computed(() => {
//   if (!props.data?.length) return 0 // fallback to 0 if no data
//   return props.data.at(-1)?.pm2_5_std ?? 0
// })

const latestPm25 = computed(() => reversed.at(-1)?.pm2_5_std ?? null)

// const showHeatWarning = computed(() => latestHeatIndex.value > HEAT_INDEX_ALERT_THRESHOLD)


// const showAQIWarning = computed(() => latestPm25.value > 2 && latestPm25.value <= 3)
// const showSevereAQIWarning = computed(() => latestPm25.value > 3)


const isBadAir = computed(() => latestPm25.value >= badAir)
const isCrapAir = computed(() => latestPm25.value >= crapAir)


// const showAQIWarning = computed(() => {
//   if (latestPm25.value == null) return false
//   return latestPm25.value > badAir
// })

// const showSevereAQIWarning = computed(() => {
//   if (latestPm25.value == null) return false
//   return latestPm25.value > crapAir
// })


onMounted(() => renderChart())
watch(() => props.data, () => renderChart(), { deep: true })

function renderChart() {
  if (chart) chart.destroy()

    // const labels = props.data.map(row => new Date(row.created_at).toLocaleTimeString())
    // const pm1 = props.data.map(row => row.pm1_0_std)
    // const pm25 = props.data.map(row => row.pm2_5_std)
    // const pm10 = props.data.map(row => row.pm10_0_std)

    const labels = props.data.map(row => new Date(row.created_at).toLocaleTimeString()).reverse()
    const pm1 = props.data.map(row => row.pm1_0_std).reverse()
    const pm25 = props.data.map(row => row.pm2_5_std).reverse()
    const pm10 = props.data.map(row => row.pm10_0_std).reverse()

  const backgroundPlugin = {
    id: 'aqiBackground',
    beforeDraw: (chart) => {
      const ctx = chart.canvas.getContext('2d')
      ctx.save()
      if (isCrapAir.value) {
        ctx.fillStyle = 'rgba(255, 0, 0, 0.1)' // red
      } else if (isBadAir.value) {
        ctx.fillStyle = 'rgba(255, 165, 0, 0.1)' // orange
      } else {
        ctx.fillStyle = 'white'
      }
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
        },
        {
          label: 'PM2.5',
          data: pm25,
          borderColor: 'green',
  borderWidth: 4, 
          tension: 0.4,
        },
        {
          label: 'PM10',
          data: pm10,
          borderColor: 'brown',
  borderWidth: 1, 
          tension: 0.4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
    },
    plugins: [backgroundPlugin],
  })
}
</script>

<template>
  <div class="w-full">
        <span v-if="true">Latest AQI PM2.5 = {{ latestPm25 ?? '—' }}, Not Good = {{ badAir }} {{ isBadAir }}, Crap = {{ crapAir }} {{ isCrapAir }}</span>
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