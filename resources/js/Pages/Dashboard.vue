<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import Button from "primevue/button"
import SelectButton from 'primevue/selectbutton';
import {ref, defineProps, onBeforeMount, onMounted} from "vue";
import { Chart, LineController, LineElement, PointElement, LinearScale, Title, Tooltip, CategoryScale } from 'chart.js';
import LineChart from "@/Components/LineChart.vue";
Chart.register(LineController, LineElement, PointElement, LinearScale, Title, Tooltip, CategoryScale);

const {data, queryParams, pairs, intervals} = defineProps({
    'data': {
        type: Array,
        required: true,
    },
    'queryParams': {
        type: Object,
        required: true,
    },
    'pairs': {
        type: Object,
        required: true,
    },
    'intervals': {
        type: Object,
        required: true,
    },
    'labels': {
        type: Array,
        required: true,
    },
})

const currencyPair = ref('');
const view = ref('');
const chartRef = ref(null);

onBeforeMount(() => {
    console.log('before mount')
    currencyPair.value = queryParams.pair;
    view.value = queryParams.interval;
})


</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between">
                            <div class="flex gap-x-2">
                                <Button class="w-32">
                                    <span class="pi pi-arrow-left"></span>
                                    Previous
                                </Button>
                                <Button class="w-32">
                                    Next
                                    <span class="pi pi-arrow-right"></span>
                                </Button>
                            </div>
                            <div class="flex gap-x-2">
                                <SelectButton v-model="currencyPair" optionLabel="label" dataKey="value"
                                              :options="pairs.data" :default-value="pairs.data[0]"/>
                                <SelectButton v-model="view" optionLabel="label" dataKey="value"
                                              :options="intervals.data" :default-value="intervals.data[0]"/>
                            </div>
                        </div>

                        <LineChart :labels="labels"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
