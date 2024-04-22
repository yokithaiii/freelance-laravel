<script setup>
import { ref } from "vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const detail = usePage().props.detail;

const form = useForm({
    avatar: detail ? detail.avatar : '',
    name: detail ? detail.name : '',
    profession: detail ? detail.profession : '',
    about_text: detail ? detail.about_text : '',
    contact_phone: detail ? detail.contact_phone : '',
    contact_telegram: detail ? detail.contact_telegram : '',
    skills: detail.skills === null ? [] : JSON.parse(detail.skills),
});

const avatarFile = ref(null);
const tempAvatarUrl = ref('');
const newSkill = ref('');

const updateAvatar = () => {
    const file = event.target.files[0];
    if (file) {
        tempAvatarUrl.value = URL.createObjectURL(file);
        avatarFile.value = file;
        form.avatar = file;
    }
};

const addSkill = () => {
    if (newSkill.value.trim()) {
        console.log(form.skills)
        form.skills.push(newSkill.value);
        newSkill.value = '';
    }
}

const removeSkill = (index) => {
    form.skills.splice(index, 1);
}
</script>

<template>
    <section>

        <form @submit.prevent="form.post(route('profile.updateDetail'))" class="space-y-6">
            <div class="grid gap-6 lg:grid-cols-2">

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div>
                        <InputLabel value="Аватар" />

                        <div class="col-span-full">
                            <div class="mt-2 flex items-start gap-x-3">
                                <img v-if="tempAvatarUrl" class="rounded-md w-20 h-20" :src="tempAvatarUrl" alt="image description">
                                <img v-else-if="form.avatar" class="rounded-md w-20 h-20" :src="`/storage/${form.avatar}`" alt="image description">
                                <div v-else class="relative w-20 h-20 overflow-hidden bg-gray-100 rounded-md dark:bg-gray-600">
                                    <svg class="absolute w-24 h-24 text-gray-400 -left-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                                <label for="dropzone-file" class="cursor-pointer rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    Изменить фото
                                </label>
                                <input v-on:change="updateAvatar" id="dropzone-file" type="file" class="hidden" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="name" value="Фамилия Имя" />

                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            autocomplete="name"
                        />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="profession" value="Профессия" />

                        <TextInput
                            id="profession"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.profession"
                            autocomplete="name"
                        />

                        <InputError class="mt-2" :message="form.errors.profession" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="skills" value="Навыки" />

                        <div class="relative">
                            <TextInput
                                id="skills"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="newSkill"
                                autocomplete="name"
                            />
                            <button @click.prevent="addSkill" class="text-white right-0 rounded-l-none absolute my-auto bottom-0 top-0 bg-gray-800 hover:bg-gray-700 rounded-md text-sm px-4 py-1">Добавить</button>
                        </div>

                        <div class="mt-2 flex gap-2 flex-wrap">
                            <span v-for="(skill, index) in form.skills" :key="index" id="badge-dismiss-default" class="inline-flex items-center px-2 py-1 text-sm font-medium text-gray-800 bg-gray-100 rounded">
                                {{ skill }}
                                <button @click.prevent="removeSkill(index)" class="inline-flex items-center p-1 ms-2 text-sm text-gray-400 bg-transparent rounded-sm hover:bg-gray-200 hover:text-gray-900" data-dismiss-target="#badge-dismiss-default" aria-label="Remove">
                                    <svg class="w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                </button>
                            </span>
                        </div>
                        <InputError class="mt-2" :message="form.errors.skills" />
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div>
                        <InputLabel for="description" value="Детальное описание профиля" />
                        <div class="mt-2">
                            <textarea v-model="form.about_text" id="description" name="about" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                        </div>
                        <InputError class="mt-2" :message="form.errors.about_text" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="phone" value="Номер телефона" />
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 top-0 flex items-center pl-2.5 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.427 14.768 17.2 13.542a1.733 1.733 0 0 0-2.45 0l-.613.613a1.732 1.732 0 0 1-2.45 0l-1.838-1.84a1.735 1.735 0 0 1 0-2.452l.612-.613a1.735 1.735 0 0 0 0-2.452L9.237 5.572a1.6 1.6 0 0 0-2.45 0c-3.223 3.2-1.702 6.896 1.519 10.117 3.22 3.221 6.914 4.745 10.12 1.535a1.601 1.601 0 0 0 0-2.456Z"/>
                                </svg>
                            </div>
                            <input id="phone" v-model="form.contact_phone" type="text" aria-describedby="helper-text-explanation" class="mt-1 border border-gray-300 text-gray-900 rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full ps-8  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="+7 (999) 999-99-99"  />
                        </div>
                        <InputError class="mt-2" :message="form.errors.contact_phone" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="tg" value="Telegram" />

                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-sm">@</span>
                            </div>
                            <input id="tg" v-model="form.contact_telegram" type="text" aria-describedby="helper-text-explanation" class="mt-1 border border-gray-300 text-gray-900 rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full ps-8 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="username"  />
                        </div>
                        <InputError class="mt-2" :message="form.errors.contact_telegram" />
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Сохранить изменения
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Сохраненные данные будут обновлены
                        </p>
                    </header>

                    <div class="flex items-center gap-4 mt-4">
                        <PrimaryButton :disabled="form.processing">Сохранить</PrimaryButton>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Информация обновлена.</p>
                        </Transition>
                    </div>
                </div>
            </div>
        </form>
    </section>
</template>
