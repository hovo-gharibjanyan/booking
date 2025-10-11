<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import FavoriteButton from '@/Components/FavoriteButton.vue';

defineProps({
    tours: Object,
});
</script>

<template>
    <Head title="Мои избранные туры" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">❤️ Мои Избранные Туры</h2>
        </template>

        <div class="py-12 bg-gradient-to-b from-gray-50 via-white to-gray-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Empty State -->
                <div v-if="tours.length === 0" class="text-center py-16">
                    <div class="bg-white rounded-3xl shadow-xl p-12 max-w-2xl mx-auto">
                        <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 mb-4">Нет избранных туров</h3>
                        <p class="text-lg text-gray-600 mb-8">
                            Вы еще не добавили ни одного тура в избранное. Начните исследовать наш каталог!
                        </p>
                        <Link :href="route('home')" class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Найти туры
                        </Link>
                    </div>
                </div>

                <!-- Tours Grid -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="tour in tours" :key="tour.id" class="group bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-gray-100 hover:border-indigo-300 transform hover:-translate-y-2">
                        <div class="relative h-64 overflow-hidden">
                            <Link :href="route('tours.show', tour.id)" class="block h-full">
                                <img 
                                    :src="tour.images && tour.images.length > 0 ? `/storage/${tour.images[0].url}` : 'https://picsum.photos/800/600'" 
                                    alt="Tour Image" 
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                            </Link>
                            
                            <!-- Favorite Button - Outside Link -->
                            <FavoriteButton :tour="tour" :is-favorited="tour.is_favorited" />
                            
                            <!-- Price Badge -->
                            <div class="absolute top-4 right-4 pointer-events-none">
                                <div class="bg-white/95 backdrop-blur-sm px-5 py-3 rounded-2xl shadow-xl">
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl font-black text-gray-900">{{ (tour.price_minor / 100).toFixed(0) }}</span>
                                        <span class="text-sm font-bold text-gray-600">₽</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <Link :href="route('tours.show', tour.id)" class="block p-6">
                            <h2 class="text-2xl font-black text-gray-900 mb-4 group-hover:text-indigo-600 transition-colors">
                                {{ tour.title }}
                            </h2>
                            
                            <div class="space-y-3 mb-5">
                                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 font-medium">Максимум гостей</p>
                                        <p class="text-sm font-bold text-gray-900">До {{ tour.max_seats }} человек</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 rounded-2xl font-bold text-center group-hover:from-indigo-700 group-hover:to-purple-700 shadow-lg group-hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2">
                                <span>Подробнее</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
