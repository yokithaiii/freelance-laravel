<script setup>
import {onMounted, ref} from "vue";
import { Modal } from 'flowbite';
import {useForm} from "@inertiajs/vue3";


const props = defineProps({
    id: Number,
    title: String,
    description: String,
    category: String,
    sub_category: String,
    price: String,
    date_deadline: String,
    date_published: String,
    price_in_hour_flag: Number,
    user: {
        login: String,
        avatar: String
    },
    auth_id: Number,
    files: Array,
    count: Number,
});

const shortenText = (text) => {
    if (text && text.length > 250) {
        return text.substring(0, 250) + '...';
    }
    return text;
};

const modalEl = ref(null);
const modalInstance = ref(null);

const initModal = () => {
    const options = {
        placement: 'bottom-right',
        backdrop: 'dynamic',
        backdropClasses:
            'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
        closable: true,
    };

    // instance options object
    const instanceOptions = {
        id: 'deleteModal',
        override: true
    };

    modalInstance.value = new Modal(modalEl.value, options, instanceOptions);
}

const form = useForm({
    id: null,
});

const hideModal = () => {
    modalInstance.value.hide();
};

const showModal = (itemId) => {
    // Сохранение айди элемента для последующего использования
    form.id = itemId;
    modalInstance.value.show();
};

const submit = (id) => {
    form.post(route('jobs.destroy', form.id), {
        onFinish: () => {
            modalInstance.value.hide();
        },
    });
};

onMounted(initModal);
</script>

<template>

    <div class="w-full p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex justify-between items-start mb-2 text-gray-500">
            <div>
                <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-400 border border-gray-500">
                    {{ category }}
                </span>
                <span v-if="sub_category" class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-400 border border-gray-500">
                    {{ sub_category }}
                </span>
            </div>
            <div class="flex flex-col gap-2 items-end">
                <span v-if="price_in_hour_flag" class="text-2xl text-gray-900 dark:text-white">Бюджет: {{ price }} ₽/час</span>
                <span v-else class="text-2xl text-gray-900 dark:text-white">Бюджет: {{ price }} ₽</span>
                <span>Срок до: {{ date_deadline }}</span>
            </div>
        </div>
        <div class="flex flex-col" style="margin-top: -35px">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                <a :href="`/jobs/${ id }`">
                    {{ title }}
                </a>
            </h5>
            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                {{ shortenText(description) }}
            </p>
        </div>
        <div class="flex flex-col gap-1" v-if="files.length > 0">
            <p>Прикрепленные файлы:</p>
            <div v-for="file in files" :key="file.id">
                <a class="flex gap-1 items-center" :href="`/storage/${ file.file_path }`" target="_blank">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7Zm.394 9.553a1 1 0 0 0-1.817.062l-2.5 6A1 1 0 0 0 8 19h8a1 1 0 0 0 .894-1.447l-2-4A1 1 0 0 0 13.2 13.4l-.53.706-1.276-2.553ZM13 9.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-blue-700">{{file.file_name}}</span>
                </a>
            </div>
        </div>
        <div class="flex items-end justify-between">
            <span class="text-sm">Заказ размещен: {{ date_published }}</span>
            <div>
                <a :href="`/jobs/edit/${ id }`" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-normal text-md text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-4">
                    Изменить
                    <svg aria-hidden="true" class="text-white ms-2 w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                <button id="deleteButton" @click="showModal(id)" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-normal text-md text-white hover:bg-red-700 focus:bg-red-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-4">
                    Удалить
                    <svg aria-hidden="true" class="text-white ms-2 w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div id="deleteModal" ref="modalEl" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
            <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                <!-- Modal content -->
                <div class="relative p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                    <svg class="text-gray-400 dark:text-gray-500 w-11 h-11 mb-3.5 mx-auto" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <p class="mb-4 text-gray-500 dark:text-gray-300">Вы действительно хотите удалить заказ безвозвратно?</p>
                    <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"></path>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div class="flex flex-col gap-1">
                            <span class="font-medium">Внимание!</span>
                            <span>
                                Связанные с заказом картинки тоже удалятся!
                            </span>
                        </div>
                    </div>
                    <form @submit.prevent="submit(id)" class="flex justify-center items-center space-x-4">
                        <button @click="hideModal()" type="button" class="py-2 px-3 text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                            Нет, отменить
                        </button>
                        <button type="submit" class="py-2 px-3 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900">
                            Да, удалить
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>

</style>