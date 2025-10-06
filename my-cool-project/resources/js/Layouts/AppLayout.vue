<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Chatbot from '@/Components/Chatbot.vue'; // Наш чат-бот  
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('home')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('home')" :active="route().current('home')">
                                    Каталог
                                </NavLink>
                                <NavLink v-if="$page.props.auth.user && $page.props.auth.user.role !== 'admin'" :href="route('dashboard')" :active="route().current('dashboard')">
                                    Мои Брони
                                </NavLink>
                                <!-- Admin Links -->
                                <template v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'">
                                    <NavLink :href="route('admin.bookings.index')" :active="route().current('admin.bookings.index')">
                                        Админ: Брони
                                    </NavLink>
                                    <NavLink :href="route('admin.tours.index')" :active="route().current('admin.tours.index')">
                                        Админ: Туры
                                    </NavLink>
                                </template>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Login/Register Links for Guests -->
                            <template v-if="!$page.props.auth.user">
                                <NavLink :href="route('login')" :active="route().current('login')">
                                    Войти
                                </NavLink>
                                <NavLink :href="route('register')" :active="route().current('register')" class="ms-4">
                                    Регистрация
                                </NavLink>
                            </template>

                            <!-- Settings Dropdown for Logged In Users -->
                            <div v-else class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}
                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            </button>
                                        </span>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Профиль </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button"> Выйти </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <!-- ... (код гамбургера остается без изменений) ... -->
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <!-- ... (весь код мобильного меню мы сейчас тоже обновим) ... -->
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <!-- Блок с флеш-сообщениями -->
                <div v-if="$page.props.flash && ($page.props.flash.success || $page.props.flash.error)" class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
                    <div v-if="$page.props.flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">...</div>
                    <div v-if="$page.props.flash.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">...</div>
                </div>
                <slot />
            </main>
        </div>
        
        <!-- Наш чат-бот -->
        <Chatbot />
    </div>
</template>