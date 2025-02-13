<script setup>
import { ref, onMounted } from 'vue';
import { Chart, LineController, LineElement, PointElement, LinearScale, Title, Tooltip, CategoryScale, Filler } from 'chart.js';

const {labels, data} = defineProps({
    labels: {
        type: Array,
        required: true,
    },
    data: {
        type: Array,
        required: true,
    },
});
// Register required components
Chart.register(LineController, LineElement, PointElement, LinearScale, Title, Tooltip, CategoryScale, Filler);

const chartRef = ref(null);
let chartInstance = null;

const chartData = {
    labels: labels,
    datasets: [
        {
            label: 'Sales Data',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            data: data,
            fill: true,
        },
    ],
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        title: {
            display: true,
            text: 'Bitcoin Price Chart',
        },
        tooltip: {
            enabled: true,
        },
    },
    scales: {
        x: {
            beginAtZero: true,
        },
        y: {
            beginAtZero: false,
        },
    },
};

onMounted(() => {
    if (chartRef.value) {
        chartInstance = new Chart(chartRef.value, {
            type: 'line',
            data: chartData,
            options: chartOptions,
        });
    }
});
</script>

<template>
    <div class="chart-container" style="position: relative; height: 400px;">
        <canvas ref="chartRef"></canvas>
    </div>
</template>
