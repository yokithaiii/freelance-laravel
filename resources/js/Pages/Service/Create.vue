<script setup>
import { ref, computed } from "vue";
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps({
    categories: Array,
});

const form = useForm({
    service_title: '',
    service_category_id: '',
    service_sub_category_id: '',
    service_cover_file: [],
    service_description: '',
    service_needs: '',
    service_price: '',
    service_term_days: '',
    service_files: [],
});

const tempCoverUrl = ref('');
const tempFiles = ref([]);

const coverHandler = (event) => {
    const file = event.target.files[0];
    if (file) {
        tempCoverUrl.value = URL.createObjectURL(file);
        form.service_cover_file = file;
    }
};

const filesHandler = (event) => {
    const files = event.target.files;
    if (files.length > 0) {
        tempFiles.value = [];
        for (let i = 0; i < files.length; i++) {
            tempFiles.value.push(files[i]);
        }
        form.service_files = tempFiles.value;
    }
};

const submit = () => {
    form.post(route('service.store'));
};

const selectedCategory = computed(() => {
    return props.categories.find(category => category.id === parseInt(form.service_category_id));
});

const subCategories = computed(() => {
    return selectedCategory.value ? selectedCategory.value.children : [];
});
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
                        <h2 class="text-lg font-medium text-gray-900">Создать услугу</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Здесь вы можете создать и разместить свою услугу.
                        </p>
                    </header>

                    <form @submit.prevent="submit">

                        <div class="">

                            <div class="mt-4">
                                <InputLabel for="title" value="Заголовок услуги" />

                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.service_title"
                                    required
                                    autocomplete="name"
                                />

                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="category" value="Категория услуги" />

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="mt-2 w-full">
                                        <select v-model="form.service_category_id" id="category" name="category" class="block w-full rounded-md border-0 mt-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mt-2 w-full">
                                        <select :disabled="!form.service_category_id" v-model="form.service_sub_category_id" id="sub_category" name="sub_category" class="block w-full rounded-md border-0 mt-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option v-for="subCategory in subCategories" :key="subCategory.id" :value="subCategory.id">
                                                {{ subCategory.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <p class="block font-medium text-sm text-gray-700">Обложка услуги</p>
                                <div class="mt-2 flex items-center ">
                                    <label for="dropzone-file" class="flex flex-col items-center justify-center w-64 h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                        <img v-if="tempCoverUrl" class="rounded-md w-full h-full object-cover" :src="tempCoverUrl" alt="image description">
                                        <div v-else class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                                        </div>
                                        <input id="dropzone-file" v-on:change="coverHandler"  type="file" class="hidden" />
                                    </label>
                                </div>
                            </div>

                            <div class="mt-4">
                                <InputLabel for="description" value="Описание услуги" />

                                <div class="mt-2">
                                    <textarea required v-model="form.service_description" id="description" name="about" rows="4" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                                </div>

                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="needs" value="От покупателя нужно" />

                                <div class="mt-2">
                                    <textarea required v-model="form.service_needs" id="needs" name="about" rows="4" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                                </div>

                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="sm:col-span-3 mt-4">

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <InputLabel for="price" value="Цена" />
                                        <TextInput
                                            id="price"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.service_price"
                                            required
                                            autocomplete="off"
                                        />
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="deadline">
                                            <span>Срок выполнения</span>
                                        </label>
                                        <div class="mt-1 w-full">
                                            <select v-model="form.service_term_days" id="deadline" name="deadline" required class="block w-full rounded-md border-0 mt-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                <option value="1">1 день</option>
                                                <option value="2">2 дня</option>
                                                <option value="3">3 дня</option>
                                                <option value="4">4 дня</option>
                                                <option value="5">5 дней</option>
                                                <option value="6">6 дней</option>
                                                <option value="7">7 дней</option>
                                                <option value="10">10 дней</option>
                                                <option value="14">2 недели</option>
                                                <option value="21">3 недели</option>
                                                <option value="30">1 месяц</option>
                                                <option value="60">2 месяца</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="multiple_files" value="Прикрепить дополнительные фотографии" />

                                <input v-on:change="filesHandler" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="multiple_files" type="file" multiple>
                                <p class="mt-3 text-sm leading-6 text-gray-600">Загрузите до 10 файлов общим объемом до 100 МБ</p>

                            </div>

                            <div class="flex items-center justify-end mt-4">

                                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Создать услугу
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
