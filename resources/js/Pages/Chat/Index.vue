<script setup>
import {onMounted, ref, watch} from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { Modal } from "flowbite";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    rooms: Array,
    user: Object
});

const form = useForm({
    body: '',
    files: null,
    file_caption: '',
});

const chat = ref(null);
const offer = ref(null);
const user_to_id = ref('');
const tempAvatarUrl = ref('');
const modalEl = ref(null);
const modalInstance = ref(null);

const initModal = () => {
    const options = {
        placement: 'bottom-right',
        backdrop: 'dynamic',
        backdropClasses:
            'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
        closable: true,
        onHide: () => {
            tempAvatarUrl.value = '';
        }
    };

    const instanceOptions = {
        id: 'deleteModal',
        override: true
    };

    modalInstance.value = new Modal(modalEl.value, options, instanceOptions);
}

const showModal = () => {
    initModal();
    modalInstance.value.show();
};

const fileHandler = (event) => {
    const file = event.target.files[0];
    if (file) {
        tempAvatarUrl.value = URL.createObjectURL(file);
        form.files = file;
    }
};

const getMessages = async (login) => {
    try {
        const response = await axios.get(`/chat/${login}`);
        chat.value = response.data;
        user_to_id.value = response.data.chat_with_id;
        offer.value = response.data.offer;
    } catch (e) {
        console.error('Ошибка при получении чата:', e);
    }
};

