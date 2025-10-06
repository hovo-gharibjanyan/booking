<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    bookings: Array,
});

const getStatusData = (status) => {
    if (status === 'paid') {
        return {
            label: 'Оплачено',
            color: 'bg-gradient-to-r from-green-500 to-emerald-500',
            icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        };
    }
    if (status === 'cancelled') {
        return {
            label: 'Отменено',
            color: 'bg-gradient-to-r from-red-500 to-pink-500',
            icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
        };
    }
    return {
        label: 'Забронировано',
        color: 'bg-gradient-to-r from-yellow-500 to-orange-500',
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
    };
};

const handleCancel = () => {
    return confirm('Вы уверены, что хотите отменить эту бронь?');
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Мои Бронирования</h2>
        </template>

        <!-- Main Content -->
        <div class="py-12 bg-gradient-to-b from-gray-50 via-white to-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Bookings Grid -->
                <div v-if="bookings.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="booking in bookings" :key="booking.id" 
                         class="group bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-gray-100 hover:border-indigo-200 transform hover:-translate-y-1">
                        
                        <!-- Image Section -->
                        <div class="relative h-64 overflow-hidden">
                            <img v-if="booking.tour.images && booking.tour.images.length > 0"
                                 :src="`/storage/${booking.tour.images[0].url}`" 
                                 :alt="booking.tour.title"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div v-else class="w-full h-full bg-gradient-to-br from-indigo-400 to-purple-400"></div>
                            
                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4">
                                <div :class="[getStatusData(booking.status).color, 'text-white px-5 py-2 rounded-full font-bold text-sm shadow-lg flex items-center gap-2']">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStatusData(booking.status).icon"></path>
                                    </svg>
                                    <span>{{ getStatusData(booking.status).label }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="p-8">
                            <!-- Title -->
                            <h3 class="text-2xl font-black text-gray-900 mb-4 group-hover:text-indigo-600 transition-colors">
                                {{ booking.tour.title }}
                            </h3>

                            <!-- Info Grid -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-50 rounded-2xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-500 uppercase">Дата</span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900">{{ booking.booking_date }}</p>
                                </div>

                                <div class="bg-gray-50 rounded-2xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-500 uppercase">Гостей</span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900">{{ booking.seats }}</p>
                                </div>
                            </div>

                            <!-- Price Section -->
                            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 mb-6 border-2 border-indigo-100">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-semibold text-gray-600">Общая сумма</span>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-3xl font-black text-gray-900">{{ (booking.total_minor / 100).toFixed(2) }}</span>
                                        <span class="text-lg font-semibold text-gray-600">₽</span>
                                    </div>
                                </div>
                                <div v-if="booking.refund_amount_minor > 0" class="flex items-center justify-between pt-3 border-t-2 border-indigo-200">
                                    <span class="text-sm font-semibold text-gray-600">Сумма возврата</span>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-xl font-bold text-green-600">{{ (booking.refund_amount_minor / 100).toFixed(2) }}</span>
                                        <span class="text-sm font-semibold text-green-600">₽</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-3">
                                <a v-if="booking.status === 'paid'" 
                                   :href="route('bookings.voucher', booking.id)" 
                                   class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4 rounded-2xl font-bold text-center hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Скачать ваучер
                                </a>

                                <Link v-if="booking.status === 'reserved'" 
                                      :href="route('bookings.payment.create', booking.id)" 
                                      as="button" 
                                      class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-4 rounded-2xl font-bold text-center hover:from-green-700 hover:to-emerald-700 shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Оплатить
                                </Link>

                                <Link v-if="booking.status === 'reserved' || booking.status === 'paid'"
                                      :href="route('bookings.cancelBooking', booking.id)" 
                                      method="post" 
                                      as="button" 
                                      :onBefore="handleCancel"
                                      class="bg-white border-2 border-red-200 text-red-600 px-6 py-4 rounded-2xl font-bold hover:bg-red-50 hover:border-red-300 transition-all duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Отменить
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-20">
                    <div class="max-w-md mx-auto bg-white rounded-3xl shadow-2xl p-12">
                        <div class="w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 mb-4">
                            Пока пусто...
                        </h3>
                        <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                            У вас пока нет активных бронирований. Пора отправиться в путешествие!
                        </p>
                        <Link :href="route('tours.index')" 
                              class="inline-block bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                            Выбрать тур
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>