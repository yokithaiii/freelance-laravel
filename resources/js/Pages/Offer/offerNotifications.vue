<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    offers: Array,
});

const form = useForm({
    offer_id: '',
    offer_from_id: null,
});

const submitAccept = (offerId, offerUserId) => {
    form.offer_id = offerId;
    form.offer_from_id = offerUserId;
    form.post(route('offer.offerAccept'));
};

const submitDecline = (offerId) => {
    form.offer_id = offerId;
    form.post(route('offer.offerDecline'));
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div v-if="offers.length === 0" class="container mx-auto py-8">
                    <div class="w-full">
                        <div class="bg-white shadow rounded-lg p-6">
                            <p>Offers not found.</p>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="text-gray-900 flex flex-col gap-4">

                        <ul role="list" class="max-w divide-y divide-gray-200 dark:divide-gray-700">
                            <li v-for="offer in offers" :key="offer.id" class="py-4 px-4 relative">
                                <div class="flex items-start space-x-4 rtl:space-x-reverse">
                                    <div class="flex-shrink-0">
                                        <img v-if="offer.user.detail_info.avatar" class="w-12 h-12 rounded-full" :src="`/storage/${ offer.user.detail_info.avatar }`" alt="Neil image">
                                        <div v-else class="relative w-14 h-14 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                            <svg class="absolute w-16 h-16 text-gray-400 -left-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">
                                            Исполнитель
                                            <a class="text-blue-600" :href="`/user/${ offer.user.login }`">
                                                {{ offer.user.detail_info.name ? offer.user.detail_info.name : offer.user.login }}
                                            </a>
                                        </p>
                                        <p class="text-sm font-medium text-gray-900">
                                            предлагает свою услугу на Ваш заказ:
                                            <a class="text-blue-600" :href="`/jobs/${ offer.job.id }`">{{ offer.job.title }}</a>
                                        </p>
                                        <br>
                                        <p class="text-sm font-medium text-gray-900">
                                            Предложение:
                                            {{ offer.offer_description }}
                                            <br>
                                            Цена:
                                            {{ offer.offer_price }}
                                            <br>
                                            Срок:
                                            {{ offer.offer_date_deadline_days }} дней
                                        </p>
                                    </div>
                                </div>
                                <div v-if="offer.offer_status != 'Принят' && offer.offer_status != 'Отклонено'" class="absolute bottom-4 right-4 flex items-center space-x-3 text-base font-semibold text-gray-900 justify-end">
                                    <button @click.prevent="submitDecline(offer.id)" class="inline-flex items-center px-4 py-2 bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150 ms-4">
                                        Отклонить
                                    </button>
                                    <button @click.prevent="submitAccept(offer.id, offer.user.id)" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150 ms-4">
                                        Принять
                                    </button>
                                </div>
                                <div v-else class="absolute bottom-4 right-4 flex items-center space-x-3 rtl:space-x-reverse text-base font-semibold text-gray-900 justify-end">
                                    <button
                                        disabled
                                        :class="{ 'bg-green-700': offer.offer_status === 'Принят', 'bg-red-700': offer.offer_status === 'Отклонено' }"
                                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase ms-4"
                                    >
                                        {{ offer.offer_status }}
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
