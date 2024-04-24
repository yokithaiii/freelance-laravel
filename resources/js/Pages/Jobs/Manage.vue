<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import CardManage from '@/Components/CardManage.vue';

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
                        <CardManage
                            v-for="job in jobs"
                            :key="job.id"
                            :job="job"
                        >
                        </CardManage>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