const submit = async (chatId) => {
    try {
        let formData = new FormData();
        formData.append('chatId', chatId);
        formData.append('body', form.body);
        if (form.files) {
            formData.append('files', form.files);
            formData.append('body', 'Файл');
            formData.append('file_caption', form.file_caption);
            form.body = 'Файл';
        }

        const response = await axios.post(`chat/${user_to_id.value}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        chat.value.messages.push(response.data);

        let foundChat = props.rooms.find(chat2 => chat2.chat.id === chatId);
        foundChat.chat.last_message.body = form.body;
        foundChat.chat.last_message.user_id = props.user.id;

        form.body = '';
        form.files = null;
    } catch (e) {
        console.error('Ошибка при отправке сообщения:', e);
    } finally {
        if (modalInstance.value) {
            modalInstance.value.hide();
        }
    }
};

onMounted(() => {
    const echoChannel = window.Echo.channel(`chat.${props.user.id}`)
        .listen('.chat', (res) => {
            if (chat.value !== null) {
                chat.value.messages.push(res.message);
            }
            let foundChat = props.rooms.find(chat2 => chat2.chat_id === res.message.chat_id);
            foundChat.chat.last_message.body = res.message.body;
            foundChat.chat.last_message.user_id = res.message.user.id;
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

                <div v-if="rooms.length === 0" class="container mx-auto py-8">
                    <div class="w-full">
                        <div class="bg-white shadow rounded-lg p-6">
                            <p>Rooms not found.</p>
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
                                    <div v-for="chat in rooms" :key="chat.id">
                                        <div @click="getMessages(chat.receiver.login)" class="flex items-center mb-4  hover:bg-gray-100 p-2 rounded-md cursor-pointer">
                                            <div class="w-12 h-12 bg-gray-300 rounded-full mr-3">
                                                <img :src="`/storage/${ chat.receiver.details.avatar }`" alt="User Avatar" class="w-12 h-12 rounded-full">
                                            </div>
                                            <div class="flex-1">
                                                <h2 class="text-lg font-semibold">{{ chat.receiver.details.name }}</h2>
                                                <p class="text-gray-600" v-if="chat.chat.last_message.body">
                                                    <span v-if="chat.chat.last_message.user_id === $page.props.auth.user.id">Вы: </span>
                                                    {{ chat.chat.last_message.body }}
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

                                <div class="h-screen overflow-y-auto p-4 bg-gray-100 relative" style="height: calc(100vh - 320px);">

                                    <div class="mb-4 w-full bg-gray-200 rounded-md">
                                        <div class="py-4 px-4 flex flex-col gap-4">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">
                                                    Название заказа: {{ chat.offer.offer_job.title }}
                                                </p>
                                                <p class="text-sm font-regular text-gray-900">
                                                    {{ chat.offer.offer_job.description }}
                                                </p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    Сумма: {{ chat.offer.offer_job.price }}
                                                </p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    Срок до: {{ chat.offer.offer_job.date_deadline }}
                                                </p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    Заказчик: {{ chat.chat_with }}
                                                </p>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">
                                                    Исполнитель откликнулся сделав предложение: {{ chat.offer.offer_description }}
                                                </p>
                                                <p class="text-sm font-regular text-gray-900">
                                                    За сумму: {{ chat.offer.offer_price }}
                                                </p>
                                                <p class="text-sm font-medium text-gray-900">
                                                    Срок: {{ chat.offer.offer_date_deadline_days }} дней
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-for="message in chat.messages" :key="message.id">

                                        <div class="flex w-full mt-2 space-x-3 max-w-xs ml-auto justify-end" v-if="message.user.id === $page.props.auth.user.id">
                                            <div class="text-right">
                                                <div class="bg-blue-600 text-white p-3 rounded-l-lg rounded-br-lg">
                                                    <div v-if="message.is_image === true">
                                                        <p class="text-sm font-normal text-white" v-if="message.image.file_caption !== null">
                                                            {{ message.image.file_caption }}
                                                        </p>
                                                        <div class="group relative my-2.5" >
                                                            <img :src="`/storage/${ message.image.file_path }`" class="rounded-lg" />
                                                        </div>
                                                    </div>
                                                    <p v-else class="text-sm font-normal text-white">{{ message.body }}</p>
                                                </div>
                                                <span class="text-xs text-gray-500 leading-none">{{ message.created_time }}</span>
                                            </div>
                                            <img class="w-10 h-10 rounded-full" :src="`/storage/${ message.user.avatar }`" alt="">
                                        </div>

                                        <div class="flex w-full mt-2 space-x-3 max-w-xs" v-else>
                                            <img class="w-10 h-10 rounded-full" :src="`/storage/${ message.user.avatar }`" alt="">
                                            <div>
                                                <div class="bg-gray-300 p-3 rounded-r-lg rounded-bl-lg">
                                                    <div class="group relative my-2.5" v-if="message.is_image === true">
                                                        <div class="absolute w-full h-full bg-gray-900/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg flex items-center justify-center">
                                                            <a :href="`/storage/${ message.image.file_path }`" download class="inline-flex items-center justify-center rounded-full h-10 w-10 bg-white/30 hover:bg-white/50 focus:ring-4 focus:outline-none focus:ring-gray-50">
                                                                <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 18">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 1v11m0 0 4-4m-4 4L4 8m11 4v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3"/>
                                                                </svg>
                                                            </a>
                                                        </div>
                                                        <img :src="`/storage/${ message.image.file_path }`" class="rounded-lg" />
                                                    </div>
                                                    <p v-else class="text-sm">{{ message.body }}</p>
                                                </div>
                                                <span class="text-xs text-gray-500 leading-none">{{ message.created_time }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form @submit.prevent="submit(chat.id)" class="">
                                    <div class="flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                                        <button @click="showModal" type="button" class="inline-flex justify-center p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 18">
                                                <path fill="currentColor" d="M13 5.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM7.565 7.423 4.5 14h11.518l-2.516-3.71L11 13 7.565 7.423Z"/>
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 1H2a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1Z"/>
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM7.565 7.423 4.5 14h11.518l-2.516-3.71L11 13 7.565 7.423Z"/>
                                            </svg>
                                            <span class="sr-only">Upload image</span>
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

            <!-- Main modal -->
            <div id="deleteModal" ref="modalEl" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
                <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                    <!-- Modal content -->
                    <div class="relative p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                        <p>Прикрепить файл: </p>

                        <div v-if="form.files === null" class="mt-2 flex items-center justify-center w-full">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                                </div>
                                <input id="dropzone-file" v-on:change="fileHandler"  type="file" class="hidden" />
                            </label>
                        </div>

                        <div v-else class="mt-2">
                            <div class="">
                                <label for="dropzone-file" class="p-4 flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <input id="dropzone-file" v-on:change="fileHandler"  type="file" class="hidden" />
                                    <img v-if="tempAvatarUrl" class="rounded-md w-full h-full object-cover" :src="tempAvatarUrl" alt="image description">
                                    <div v-else class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <InputLabel for="caption" value="Подпись" class="text-left" />

                            <TextInput
                                id="caption"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.file_caption"
                            />
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button @click.prevent="submit(chat.id)" type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-normal text-md text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-4">
                                Отправить
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
