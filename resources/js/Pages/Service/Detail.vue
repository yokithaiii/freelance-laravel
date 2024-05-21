<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Swiper from "swiper";
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { Navigation, Pagination } from "swiper/modules";
import { onMounted, ref } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { Modal } from "flowbite";

const props = defineProps({
    service: Object,
})

const form = useForm({
    message: '',
});

const modalEl = ref(null);
const modalInstance = ref(null);

const initSwiper = () => {
    new Swiper('.swiper', {
        modules: [Navigation, Pagination],
        pagination: {
            el: '.swiper-pagination',
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
}

const initModal = () => {
    const options = {
        placement: 'bottom-right',
        backdrop: 'dynamic',
        backdropClasses:
            'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
        closable: true,
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

const submit = (id) => {
    try {
        form.post(route('service.order', { id: id }));
        form.message = '';
    } catch (e) {
        console.error('Ошибка при отправке сообщения:', e);
    } finally {
        if (modalInstance.value) {
            modalInstance.value.hide();
        }
    }

};

onMounted(initSwiper);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Service detail</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-4">
                    <a href="/services" class="flex items-center gap-1">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
                        </svg>
                        Назад
                    </a>
                </div>
                <div class="grid grid-cols-3 gap-8">
                    <div class="col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 flex flex-col gap-4">
                            <div>
                                <h1 class="font-medium text-xl">{{service.data.service_title}}</h1>
                            </div>

                            <div class="swiper w-full">
                                <div class="swiper-wrapper w-full">
                                    <div class="swiper-slide">
                                        <img class="w-full h-[450px] object-cover" :src="`/storage/${service.data.cover.file_path}`" alt="">
                                    </div>
                                    <div class="swiper-slide" v-for="(image, index) in service.data.images" :key="index">
                                        <img class="w-full h-[450px] object-cover" :src="`/storage/${image.file_path}`" alt="">
                                    </div>
                                </div>
                                <div class="swiper-pagination"></div>
                                <div class="swiper-button-prev">
                                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                                    </svg>
                                </div>
                                <div class="swiper-button-next">
                                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <span v-for="category in service.data.categories" :key="category.id" class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-400 border border-gray-500">
                                    {{ category.name }}
                                </span>
                            </div>

                            <div>
                                <h2 class="font-medium">Описание: </h2>
                                <p>{{ service.data.service_description }}</p>
                            </div>

                            <hr>

                            <div>
                                <h2 class="font-medium">От покупателя нужно: </h2>
                                <p>{{ service.data.service_needs }}</p>
                            </div>

                            <hr>

                            <div>
                                <h2 class="font-medium">Контакты: </h2>
                                <ul class="mt-2">
                                    <li class="mb-2 flex gap-2 items-center">
                                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m3.5 5.5 7.893 6.036a1 1 0 0 0 1.214 0L20.5 5.5M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"/>
                                        </svg>
                                        <a href="#">{{service.data.user.email}}</a>
                                    </li>
                                    <li class="mb-2 flex gap-2 items-center">
                                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.427 14.768 17.2 13.542a1.733 1.733 0 0 0-2.45 0l-.613.613a1.732 1.732 0 0 1-2.45 0l-1.838-1.84a1.735 1.735 0 0 1 0-2.452l.612-.613a1.735 1.735 0 0 0 0-2.452L9.237 5.572a1.6 1.6 0 0 0-2.45 0c-3.223 3.2-1.702 6.896 1.519 10.117 3.22 3.221 6.914 4.745 10.12 1.535a1.601 1.601 0 0 0 0-2.456Z"/>
                                        </svg>
                                        <a href="#">{{ service.data.user.details.contact_phone }}</a>
                                    </li>
                                    <li class="mb-2 flex gap-2 items-center">
                                        <svg class="w-6 h-6 text-gray-800" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                            <path fill="currentColor" d="m16 .5c-8.563 0-15.5 6.938-15.5 15.5s6.938 15.5 15.5 15.5c8.563 0 15.5-6.938 15.5-15.5s-6.938-15.5-15.5-15.5zm7.613 10.619-2.544 11.988c-.188.85-.694 1.056-1.4.656l-3.875-2.856-1.869 1.8c-.206.206-.381.381-.781.381l.275-3.944 7.181-6.488c.313-.275-.069-.431-.482-.156l-8.875 5.587-3.825-1.194c-.831-.262-.85-.831.175-1.231l14.944-5.763c.694-.25 1.3.169 1.075 1.219z"/>
                                        </svg>
                                        <a href="#">@{{ service.data.user.details.contact_telegram }}</a>
                                    </li>
                                </ul>
                            </div>

                            <hr>

                            <div>
                                <h2 class="font-medium">Отзывы: </h2>
                                <div>here reviews...</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-1 bg-white shadow-sm rounded-lg">
                        <div class="p-6 text-gray-900 flex flex-col gap-4">
                            <div>
                                <p class="font-bold text-xl">Цена: {{ service.data.service_price }} ₽</p>
                            </div>
                            <div>
                                <p class="font-medium">Дней на выполнение: {{ service.data.service_term_days }}</p>
                            </div>
                            <div>
                                <a @click="showModal" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-normal text-md text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Заказать услугу
                                    <svg class="w-6 h-6 text-white ms-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312"/>
                                    </svg>
                                </a>
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
                    <p class="font-medium mb-2">Заказать услугу</p>

                    <hr>

                    <div class="mt-2 text-left bg-gray-200 rounded-md p-4">
                        <p class="text-sm font-medium">{{service.data.service_title}}</p>
                        <p class="text-sm">{{service.data.service_description}}</p>
                        <p class="text-sm">Цена: {{ service.data.service_price }} ₽</p>
                        <p class="text-sm">Дней на выполнение: {{ service.data.service_term_days }}</p>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="message" value="Сообщение" class="text-left" />

                        <TextInput
                            id="message"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.message"
                        />
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button @click.prevent="submit(service.data.id)" type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-normal text-md text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-4">
                            Отправить
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.swiper-button-next, .swiper-button-prev {
    width: 22px;
    height: 22px;
    background: #1F2937;
    border-radius: 100%;
    padding: 2px;
}
.swiper-button-next::after, .swiper-button-prev::after {
    display: none;
}
.swiper-pagination-bullet-active {
    background: #1F2937;
}
</style>
