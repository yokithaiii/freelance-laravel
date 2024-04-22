<script setup>
import { ref, computed, watch, onMounted } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Datepicker from 'flowbite-datepicker/Datepicker';

const props = defineProps({
    categories: Array,
    job: Object,
});

const form = useForm({
    title: props.job.title ? props.job.title : '',
    description: props.job.description ? props.job.description : '',
    category_id: props.job.category_id ? props.job.category_id : '',
    sub_category_id: props.job.sub_category_id ? props.job.sub_category_id : '',
    price: props.job.price ? props.job.price : '',
    price_in_hour: props.job.price_in_hour ? props.job.price_in_hour : false,
    files: props.job.images ? props.job.images : null,
    date: props.job.date ? props.job.date : null,
    delete_files: [],
});

const tempFiles = ref(props.job.images ? props.job.images : null);
const tempPrice = ref(props.job.price ? props.job.price : '');
const tempPriceInHour= ref(props.job.price_in_hour ? props.job.price_in_hour : '');
const isChecked = ref(props.job ? props.job.price_in_hour : false);
const datepickerInput = ref(null);

const fileHandler = (event) => {
    const files = event.target.files;
    if (files.length > 0) {
        tempFiles.value = [];
        for (let i = 0; i < files.length; i++) {
            tempFiles.value.push(files[i]);
        }
        form.files = tempFiles.value;
    }
};

const deleteFiles = (index) => {
    const removedFile = tempFiles.value.splice(index, 1);
    if (removedFile.length > 0) {
        const fileId = removedFile[0].id;
        if (!form.delete_files.includes(fileId)) {
            form.delete_files.push(fileId);
        }
    }
    console.log(form.delete_files);
}

const updatePrice = () => {
    if (isChecked.value) {
        form.price = tempPriceInHour.value;
        form.price_in_hour = true;
    } else {
        form.price = tempPrice.value;
    }
}

const selectedCategory = computed(() => {
    return props.categories.find(category => category.id === parseInt(form.category_id));
});

const subCategories = computed(() => {
    return selectedCategory.value ? selectedCategory.value.sub_categories : [];
});

