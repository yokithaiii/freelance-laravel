<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Datepicker from 'flowbite-datepicker/Datepicker';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    title: '',
    description: '',
    price: '',
    price_in_hour_flag: false,
    date_deadline: '',
    category_id: '',
    sub_category_id: '',
    files: [],
});

const tempFiles = ref([]);
const tempPrice = ref('');
const tempPriceInHour= ref('');
const isChecked = ref(false);
const datepickerInput = ref([]);

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

const priceHandler = () => {
    if (isChecked.value) {
        form.price = tempPriceInHour.value;
        form.price_in_hour_flag = true;
    } else {
        form.price = tempPrice.value;
    }
}

const initDatepicker = () => {
    if (datepickerInput.value) {
        new Datepicker(datepickerInput.value, {
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            disablePastDates: true,
        });
        datepickerInput.value.addEventListener('changeDate', (event) => {
            const date = new Date(event.detail.date);

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            form.date_deadline = `${year}-${month}-${day}`;
        });
    }
};

const submit = () => {
    form.post(route('jobs.store'));
};

const selectedCategory = computed(() => {
    return props.categories.find(category => category.id === parseInt(form.category_id));
});

const subCategories = computed(() => {
    return selectedCategory.value ? selectedCategory.value.children : [];
});

watch(isChecked, (newValue) => {
    if (newValue) {
        priceHandler();
    } else {
        form.price = tempPrice.value;
        form.price_in_hour_flag = false;
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
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Создать заказ</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Здесь вы можете обновить информацию профиля.
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
                                            @input="priceHandler"
                                            v-model="tempPrice"
                                            required
                                            :disabled="isChecked"
                                            autocomplete="off"
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
                                            <select v-model="tempPriceInHour" @change="priceHandler" id="category" name="category" autocomplete="country-name" class="block w-full rounded-md border-0 mt-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
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
                                            <input autocomplete="off" id="date" ref="datepickerInput" required type="text" class="mt-1 border border-gray-300 text-gray-900 text-sm shadow-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date">
                                        </div>
                                    </div>
                                </div>


                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="multiple_files" value="Прикрепить файлы" />

                                <input v-on:change="fileHandler" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="multiple_files" type="file" multiple>
                                <p class="mt-3 text-sm leading-6 text-gray-600">Загрузите до 10 файлов общим объемом до 100 МБ</p>

                            </div>

                            <div class="flex items-center justify-end mt-4">

                                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Создать заказ
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
