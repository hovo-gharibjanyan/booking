<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue'; // Используем существующий NavLink
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <!-- Навигационная панель (шапка) -->
            <nav class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Левая часть: Логотип -->
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <Link href="/">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>
                        </div>

                        <!-- Правая часть: Ссылки -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- "Умный" блок: показываем разные ссылки гостям и юзерам -->
                            <template v-if="$page.props.auth.user">
                                <!-- Ссылки для залогиненного пользователя -->
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                                <!-- Мы добавим кнопку Logout позже -->
                            </template>
                            <template v-else>
                                <!-- Ссылки для гостя -->
                                <NavLink :href="route('login')" :active="route().current('login')">
                                    Войти
                                </NavLink>
                                <NavLink :href="route('register')" :active="route().current('register')" class="ms-4">
                                    Регистрация
                                </NavLink>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Основной контент страницы -->
            <main>
                 <!-- Блок с флеш-сообщением -->
                 <div v-if="$page.props.flash && $page.props.flash.success" class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Успех!</strong>
                        <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                    </div>
                </div>

                <!-- Сюда будет вставляться контент наших страниц -->
                <slot />
            </main>
        </div>
    </div>
</template>