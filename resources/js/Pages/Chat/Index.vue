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

                                <form @submit.prevent="submit(chat.id)" class="">
                                    <div class="flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                                        <button type="button" class="inline-flex justify-center p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 18">
                                                <path fill="currentColor" d="M13 5.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM7.565 7.423 4.5 14h11.518l-2.516-3.71L11 13 7.565 7.423Z"/>
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 1H2a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1Z"/>
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM7.565 7.423 4.5 14h11.518l-2.516-3.71L11 13 7.565 7.423Z"/>
                                            </svg>
                                            <span class="sr-only">Upload image</span>
                                        </button>
                                        <button type="button" class="p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.408 7.5h.01m-6.876 0h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM4.6 11a5.5 5.5 0 0 0 10.81 0H4.6Z"/>
                                            </svg>
                                            <span class="sr-only">Add emoji</span>
                                        </button>
                                        <textarea v-model="form.body" id="chat" rows="1" class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Your message..."></textarea>
                                        <button type="submit" class="inline-flex justify-center p-2 text-blue-600 rounded-full cursor-pointer hover:bg-gray-200 dark:text-blue-500 dark:hover:bg-gray-600">
                                            <svg class="w-5 h-5 text-gray-700 rotate-90 rtl:-rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                                <path d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z"/>
                                            </svg>
                                            <span class="sr-only">Send message</span>
                                        </button>
                                    </div>
                                </form>
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
