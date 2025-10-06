<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3'; // <-- 1. Добавляем импорт Link
import { computed } from 'vue';

const props = defineProps({
    bookings: Array,
});

// Наша "умная" функция для стилизации статусов
const statusClasses = computed(() => {
    return (status) => {
        if (status === 'paid') {
            return 'bg-green-100 text-green-800';
        }
        if (status === 'cancelled') {
            return 'bg-red-100 text-red-800';
        }
        return 'bg-yellow-100 text-yellow-800';
    };
});

// Новая "умная" функция, чтобы проверять, есть ли у брони ожидающие транзакции
const hasPendingTransaction = (booking) => {
    return booking.transactions && booking.transactions.some(t => t.status === 'pending');
};

</script>

<template>
    <Head title="Управление бронями" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Управление бронированиями</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Клиент</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Тур</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                                    <th class="relative px-6 py-3"><span class="sr-only">Действия</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="booking in bookings" :key="booking.id">
                                    <td class="px-6 py-4">
                                        <span v-if="booking.user">{{ booking.user.name }}</span>
                                        <span v-else class="text-gray-400 italic">{{ booking.customer_name }} (Гость)</span>
                                    </td>
                                    <td class="px-6 py-4">{{ booking.tour.title }}</td>
                                    <td class="px-6 py-4">{{ booking.booking_date }}</td>
                                    
                                    <!-- ===== НАЧАЛО ИЗМЕНЕНИЙ В СТАТУСЕ ===== -->
                                    <td class="px-6 py-4">
                                        <!-- Если есть ожидающая транзакция, показываем особый статус -->
                                        <div v-if="hasPendingTransaction(booking)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Ожидает подтверждения
                                        </div>
                                        <!-- Иначе показываем обычный статус -->
                                        <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="statusClasses(booking.status)">
                                            {{ booking.status }}
                                        </span>
                                    </td>
                                    <!-- ===== КОНЕЦ ИЗМЕНЕНИЙ В СТАТУСЕ ===== -->

                                    <!-- ===== НАЧАЛО ИЗМЕНЕНИЙ В ДЕЙСТВИЯХ ===== -->
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <!-- Показываем кнопку, если есть хотя бы одна транзакция в ожидании -->
                                        <template v-if="hasPendingTransaction(booking)">
                                            <Link 
                                                :href="route('admin.bookings.confirm-payment', booking.id)"
                                                method="post"
                                                as="button"
                                                class="text-green-600 hover:text-green-900"
                                            >
                                                Подтвердить оплату
                                            </Link>
                                        </template>
                                    </td>
                                    <!-- ===== КОНЕЦ ИЗМЕНЕНИЙ В ДЕЙСТВИЯХ ===== -->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>