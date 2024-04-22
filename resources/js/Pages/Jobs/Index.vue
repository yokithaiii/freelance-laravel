<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';

const props = defineProps({
    jobs: Array,
})
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 flex flex-col gap-4">
                        <Card
                            v-for="job in jobs"
                            :key="job.id"
                            :id="job.id"
                            :title="job.title"
                            :description="job.description"
                            :category="job.category ? job.category.name : 'Категория не указана'"
                            :sub_category="job.sub_category ? job.sub_category.name : null"
                            :price="job.price"
                            :price_in_hour_flag="job.price_in_hour_flag"
                            :date_published="job.created_at_for_humans"
                            :date_deadline="job.date_deadline"
                            :user="job.user"
                            :auth_id="job.auth_id"
                            :files="job.images"
                            :count="job.count"
                        >
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
