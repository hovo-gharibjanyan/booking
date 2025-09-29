<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'; // <-- ИСПОЛЬЗУЕМ ПРАВИЛЬНЫЙ ЛЭЙАУТ
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    tours: Array,
});
</script>

<template>
    <Head title="Каталог туров" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">
                    Каталог Туров ({{ tours.length }} шт.)
                </h1>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="tour in tours" :key="tour.id" :href="route('tours.show', tour.id)">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 h-full">
                            <div class="p-6">
                                <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ tour.title }}</h2>
                                <p class="text-gray-600 mb-4 line-clamp-3">{{ tour.description }}</p>
                                <div class="text-lg font-bold text-gray-900">
                                    {{ (tour.price_minor / 100).toFixed(2) }} у.е.
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>