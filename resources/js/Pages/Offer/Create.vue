<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import DetailOfferCard from "@/Components/DetailOfferCard.vue";

const props = defineProps({
    job: Object,
});

const form = useForm({
    offer_description: '',
    offer_price: '',
    offer_date_deadline_days: '',
    job_id: props.job.id,
    customer_id: props.job.user_id
});

const submit = () => {
    form.post(route('offer.store'));
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Job detail
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-4">
                    <a href="/jobs" class="flex items-center gap-1">
                        <svg
                            class="w-6 h-6 text-gray-800 dark:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 12h14M5 12l4-4m-4 4 4 4"
                            />
                        </svg>
                        Назад
                    </a>
                </div>
                <div class="mt-4">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Детали проекта
                    </h2>
                </div>
                <div class="mt-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 flex flex-col gap-4">
                        <DetailOfferCard 
                            :job="job"> 
                        </DetailOfferCard>
                    </div>
                </div>
                <div class="mt-4">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Предложить услугу
                    </h2>
                </div>
                <div
                    class="mt-4 bg-white overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 flex flex-col gap-4">
                        <p>Как вы будете решать задачу клиента</p>

                        <ul>
                            <li>
                                Опишите свой релевантный опыт. Расскажите про
                                1-3 ваших кейса по решению подобных задач.
                            </li>
                            <li>
                                Укажите, как именно вы собираетесь выполнять это
                                задание. Опишите ключевые моменты.
                            </li>
                            <li>
                                Составляйте уникальные отклики, которые покажут
                                вашу компетентность и заинтересованность в
                                проекте. Не используйте шаблонные тексты
                            </li>
                        </ul>

                        <p>
                            Пройдите урок по работе на Бирже, научитесь писать
                            продающие отклики и увеличьте свои шансы на
                            получение заказа!
                        </p>

                        <form @submit.prevent="submit">
                            <div>
                                <div class="mt-2">
                                    <textarea
                                        required
                                        id="description"
                                        v-model="form.offer_description"
                                        rows="4"
                                        placeholder="Напишите как вы будете решать задачу клиента."
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-black focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    ></textarea>
                                </div>
                                <div class="sm:col-span-3 mt-4">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block font-medium text-sm text-gray-700" for="price" >
                                                <span>Цена</span>
                                            </label>
                                            <input
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                                v-model="form.offer_price"
                                                id="price"
                                                type="text"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <label class="block font-medium text-sm text-gray-700" for="deadline">
                                                <span>Когда заказ должен быть готов</span>
                                            </label>
                                            <div class="mt-1 w-full">
                                                <select v-model="form.offer_date_deadline_days" id="deadline" name="deadline" required class="block w-full rounded-md border-0 mt-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
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
                                </div>
                                <div class="flex items-center justify-end mt-6">
                                    <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-4">
                                        Предложить услугу
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