const initDatepicker = () => {
    if (datepickerInput.value) {
        const datepicker = new Datepicker(datepickerInput.value, {
            weekStart: 1,
            format: 'yyyy-mm-dd',
            todayHighlight: true,
        });

        if (props.job.date) {
            datepicker.setDate(props.job.date);
        }

        datepickerInput.value.addEventListener('changeDate', (event) => {
            // Получаем дату из события
            const date = new Date(event.detail.date);

            // Форматируем дату в формат 'Y-m-d' без учета часового пояса
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Месяцы начинаются с 0, поэтому +1
            const day = String(date.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`;

            // Отправляем дату на сервер или используем в вашем приложении
            form.date = formattedDate;
            console.log(form.date);
        });

    }
};

const submit = () => {
    form.post(route('jobs.update', { id: props.job.id }), {
        onFinish: () => {
            resetForm();
        },
    });
};

const resetForm = () => {
    form.title = '';
    form.description = '';
    form.category_id = '';
    form.sub_category_id = '';
    form.price = '';
    form.price_in_hour = false;
    form.files = null;
    form.date = null;
    tempFiles.value = null;
    tempPrice.value = '';
    tempPriceInHour.value = '';
    isChecked.value = false;
};

// Отслеживаем изменения в чекбоксе
watch(isChecked, (newValue) => {
    if (newValue) {
        updatePrice();
    } else {
        form.price = tempPrice.value;
        form.price_in_hour = false;
    }
});

onMounted(initDatepicker);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create job</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="mb-4">
                    <a href="/jobs/manage" class="flex items-center gap-1">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
                        </svg>
                        Назад
                    </a>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Изменить заказ - {{ job.title }}</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Здесь вы можете обновить информацию заказа.
                        </p>
                    </header>

                    <form @submit.prevent="submit">

                        <div class="">

                            <div class="mt-4">
                                <InputLabel for="title" value="Заголовок заказа" />

                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    required
                                    autocomplete="name"
                                />

                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="description" value="Описание задачи" />

                                <div class="mt-2">
                                    <textarea required v-model="form.description" id="description" name="about" rows="4" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                                </div>

                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="category" value="Категория" />

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="mt-2 w-full">
                                        <select v-model="form.category_id" id="category" name="category" class="block w-full rounded-md border-0 mt-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mt-2 w-full">
                                        <select :disabled="!form.category_id" v-model="form.sub_category_id" id="sub_category" name="sub_category" class="block w-full rounded-md border-0 mt-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option v-for="subCategory in subCategories" :key="subCategory.id" :value="subCategory.id">
                                                {{ subCategory.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3 mt-4">

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <InputLabel for="price" value="Цена" />
                                        <TextInput
                                            id="price"
                                            type="text"
                                            class="mt-1 block w-full"
                                            @input="updatePrice"
                                            v-model="tempPrice"
                                            required
                                            :disabled="isChecked"
                                        />

                                        <div class="mt-4 relative flex gap-3 items-center">
                                            <div class="flex h-6 items-center">
                                                <input id="other_price" v-model="isChecked" name="comments" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                            </div>
                                            <div class="text-sm leading-6">
                                                <label for="other_price" class="font-medium text-gray-900">Не могу оценить стоимость заказа. Жду предложений от фрилансеров</label>
                                            </div>
                                        </div>

                                        <div class="mt-2 w-full" v-if="isChecked">
                                            <p class="mt-3 text-sm leading-6 text-gray-600">Чтобы фрилансеры могли присылать более релевантные отклики, укажите пожалуйста ориентировочную сумму, которую вы готовы платить за час работы:</p>
                                            <select v-model="tempPriceInHour" @change="updatePrice" id="category" name="category" autocomplete="country-name" class="block w-full rounded-md border-0 mt-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                                <option value="100">До 100 ₽</option>
                                                <option value="100-200">101 - 200 ₽</option>
                                                <option value="200-500">201 - 500 ₽</option>
                                                <option value="500-1000">501 - 1000 ₽</option>
                                                <option value="1000-2000">1001 - 2000 ₽</option>
                                                <option value="2000-3000">2001 - 3000 ₽</option>
                                                <option value="3000-4000">3001 - 4000 ₽</option>
                                                <option value="4000-5000">4001 - 5000 ₽</option>
                                                <option value="5000">Более 5000 ₽</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <InputLabel for="date" value="Когда заказ должен быть готов" />
                                        <div class="relative w-full">
                                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                                </svg>
                                            </div>
                                            <input id="date" ref="datepickerInput" required type="text" class="mt-1 border border-gray-300 text-gray-900 text-sm shadow-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date">
                                        </div>
                                    </div>
                                </div>


                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4" v-if="job.images.length > 0">
                                <InputLabel for="multiple_files" value="Прикрепленные файлы" />

                                <div class="flex gap-3 mt-2">
                                    <div v-for="(file, index) in job.images" :key="file.id">
                                        <div class="group relative my-2.5">
                                            <div class="absolute w-full h-full bg-gray-900/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg flex items-center justify-center">
                                                <div @click="deleteFiles(index)" class="cursor-pointer inline-flex items-center justify-center rounded-full h-10 w-10 bg-white/30 hover:bg-red-700/50 focus:ring-4 focus:outline-none dark:text-white focus:ring-gray-50">
                                                    <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <img :src="`/storage/${ file.url }`" class="h-24 w-24 object-cover rounded-md" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <InputLabel for="multiple_files" value="Добавить файлы" />

                                <input v-on:change="fileHandler" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="multiple_files" type="file" multiple>
                                <p class="mt-3 text-sm leading-6 text-gray-600">Загрузите до 10 файлов общим объемом до 100 МБ</p>

                            </div>

                            <div class="flex items-center justify-end mt-4">

                                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Обновить заказ
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
