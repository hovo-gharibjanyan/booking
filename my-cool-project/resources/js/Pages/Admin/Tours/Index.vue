<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    tours: Object, // Laravel Paginator приходит как объект
});

const handleDelete = () => {
    return confirm('Вы уверены? Это действие необратимо!');
};

</script>

<template>
    <Head title="Управление турами" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Управление турами
                </h2>
                <!-- Кнопка "Создать тур", которая ведет на страницу создания -->
                <Link :href="route('admin.tours.create')" class="px-4 py-2 bg-gray-800 text-white border rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700">
                    Создать тур
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Название</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Цена</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Макс. мест</th>
                                    <th class="relative px-6 py-3"><span class="sr-only">Действия</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Используем tours.data, т.к. это объект пагинации -->
                                <tr v-for="tour in tours.data" :key="tour.id">
                                    <td class="px-6 py-4">{{ tour.title }}</td>
                                    <td class="px-6 py-4">{{ (tour.price_minor / 100).toFixed(2) }} у.е.</td>
                                    <td class="px-6 py-4">{{ tour.max_seats }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <!-- Сюда добавим кнопки "Редактировать" / "Удалить" -->
                                        <Link :href="route('admin.tours.edit', tour.id)" class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </Link>

                                        <Link 
                                        :href="route('admin.tours.destroy', tour.id)"
                                        method="delete"
                                        as="button"
                                        class="text-red-600 hover:text-red-900"
                                        :onBefore="handleDelete"
                                        >
                                            Delete
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Здесь позже добавим пагинацию -->
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>