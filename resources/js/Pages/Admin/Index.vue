<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">ADMIN PANEL</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="container mx-auto py-8">
                    <div class="bg-white shadow rounded-lg p-6">
                        <p>admin panel</p>
                        <p>links: </p>
                        <br>
                        <ul>
                            <li>
                                <a :href="route('admin.createCategories')" class="text-blue-600 underline">Add Categories</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
