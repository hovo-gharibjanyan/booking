<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    bookings: Array,
});

const statusClasses = computed(() => {
    return (status) => {
        if (status === 'paid') {
            return 'bg-green-100 text-green-800';
        }
        if (status === 'cancelled') {
            return 'bg-red-100 text-red-800';
        }
        // По умолчанию (для 'reserved')
        return 'bg-yellow-100 text-yellow-800';
    };
});

const handleCancel = () => {
    return confirm('Вы уверены, что хотите отменить эту бронь?');
};

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Мои Бронирования</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="bookings.length > 0" class="p-6 text-gray-900">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тур</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата брони</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Мест</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сумма</th>
                                    
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Действия</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="booking in bookings" :key="booking.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ booking.tour.title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ booking.booking_date }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ booking.seats }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="statusClasses(booking.status)">
                                            {{ booking.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ (booking.total_minor / 100).toFixed(2) }} у.е.
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a :href="route('bookings.voucher', booking.id)" class="text-indigo-600 hover:text-indigo-900">
                                            Ваучер
                                        </a>
                                        <Link 
                                            v-if="booking.status === 'reserved'"
                                            :href="route('bookings.cancel', booking.id)" 
                                            method="post" 
                                            as="button"
                                            class="text-red-600 hover:text-red-900"
                                            :onBefore="handleCancel"
                                        >
                                            Отменить
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="p-6 text-gray-900">
                        У вас пока нет активных бронирований. Пора это исправить!
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>