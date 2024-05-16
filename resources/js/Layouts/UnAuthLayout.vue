<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-gray-800">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-white"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden md:block">
                                <div class="ml-10 flex items-baseline space-x-4">
                                    <NavLink :href="route('jobs.index')" :active="route().current('jobs.index')">
                                        Jobs
                                    </NavLink>
                                    <NavLink :href="route('chat.index')" :active="route().current('chat.index')">
                                        Chat
                                    </NavLink>
                                    <NavLink :href="route('admin.index')" :active="route().current('admin.index')">
                                        Admin panel
                                    </NavLink>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="ml-4 flex items-center md:ml-6 gap-3">
                                <NavLink :href="route('jobs.create')" :active="route().current('jobs.create')">
                                    Разместить заказ
                                </NavLink>

                                <div class="ms-3 relative">
                                    <Dropdown align="right" width="48" class="">
                                        <template #trigger>
                                            <span class=" rounded-md">
                                                <button type="button"
                                                        class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                                                        id="user-menu-button" aria-expanded="false"
                                                        aria-haspopup="true">
                                                    <span class="absolute -inset-1.5"></span>
                                                    <span class="sr-only">Open user menu</span>
                                                    <div
                                                        class="relative w-9 h-9 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                                        <svg class="absolute w-11 h-11 text-gray-400 -left-1"
                                                             fill="currentColor" viewBox="0 0 20 20"
                                                             xmlns="http://www.w3.org/2000/svg"><path
                                                            fill-rule="evenodd"
                                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                            clip-rule="evenodd"></path></svg>
                                                    </div>

                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <div class=" divide-y divide-gray-100 rounded-lg">
                                                <div class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                                    <div>login</div>
                                                    <div class="font-medium truncate">
                                                        email
                                                    </div>
                                                </div>
                                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                                    aria-labelledby="avatarButton">
                                                    <li>
                                                        <DropdownLink href="/user/">
                                                            Профиль
                                                        </DropdownLink>
                                                    </li>
                                                    <li>
                                                        <DropdownLink :href="route('jobs.manage')"> Мои заказы
                                                        </DropdownLink>
                                                    </li>
                                                    <li>
                                                        <DropdownLink :href="route('offer.index')"> Мои предложения
                                                        </DropdownLink>
                                                    </li>
                                                    <li>
                                                        <DropdownLink :href="route('profile.edit')"> Настройки
                                                        </DropdownLink>
                                                    </li>
                                                    <li>
                                                        <DropdownLink :href="route('logout')" method="post" as="button"
                                                                      class="block w-full px-4 py-2 text-sm text-left text-red-700 dark:hover:text-white dark:hover:bg-red-600 hover:bg-red-100">
                                                            Выйти
                                                        </DropdownLink>
                                                    </li>
                                                </ul>
                                            </div>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                <!--                                {{ $page.props.auth.user.login }}-->
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                <!--                                {{ $page.props.auth.user.email }}-->
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->


            <!-- Page Content -->
            <main>
                <slot/>
            </main>
        </div>
    </div>
</template>
