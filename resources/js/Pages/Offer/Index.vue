<script setup>
import { ref, onMounted } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    offers: Array,
});

const formatDate = (inputDate) => {
    const date = new Date(inputDate);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return day + '.' + month + '.' + year;
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
                            <p>Предложения не найдены.</p>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="text-gray-900 flex flex-col gap-4">
<!--                        <pre>-->
<!--                            {{ offers }}-->
<!--                        </pre>-->

                        <ul role="list" class="max-w divide-y divide-gray-200 dark:divide-gray-700">
                            <li v-for="offer in offers" :key="offer.id" class="py-4 px-4 hover:bg-gray-100">
                                <a :href="`/jobs/${ offer.job.id }`">
                                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                        <div class="flex-shrink-0">
                                            <img v-if="offer.job.user.detail_info.avatar" class="w-12 h-12 rounded-full" :src="`/storage/${ offer.job.user.detail_info.avatar }`">
                                            <div v-else class="relative w-14 h-14 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                                <svg class="absolute w-16 h-16 text-gray-400 -left-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                Дата: {{ formatDate(offer.created_at) }}
                                            </p>
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                Название заказа: {{  offer.job.title }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                Заказчик: {{ offer.job.user.detail_info.name ? offer.job.user.detail_info.name : offer.job.user.login }}
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-3 rtl:space-x-reverse inline-flex items-center text-base font-semibold text-gray-900 text-white">
                                            <span v-if="offer.offer_status == 'Ожидается'" class="inline-flex items-center bg-yellow-100 text-xs font-medium px-2.5 py-0.5 rounded-full bg-yellow-900 text-yellow-300">
                                                <span class="w-2 h-2 me-1 bg-yellow-500 rounded-full"></span>
                                                Ожидается
                                            </span>
                                            <span v-if="offer.offer_status == 'Принят'" class="inline-flex items-center bg-green-100 text-xs font-medium px-2.5 py-0.5 rounded-full bg-green-900 text-green-300">
                                                <span class="w-2 h-2 me-1 bg-green-500 rounded-full"></span>
                                                Принят
                                            </span>
                                            <span v-if="offer.offer_status == 'Отклонено'" class="inline-flex items-center bg-red-100 text-xs font-medium px-2.5 py-0.5 rounded-full bg-red-900 text-red-300">
                                                <span class="w-2 h-2 me-1 bg-red-500 rounded-full"></span>
                                                Отклонено
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
