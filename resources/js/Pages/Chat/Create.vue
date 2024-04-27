<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    sender: Object,
    receiver: Object,
});

const form = useForm({
    body: '',
});

// const submit = () => {
//     form.post(route('chat.store'));
// };
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Job detail
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-4">
                    <a href="/jobs" class="flex items-center gap-1">
                        <svg
                            class="w-6 h-6 text-gray-800 dark:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 12h14M5 12l4-4m-4 4 4 4"
                            />
                        </svg>
                        Назад
                    </a>
                </div>
                <div class="mt-4">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Чат с пользователем {{ receiver.detail_info.name !== null ? receiver.detail_info.name : receiver.login }}
                    </h2>
                </div>

<!--                <pre>{{ sender }}</pre>-->
<!--                <pre>{{ receiver }}</pre>-->

                <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 text-gray-800 p-10">
                    <div class="flex flex-col flex-grow w-full max-w-xl bg-white shadow-xl rounded-lg overflow-hidden">
                        <div class="flex flex-col flex-grow h-0 p-4 overflow-auto">

                            <div class="flex w-full mt-2 space-x-3 max-w-xs">
                                <img class="w-10 h-10 rounded-full" :src="`/storage/${ receiver.detail_info.avatar }`" alt="">
                                <div>
                                    <div class="bg-gray-300 p-3 rounded-r-lg rounded-bl-lg">
                                        <p class="text-sm">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                    </div>
                                    <span class="text-xs text-gray-500 leading-none">2 min ago</span>
                                </div>
                            </div>
                            <div class="flex w-full mt-2 space-x-3 max-w-xs ml-auto justify-end">
                                <div>
                                    <div class="bg-blue-600 text-white p-3 rounded-l-lg rounded-br-lg">
                                        <p class="text-sm">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod.</p>
                                    </div>
                                    <span class="text-xs text-gray-500 leading-none">2 min ago</span>
                                </div>
                                <img class="w-10 h-10 rounded-full" :src="`/storage/${ receiver.detail_info.avatar }`" alt="">
                            </div>

                        </div>

                        <div class="bg-gray-300 p-4">
                            <div class="flex items-center pt-0">
                                <form @submit.prevent="submit" class="flex items-center justify-center w-full space-x-2">
                                    <input class="flex h-10 w-full rounded-md border border-[#e5e7eb] px-3 py-2 text-sm placeholder-[#6b7280]" placeholder="Type your message" value="">
                                    <button class="inline-flex items-center justify-center rounded-md text-sm font-medium text-[#f9fafb] disabled:pointer-events-none disabled:opacity-50 bg-black hover:bg-[#111827E6] h-10 px-4 py-2">
                                        Send
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                    <!-- Component End  -->
                </div>


            </div>
        </div>
    </AuthenticatedLayout>
</template>
