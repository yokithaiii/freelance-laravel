<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div>
                    <a href="/jobs" class="flex items-center gap-1">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
                        </svg>
                        Назад
                    </a>
                </div>
                <div v-if="!user.detail_info" class="container mx-auto py-8">
                    <div class="w-full">
                        <div class="bg-white shadow rounded-lg p-6">
                            <p>No profile information available.</p>
                        </div>
                    </div>
                </div>
                <div v-else class="container mx-auto py-8">
                    <div class="grid grid-cols-4 sm:grid-cols-12 gap-6 ">
                        <div class="col-span-4 sm:col-span-3">
                            <div class="bg-white shadow rounded-lg p-6">
                                <div class="flex flex-col items-start">
                                    <img :src="`/storage/${ user.detail_info.avatar }`" class="w-full h-auto max-h-[250px] object-cover bg-gray-300 rounded-md mb-4 shrink-0"/>
                                    <h2 class="text-xl font-bold">{{ user.detail_info.name }}</h2>
                                    <p class="text-gray-700">{{ user.detail_info.profession }}</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <span v-for="(skill, index) in JSON.parse(user.detail_info.skills)" :key="index" id="badge-dismiss-default" class="inline-flex items-center px-2 py-1 text-sm font-medium text-gray-800 bg-gray-100 rounded">
                                            {{ skill }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-gray-700">Размещено заказов: {{ user.detail_info.jobs_count }}</p>
                                </div>
                                <hr class="my-6 border-t border-gray-300">
                                <div class="flex flex-col">
                                    <span class="text-gray-700 uppercase font-bold tracking-wider mb-2">Contacts</span>
                                    <ul>
                                        <li class="mb-2">
                                            <a href="#">
                                                email: {{ user.email }}
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a href="#">
                                                wa: {{ user.detail_info.contact_phone }}
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <a href="#">
                                                tg: @{{ user.detail_info.contact_telegram }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-4 sm:col-span-9">
                            <div class="bg-white shadow rounded-lg p-6">
                                <h2 class="text-xl font-bold mb-4">About Me</h2>
                                <p class="text-gray-700">
                                    {{ user.detail_info.about_text }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
