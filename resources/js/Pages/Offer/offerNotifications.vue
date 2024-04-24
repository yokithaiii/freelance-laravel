<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    offers: Array,
});

const form = useForm({
    offer_id: '',
});

const submitAccept = (offerId) => {
    form.offer_id = offerId;
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
                        <!-- <pre>
                            {{ offers }}
                        </pre> -->

                        <ul role="list" class="max-w divide-y divide-gray-200 dark:divide-gray-700">
                            <li v-for="offer in offers" :key="offer.id" class="py-4 px-4">
                                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                    <div class="flex-shrink-0">
                                        <img v-if="offer.user.detail_info.avatar" class="w-8 h-8 rounded-full" :src="`/storage/${ offer.user.detail_info.avatar }`" alt="Neil image">
                                        <div v-else class="relative w-14 h-14 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                            <svg class="absolute w-16 h-16 text-gray-400 -left-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            Название заказа: {{  offer.job.title }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            Исполнитель: {{ offer.user.detail_info.name ? offer.user.detail_info.name : offer.user.login }}
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-3 rtl:space-x-reverse inline-flex items-center text-base font-semibold text-gray-900 text-white">
                                        <button @click.prevent="submitDecline(offer.id)" class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-4">
                                            Отклонить
                                        </button>
                                        <button @click.prevent="submitAccept(offer.id)" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-4">
                                            Принять
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
