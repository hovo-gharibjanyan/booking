<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
// ... импорты для полей ввода ...

const form = useForm({
    title: '',
    description: '',
    image: null, // Для загрузки файла
    price_minor: 0,
    max_seats: 10,
    available_dates: [],
});

const submit = () => {
    // Отправляем данные на метод store в контроллере
    form.post(route('admin.tours.store'));
};
</script>

<template>
    <Head title="Создать тур" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl ...">Создание нового тура</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto ...">
                <form @submit.prevent="submit">
                    <!-- Поле для Названия -->
                    <!-- Поле для Описания (textarea) -->
                    <!-- Поле для Загрузки картинки (<input type="file">) -->
                    <!-- Поле для Цены -->
                    <!-- ... и так далее ... -->
                    <button type="submit" :disabled="form.processing">Сохранить</button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>