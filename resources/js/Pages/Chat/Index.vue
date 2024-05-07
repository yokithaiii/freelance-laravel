<script setup>
import {onMounted, ref, watch} from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    chats: Array,
    user: Object
});

const form = useForm({
    body: '',
});

const chat = ref(null);
const user_to_id = ref('');

const getMessages = async (login) => {
    try {
        const response = await axios.get(`/chat/${login}`);
        chat.value = response.data;
        user_to_id.value = response.data.chat_with_id;

        axios.patch('/read_all_messages', {
            chat_id: response.data.id,
        })
    } catch (e) {
        console.error('Ошибка при получении чата:', e);
    }
};

const submit = async (chatId) => {
    try {
        const response = await axios.post(`chat/${user_to_id.value}`, {
            chatId: chatId,
            body: form.body
        });
        chat.value.messages.push(response.data);

        let foundChat = props.chats.find(chat2 => chat2.chat_id === chatId);
        foundChat.last_message.body = form.body;
        foundChat.last_message.user_id = props.user.id;

        form.body = '';
    } catch (e) {
        console.error('Ошибка при отправке сообщения:', e);
    }
};

onMounted(() => {
    const echoChannel = window.Echo.channel(`chat.${props.user.id}`)
        .listen('.chat', (res) => {
            if (chat.value !== null) {
                chat.value.messages.push(res.message);

                axios.patch('/read_message', {
                    chat_id: res.message.chat_id,
                    message_id: res.message.id,
                })
            }
            let foundChat = props.chats.find(chat2 => chat2.chat_id === res.message.chat_id);
            foundChat.last_message.body = res.message.body;
            foundChat.last_message.user_id = res.message.user.id;
        });
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div v-if="chats.length === 0" class="container mx-auto py-8">
                    <div class="w-full">
                        <div class="bg-white shadow rounded-lg p-6">
                            <p>Chats not found.</p>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="text-gray-900 flex flex-col gap-4">
                        <div class="flex">

                            <!--sidebar-->
                            <div class="w-1/3 bg-white border-r border-gray-100">
                                <header class="bg-white p-4 text-gray-700">
                                    <h1 class="text-2xl font-semibold">All chats</h1>
                                </header>

                                <div class="overflow-y-auto h-auto p-3">
                                    <div v-for="chat in chats" :key="chat.id">
                                        <div @click="getMessages(chat.receiver.login)" class="flex items-center mb-4  hover:bg-gray-100 p-2 rounded-md cursor-pointer">
                                            <div class="w-12 h-12 bg-gray-300 rounded-full mr-3">
                                                <img :src="`/storage/${ chat.receiver.detail_info.avatar }`" alt="User Avatar" class="w-12 h-12 rounded-full">
                                            </div>
                                            <div class="flex-1">
                                                <h2 class="text-lg font-semibold">{{ chat.receiver.detail_info.name }}</h2>
                                                <p class="text-gray-600" v-if="chat.last_message.body">
                                                    <span v-if="chat.last_message.user_id === $page.props.auth.user.id">Вы: </span>
                                                    {{ chat.last_message.body }}
                                                </p>
                                                <p class="text-gray-600" v-else>Сообщений пока нет</p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!--chat area-->
                            <div class="flex-1" v-if="chat">
                                <header class="bg-white p-4 text-gray-700">
                                    <h1 class="text-2xl font-semibold">Чат с пользователем {{ chat.chat_with }}</h1>
                                </header>

                                <div class="h-screen overflow-y-auto p-4 bg-gray-100" style="height: calc(100vh - 320px);">

                                    <div v-for="message in chat.messages" :key="message.id">

                                        <div class="flex w-full mt-2 space-x-3 max-w-xs ml-auto justify-end" v-if="message.user.id === $page.props.auth.user.id">
                                            <div class="text-right">
                                                <div class="bg-blue-600 text-white p-3 rounded-l-lg rounded-br-lg">
                                                    <p class="text-sm font-normal text-white">{{ message.body }}</p>
<!--                                                    <span v-if="message.is_read" class="text-sm font-normal text-gray-300">Прочитано</span>-->
<!--                                                    <span v-else class="text-sm font-normal text-gray-300">Доставлено</span>-->
                                                </div>
                                                <span class="text-xs text-gray-500 leading-none">{{ message.created_at }}</span>
                                            </div>
                                            <img class="w-10 h-10 rounded-full" :src="`/storage/${ message.user.avatar }`" alt="">
                                        </div>

                                        <div class="flex w-full mt-2 space-x-3 max-w-xs" v-else>
                                            <img class="w-10 h-10 rounded-full" :src="`/storage/${ message.user.avatar }`" alt="">
                                            <div>
                                                <div class="bg-gray-300 p-3 rounded-r-lg rounded-bl-lg">
                                                    <p class="text-sm">{{ message.body }}</p>
                                                </div>
                                                <span class="text-xs text-gray-500 leading-none">{{ message.created_at }}</span>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="bg-white p-4">
                                    <div class="flex items-center pt-0">
                                        <form @submit.prevent="submit(chat.id)" class="flex items-center justify-center w-full space-x-2">
                                            <input v-model="form.body" class="flex h-10 w-full rounded-md border border-[#e5e7eb] px-3 py-2 text-sm placeholder-[#6b7280]" placeholder="Type your message" value="">
                                            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium text-[#f9fafb] disabled:pointer-events-none disabled:opacity-50 bg-black hover:bg-[#111827E6] h-10 px-4 py-2">
                                                Send
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-1" v-else>
                                <div class="h-screen overflow-y-auto p-4 flex items-center justify-center" style="height: calc(100vh - 188px);">
                                    <div class="">
                                        Выберите собеседника
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
