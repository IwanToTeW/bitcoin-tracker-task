<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import Button from "primevue/button"
import SelectButton from 'primevue/selectbutton';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import {ref, defineProps, onBeforeMount, onMounted, computed} from "vue";
import LineChart from "@/Components/LineChart.vue";
import {
    format,
    addDays,
    subDays,
    addWeeks,
    subWeeks,
    startOfWeek,
    isAfter,
    isToday, endOfWeek,
} from "date-fns";
import InputError from "@/Components/InputError.vue";

const {data, queryParams, pairs, intervals, timePeriods} = defineProps({
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
    'timePeriods': {
        type: Object,
        required: true,
    },
    'labels': {
        type: Array,
        required: true,
    },
})

const currencyPair = ref('');
const showSubscribeModal = ref(false);
const view = ref('');
const form = ref({
    email: '',
    price: '',
    percentage: '',
    period: 0,
    errors: {},
});
const today = startOfWeek(new Date(), {weekStartsOn: 1});

const isDayView = computed(() => view.value?.value === 'day');
const isNextDisabled = computed(() => isAfter(currentDate.value, today) || isToday(currentDate.value));
const currentDate = ref(isDayView ? new Date() : today);

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
    currentDate.value = startOfWeek(subWeeks(currentDate.value, 1), {weekStartsOn: 1});
    refreshData();
};

const nextWeek = () => {
    currentDate.value = startOfWeek(addWeeks(currentDate.value, 1), {weekStartsOn: 1});
    refreshData();
};

const subscribe = () => {

    axios.post(route('api.v1.subscriptions.store'), {
        ...form.value,
        pair: currencyPair.value.value,
    }).then(response => {
        console.log(response.data);
    }).catch(error => {
        if (error.response.status === 422) {
            form.value.errors = error.response.data.errors;
        }
    });
}
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
                                <Button :disabled="isNextDisabled" class="w-32"
                                        @click="isDayView ? nextDay() : nextWeek()">
                                    Next
                                    <span class="pi pi-arrow-right"></span>
                                </Button>

                                <div v-text="format(currentDate,'dd/MM/yy')" class="text-gray-700 ml-4 mt-2">
                                </div>
                            </div>
                            <div class="flex gap-x-2">
                                <div class="size-8 mt-3" @click="showSubscribeModal = !showSubscribeModal">
                                    <span class="pi pi-bell hover:cursor-pointer"></span>
                                </div>

                                <SelectButton v-model="currencyPair" optionLabel="label" dataKey="value"
                                              @value-change="onPairChange"
                                              :options="pairs.data"/>
                                <SelectButton v-model="view" optionLabel="label" dataKey="value"
                                              @value-change="onViewChange"
                                              :options="intervals.data"/>
                            </div>
                        </div>

                        <LineChart v-if="data.length > 0" class="mt-5" :key="data" :labels="labels" :data="data"/>
                        <div v-else class="text-center mt-5 text-gray-600"> There is no data found for this date</div>

                        <div class="mt-10 w-1/2">
                            <span
                                class="text-surface-500 dark:text-surface-400 block mb-8">Subscribe For Notification</span>
                            <div class="flex items-center gap-4 ">
                                <label for="email" class="font-semibold w-24">Email</label>
                                <div>
                                    <InputText id="email" v-model="form.email" class="flex-auto" autocomplete="off"/>
                                    <InputError v-for="error in form.errors?.email" :message="error"></InputError>
                                </div>
                            </div>


                            <div class="flex items-center gap-4 mt-4">
                                <div class="flex items-center gap-4 mb-4">
                                    <label for="price" class="font-semibold w-24">Price Limit</label>
                                    <div>
                                        <InputText v-model="form.price" id="price" class="flex-auto"
                                                   autocomplete="off"/>
                                        <InputError v-for="error in form.errors?.price" :message="error"></InputError>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 mb-4">
                                    <label for="percentage" class="font-semibold w-24">Percentage</label>
                                    <div>

                                        <InputNumber v-model="form.percentage" id="percentage" class="flex-auto"
                                                     autocomplete="off" :min="0"
                                                     :max="100"/>
                                        <InputError v-for="error in form.errors?.percentage"
                                                    :message="error"></InputError>
                                    </div>
                                </div>

                            </div>

                            <div class="flex items-center gap-4 mb-8">
                                <label for="period" class="font-semibold w-24">Period</label>
                                <Select v-model="form.period" class="ml-5 w-full" :options="timePeriods.data"
                                        optionLabel="label"
                                        option-value="value"></Select>
                            </div>
                            <InputError v-for="error in form.errors?.period" :message="error"></InputError>

                            <div class="flex justify-end gap-2">
                                <Button type="button" label="Subscribe" @click="subscribe"></Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
