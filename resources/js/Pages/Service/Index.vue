<script setup>
import { onMounted } from "vue";
import Swiper from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { Navigation, Pagination } from 'swiper/modules';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const initSwiper = () => {
    new Swiper('.swiper', {
        modules: [Navigation, Pagination],
        pagination: {
            el: '.swiper-pagination',
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
}


const props = defineProps({
    services: Object,
});

onMounted(initSwiper);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="grid grid-cols-1 gap-x-8 gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-3" v-if="services.data.length > 0">
                    <div v-for="service in services.data" :key="service.id"
                         class="max-w-sm bg-white border border-gray-200 rounded-lg shadow overflow-hidden">
                        <div>
                            <div class="swiper">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide h-full">
                                        <img class="h-full object-cover" :src="`/storage/${ service.cover.file_path }`"  alt="" />
                                    </div>
                                    <div class="swiper-slide h-full" v-for="(image, index) in service.images" :key="index">
                                        <img class="h-full object-cover" :src="`/storage/${ image.file_path }`"  alt="" />
                                    </div>
                                </div>
                                <div class="swiper-pagination"></div>
                                <div class="swiper-button-prev">
                                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                                    </svg>
                                </div>
                                <div class="swiper-button-next">
                                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-5">
                            <a :href="`/services/${service.id}`">
                                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                    {{ service.service_title }}
                                </h5>
                            </a>
                            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                                {{ service.service_description }}
                            </p>
                            <hr>
                            <div class="mt-2 flex gap-2 items-center">
                                <div>
                                    <img class="w-10 h-10 rounded-full" :src="`/storage/${ service.user.details.avatar }`" alt="">
                                </div>
                                <div class="flex flex-col">
                                    <a class="text-sm font-medium text-blue-600" :href="`/user/${service.user.login}`">
                                        {{ service.user.details ? service.user.details.name : service.user.login }}
                                    </a>
                                    <p v-if="service.user.details" class="text-sm text-gray-800">{{ service.user.details.profession }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                service index
                <pre>
                    {{services}}
                </pre>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.swiper-button-next, .swiper-button-prev {
    width: 22px;
    height: 22px;
    background: #1F2937;
    border-radius: 100%;
    padding: 2px;
}
.swiper-button-next::after, .swiper-button-prev::after {
    display: none;
}
.swiper-pagination-bullet-active {
    background: #1F2937;
}
</style>
