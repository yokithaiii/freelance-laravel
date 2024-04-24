<script setup>
const props = defineProps({
    job: Object,
});
</script>

<template>
    <div class="">
        <div class="flex justify-between items-start mb-2 text-gray-500">
            <div>
                <span v-for="category in job.selected_categories" :key="category.id" class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-400 border border-gray-500">
                    {{ category.name }}
                </span>
            </div>
            <div class="flex flex-col gap-2 items-end">
                <span v-if="job.price_in_hour_flag" class="text-2xl text-gray-900 dark:text-white">Бюджет: {{ job.price }} ₽/час</span>
                <span v-else class="text-2xl text-gray-900 dark:text-white">Бюджет: {{ job.price }} ₽</span>
                <span>Срок до: {{ job.date_deadline }}</span>
            </div>
        </div>
        <div class="flex flex-col" style="margin-top: -35px">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                <a :href="`/jobs/${ job.id }`">
                    {{ job.title }}
                </a>
            </h5>
            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                {{ job.description }}
            </p>
        </div>
        <div class="flex flex-col gap-1" v-if="job.images.length > 0">
            <p>Прикрепленные файлы:</p>
            <div v-for="file in job.images" :key="file.id">
                <a class="flex gap-1 items-center" :href="`/storage/${ file.file_path }`" target="_blank">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7Zm.394 9.553a1 1 0 0 0-1.817.062l-2.5 6A1 1 0 0 0 8 19h8a1 1 0 0 0 .894-1.447l-2-4A1 1 0 0 0 13.2 13.4l-.53.706-1.276-2.553ZM13 9.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-blue-700">{{file.file_name}}</span>
                </a>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-start space-x-4">
                <img v-if="job.user.detail_info" class="w-14 h-14 rounded-sm"  :src="`/storage/${ job.user.detail_info.avatar }`" alt="">
                <div v-else class="relative w-14 h-14 overflow-hidden bg-gray-100 rounded-sm dark:bg-gray-600">
                    <svg class="absolute w-16 h-16 text-gray-400 -left-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                </div>
                <div class="flex flex-col">
                    <span class="font-medium dark:text-white">
                        Заказчик: <a :href="`/user/${ job.user.login }`" class="text-blue-700">{{ job.user.login }}</a>
                        <span v-if="job.user.id === job.auth_id" class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400">
                            Это вы
                        </span>
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-end justify-between">
            <span class="text-sm">Заказ размещен: {{ job.created_at_for_humans }}</span>
            <div>
                <a v-if="job.user.id !== job.auth_id" :href="`/offer/job=${ job.id }`" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-normal text-md text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-4">
                    Предложить услугу
                    <svg class="w-6 h-6 text-white ms-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>