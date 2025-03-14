<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import Button from "primevue/button"
import SelectButton from 'primevue/selectbutton';
import Select from 'primevue/select';
import Toast from 'primevue/toast';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import {ref, defineProps, onBeforeMount, onMounted, computed} from "vue";
import LineChart from "@/Components/LineChart.vue";
import {useToast} from 'primevue/usetoast';

import {
    format,
    addDays,
    subDays,
    addWeeks,
    subWeeks,
    startOfWeek,
    isAfter,
    isToday, endOfWeek, isSameDay,
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
const toast = useToast();

const currencyPair = ref('');
const showSubscribeModal = ref(false);
const view = ref('');
const defaultForm = {
    email: '',
    price: '',
    percentage: 0,
    period: 0,
    errors: {},
}
const form = ref(defaultForm);

const today = new Date();

const isDayView = computed(() => view.value?.value === 'day');
const currentDate = ref(isDayView ? new Date(queryParams.date) : today);

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

const isNextDisabled = () => {

    if (isDayView.value) {
        return isToday(currentDate.value) || isAfter(currentDate.value, today);
    }

    return today <= endOfWeek(currentDate.value, {weekStartsOn: 1});
}

const getLabel = () => {
    if (isDayView.value) {
        return format(currentDate.value,'dd/MM/yy')
    }

    const weekStart = startOfWeek(currentDate.value, {weekStartsOn: 1});
    const weekEnd = endOfWeek(currentDate.value, {weekStartsOn: 1});

    const hasWeekEnded = isAfter(new Date(), weekEnd);

    if(hasWeekEnded) {
        return `${format(weekStart, 'dd/MM/yy')} - ${format(weekEnd, 'dd/MM/yy')}`
    }
    return `${format(subWeeks(new Date(), 1), 'dd/MM/yy')} - ${format(new Date(),'dd/MM/yy')}`

}
const subscribe = () => {
    axios.post(route('api.v1.subscriptions.store'), {
        ...form.value,
        pair: currencyPair.value.value,
    }).then(response => {
        if (response.data.success) {
            toast.add({severity: 'success', summary: 'Success', detail: 'You have successfully subscribed', life: 3000})
            form.value.errors = [];
            form.value = defaultForm;
        } else {
            toast.add({severity: 'error', summary: 'Error', detail: 'Something went wrong', life: 3000})
        }
    }).catch(error => {
        toast.add({severity: 'error', summary: 'Error', detail: 'Something went wrong', life: 3000})
        if (error.response.status === 422) {
            form.value.errors = error.response.data.errors;
        }
    });
}
</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <Toast/>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between">
                            <div class="flex gap-x-2">
                                <Button class="w-32" @click="isDayView ? previousDay() : previousWeek()">
                                    <span class="pi pi-arrow-left"></span>
                                    Previous
                                </Button>
                                <Button :disabled="isNextDisabled()" class="w-32"
                                        @click="isDayView ? nextDay() : nextWeek()">
                                    Next
                                    <span class="pi pi-arrow-right"></span>
                                </Button>

                                <div v-text="getLabel()" class="text-gray-700 ml-4 mt-2">
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
                            <div class="flex items-center gap-4">
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
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 mb-4">
                                    <label for="percentage" class="font-semibold w-24">Percentage</label>
                                    <div>
                                        <InputNumber v-model="form.percentage" id="percentage" class="flex-auto"
                                                     autocomplete="off" :min="0"
                                                     :max="100"/>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between gap-4 mb-4">
                                <InputError v-for="error in form.errors?.price" :message="error"></InputError>

                                <InputError v-for="error in form.errors?.percentage"
                                            :message="error"></InputError>
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
