<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { ref } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const form = useForm({
    category_name: '',
    sub_category_name: '',
});

const isChecked = ref(false);

const submit = () => {
    form.post(route('admin.storeCategories'));
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">ADD CATEGORIES</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="container mx-auto py-8">
                    <div class="bg-white shadow rounded-lg p-6">
                        <p>admin categories</p>

                        <form @submit.prevent="submit">

                            <div class="mt-4">
                                <InputLabel for="category_name" value="Название категории" />

                                <TextInput
                                    id="category_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.category_name"
                                />
                            </div>

                            <div class="mt-4">

                                <div class="mt-4 relative flex gap-3 items-center">
                                    <div class="flex h-6 items-center">
                                        <input id="other_price" v-model="isChecked" name="comments" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    </div>
                                    <div class="text-sm leading-6">
                                        <label for="other_price" class="font-medium text-gray-900">
                                            Добавить подкатегорию
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-2 w-full" v-if="isChecked">
                                    <InputLabel for="sub_category_name" value="Название подкатегории" />

                                    <TextInput
                                        id="sub_category_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.sub_category_name"
                                    />
                                </div>

                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ms-4">
                                    Добавить категорию
                                </PrimaryButton>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
