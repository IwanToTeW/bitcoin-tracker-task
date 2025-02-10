<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import Button from "primevue/button"
import SelectButton from 'primevue/selectbutton';
import {ref, defineProps, onBeforeMount, onMounted, computed} from "vue";
import LineChart from "@/Components/LineChart.vue";
import {format, addDays, subDays, addWeeks, subWeeks, startOfWeek, isAfter, isToday} from "date-fns";

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
const today = startOfWeek(new Date(), { weekStartsOn: 1 });

const isDayView = computed(() => view.value?.value === 'day');
const isNextDisabled = computed(() => isAfter(currentDate.value, today) || isToday(currentDate.value));

const currentDate = ref( isDayView ? new Date() : startOfWeek(new Date(), { weekStartsOn: 1 }));

onBeforeMount(() => {
    currencyPair.value = pairs.data.find(pair => pair.value === queryParams.pair) ?? pairs.data[0];
    view.value = intervals.data.find(interval => interval.value === queryParams.interval) ?? intervals.data[0];
})

const onViewChange = () => {
    refreshData();
}

const onPairChange = () => {
    refreshData();
}

const refreshData = () => {
    router.visit(route('dashboard', {
        pair: currencyPair.value.value,
        interval: view.value.value,
        date: format(currentDate.value, 'yyyy-MM-dd')
    }), {
        preserveState: true,
        preserveScroll: true,
    });
}

const previousDay = () => {
    currentDate.value = subDays(currentDate.value, 1);
    refreshData();
};

const nextDay = () => {
    currentDate.value = addDays(currentDate.value, 1);
    refreshData();
};

const previousWeek = () => {
    currentDate.value = startOfWeek(subWeeks(currentDate.value, 1), { weekStartsOn: 1 });
    refreshData();
};

const nextWeek = () => {
    currentDate.value = startOfWeek(addWeeks(currentDate.value, 1), { weekStartsOn: 1 });
    refreshData();
};

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
                                <Button class="w-32" @click="isDayView ? previousDay() : previousWeek()">
                                    <span class="pi pi-arrow-left"></span>
                                    Previous
                                </Button>
                                <Button :disabled="isNextDisabled" class="w-32" @click="isDayView ? nextDay() : nextWeek()">
                                    Next
                                    <span class="pi pi-arrow-right"></span>
                                </Button>

                                <div v-text="format(currentDate, 'dd/MM/yyyy')" class="text-gray-700 ml-4 mt-2">
                                </div>
                            </div>
                            <div class="flex gap-x-2">
                                <SelectButton v-model="currencyPair" optionLabel="label" dataKey="value"
                                              @value-change="onPairChange"
                                              :options="pairs.data"/>
                                <SelectButton v-model="view" optionLabel="label" dataKey="value"
                                              @value-change="onViewChange"
                                              :options="intervals.data"/>
                            </div>
                        </div>

                        <LineChart class="mt-5"  :key="data" :labels="labels" :data="data"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
