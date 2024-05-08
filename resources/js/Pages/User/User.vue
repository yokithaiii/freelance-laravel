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
                <div v-if="!user.detail_info.name" class="container mx-auto py-8">
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
                                    <img :src="`/storage/${ user.detail_info.avatar }`" class="w-full h-[250px] max-h-[250px] object-cover bg-gray-300 rounded-md mb-4 shrink-0"/>
                                    <h2 class="text-xl font-bold">{{ user.detail_info.name }}</h2>
                                    <p class="text-gray-700">{{ user.detail_info.profession }}</p>
                                    <p class="mt-2 text-gray-700">Размещено заказов: {{ user.detail_info.jobs_count }}</p>
                                </div>
                                <hr class="my-6 border-t border-gray-300">
                                <div class="flex flex-col">
                                    <span class="text-gray-700 uppercase font-bold tracking-wider mb-2">Contacts</span>
                                    <ul>
                                        <li v-if="user.email" class="mb-2">
                                            <a href="#" class="flex items-center gap-2">
                                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m3.5 5.5 7.893 6.036a1 1 0 0 0 1.214 0L20.5 5.5M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"/>
                                                </svg>
                                                {{ user.email }}
                                            </a>
                                        </li>
                                        <li v-if="user.detail_info.contact_phone" class="mb-2">
                                            <a href="#" class="flex items-center gap-2">
                                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.427 14.768 17.2 13.542a1.733 1.733 0 0 0-2.45 0l-.613.613a1.732 1.732 0 0 1-2.45 0l-1.838-1.84a1.735 1.735 0 0 1 0-2.452l.612-.613a1.735 1.735 0 0 0 0-2.452L9.237 5.572a1.6 1.6 0 0 0-2.45 0c-3.223 3.2-1.702 6.896 1.519 10.117 3.22 3.221 6.914 4.745 10.12 1.535a1.601 1.601 0 0 0 0-2.456Z"/>
                                                </svg>
                                                {{ user.detail_info.contact_phone }}
                                            </a>
                                        </li>
                                        <li v-if="user.detail_info.contact_telegram" class="mb-2">
                                            <a href="#" class="flex items-center gap-2">
                                                <svg class="w-6 h-6 text-gray-800" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill="currentColor" d="m16 .5c-8.563 0-15.5 6.938-15.5 15.5s6.938 15.5 15.5 15.5c8.563 0 15.5-6.938 15.5-15.5s-6.938-15.5-15.5-15.5zm7.613 10.619-2.544 11.988c-.188.85-.694 1.056-1.4.656l-3.875-2.856-1.869 1.8c-.206.206-.381.381-.781.381l.275-3.944 7.181-6.488c.313-.275-.069-.431-.482-.156l-8.875 5.587-3.825-1.194c-.831-.262-.85-.831.175-1.231l14.944-5.763c.694-.25 1.3.169 1.075 1.219z"/>
                                                </svg>
                                                {{ user.detail_info.contact_telegram }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-4 sm:col-span-9">
                            <div class="bg-white shadow rounded-lg p-6">
                                <h2 class="text-xl font-bold mb-4">About Me</h2>
                                <div>
                                    <p class="text-gray-700" >
                                        {{ user.detail_info.about_text ? user.detail_info.about_text : 'Описание профиля отсутствует' }}
                                    </p>
                                    <div class="mt-4">
                                        <p>Навыки: </p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span v-for="(skill, index) in JSON.parse(user.detail_info.skills)" :key="index" id="badge-dismiss-default" class="inline-flex items-center px-2 py-1 text-sm font-medium text-gray-800 bg-gray-100 rounded">
                                                {{ skill }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
