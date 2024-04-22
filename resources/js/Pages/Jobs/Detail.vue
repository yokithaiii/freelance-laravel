<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import CardDetail from "@/Components/CardDetail.vue";

const props = defineProps({
    job: Object,
})
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Job detail</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-4">
                    <a href="/jobs" class="flex items-center gap-1">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
                        </svg>
                        Назад
                    </a>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 flex flex-col gap-4">
                        <CardDetail
                            :key="job.id"
                            :id="job.id"
                            :title="job.title"
                            :description="job.description"
                            :category="job.category ? job.category.name : 'Категория не указана'"
                            :sub_category="job.sub_category ? job.sub_category.name : null"
                            :price="job.price"
                            :price_in_hour_flag="job.price_in_hour_flag"
                            :date_published="job.created_at_for_humans"
                            :date_deadline="job.term"
                            :user="job.user"
                            :auth_id="job.auth_id"
                            :files="job.images"
                        >
                        </CardDetail>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
