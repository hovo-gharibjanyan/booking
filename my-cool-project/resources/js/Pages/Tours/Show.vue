<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    tour: Object,
});

const form = useForm({
    tour_id: props.tour.id,
    customer_phone: '', 
    booking_date: '',
    seats: 1,
});

const availableSeats = ref(null);

watch(() => form.booking_date, async (newDate) => {
    if (newDate) {
        availableSeats.value = 'Загрузка...';
        try {
            const response = await fetch(route('tours.availability', { tour: props.tour.id, date: newDate }));
            const data = await response.json();
            availableSeats.value = data.available_seats;
        } catch (error) {
            console.error('Ошибка при проверке свободных мест:', error);
            availableSeats.value = 'Ошибка';
        }
    } else {
        availableSeats.value = null;
    }
});

const submit = () => {
    form.post(route('bookings.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head :title="tour.title" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <h1 class="text-4xl font-extrabold text-gray-900 mb-4">{{ tour.title }}</h1>
                            <p class="text-gray-700 text-lg mb-6">{{ tour.description }}</p>

                            <div class="grid grid-cols-2 gap-4 text-lg">
                                <div class="font-semibold">Цена:</div>
                                <div>{{ (tour.price_minor / 100).toFixed(2) }} у.е. (за место)</div>

                                <div class="font-semibold">Макс. мест:</div>
                                <div>{{ tour.max_seats }}</div>
                            </div>
                        </div>

                        <div>
                            <div v-if="$page.props.auth.user" class="p-6 rounded-lg ring-1 ring-gray-200">
                                <h2 class="text-2xl font-bold mb-4">Забронировать тур</h2>
                                <form @submit.prevent="submit">
                                    <div class="mt-4">
                                        <InputLabel for="customer_phone" value="Ваш контактный телефон" />
                                        <TextInput id="customer_phone" v-model="form.customer_phone" type="tel" class="mt-1 block w-full" required />
                                        <InputError class="mt-2" :message="form.errors.customer_phone" />
                                    </div>
                                    <div class="mt-4">
                                        <InputLabel for="booking_date" value="Выберите дату" />
                                        <select id="booking_date" v-model="form.booking_date" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                            <option value="" disabled>-- Выберите --</option>
                                            <option v-for="date in JSON.parse(tour.available_dates)" :key="date" :value="date">
                                                {{ date }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.booking_date" />
                                        <div v-if="availableSeats !== null" class="mt-2 text-sm text-gray-600">
                                            Свободных мест: <span class="font-bold">{{ availableSeats }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <InputLabel for="seats" value="Количество мест" />
                                        <TextInput id="seats" v-model="form.seats" type="number" min="1" :max="tour.max_seats" class="mt-1 block w-full" required />
                                        <InputError class="mt-2" :message="form.errors.seats" />
                                    </div>
                                    <div class="flex items-center justify-end mt-6">
                                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                            Забронировать
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                            <div v-else class="p-8 rounded-lg ring-1 ring-indigo-300 bg-indigo-50 text-center">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4">Готовы к приключениям?</h2>
                                <p class="text-gray-600 mb-6">
                                    Чтобы забронировать этот тур, пожалуйста, войдите в свой аккаунт или зарегистрируйтесь.
                                </p>
                                <div class="flex justify-center gap-4">
                                    <Link :href="route('login')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                        Войти
                                    </Link>
                                    <Link :href="route('register')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                                        Регистрация
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>