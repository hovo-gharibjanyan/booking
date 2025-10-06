<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'; 
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    tours: Array,
});
</script>

<template>
    <Head title="Каталог туров" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Каталог Туров</h2>
        </template>

        <div class="py-12 bg-gradient-to-b from-gray-50 via-white to-gray-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="tour in tours.data" :key="tour.id" class="group bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-gray-100 hover:border-indigo-300 transform hover:-translate-y-2">
                        <Link :href="route('tours.show', tour.id)" class="block">
    
                        <div class="relative h-64 overflow-hidden">
                            <img 
                                :src="tour.images.length > 0 ? `/storage/${tour.images[0].url}` : 'https://picsum.photos/800/600'" 
                                alt="Tour Image" 
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                            
                            <!-- Price Badge -->
                            <div class="absolute top-4 right-4">
                                <div class="bg-white/95 backdrop-blur-sm px-5 py-3 rounded-2xl shadow-xl">
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl font-black text-gray-900">{{ (tour.price_minor / 100).toFixed(0) }}</span>
                                        <span class="text-sm font-bold text-gray-600">₽</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
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
                        </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>