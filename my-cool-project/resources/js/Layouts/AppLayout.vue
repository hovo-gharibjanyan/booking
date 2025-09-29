<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <Link href="/">
                            <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                        </Link>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <template v-if="$page.props.auth.user">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </NavLink>
                        </template>
                        <template v-else>
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

        <main>
             <div v-if="$page.props.flash && $page.props.flash.success" class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                    <strong class="font-bold">Успех!</strong>
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>
            </div>
            <div v-if="$page.props.flash && $page.props.flash.error" class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                    <strong class="font-bold">Ошибка!</strong>
                    <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                </div>
            </div>
            <slot />
        </main>
    </div>
</template>