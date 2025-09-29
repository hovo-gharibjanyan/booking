<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
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
    customer_name: '',
    customer_email: '',
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

    <GuestLayout>
        <div class="max-w-4xl mx-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- Левая колонка: Информация о туре -->
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

            <!-- Правая колонка: Форма бронирования -->
            <div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-4">Забронировать тур</h2>
                    <form @submit.prevent="submit">
                        
                        <!-- ===== ВОТ ЭТИ БЛОКИ, СКОРЕЕ ВСЕГО, ПРОПАЛИ ===== -->

                        <!-- Поле Имя -->
                        <div>
                            <InputLabel for="customer_name" value="Ваше имя" />
                            <TextInput id="customer_name" v-model="form.customer_name" type="text" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.customer_name" />
                        </div>

                        <!-- Поле Email -->
                        <div class="mt-4">
                            <InputLabel for="customer_email" value="Email" />
                            <TextInput id="customer_email" v-model="form.customer_email" type="email" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.customer_email" />
                        </div>

                        <!-- Поле Телефон -->
                        <div class="mt-4">
                            <InputLabel for="customer_phone" value="Телефон" />
                            <TextInput id="customer_phone" v-model="form.customer_phone" type="tel" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.customer_phone" />
                        </div>

                        <!-- ===== КОНЕЦ ПРОПАВШИХ БЛОКОВ ===== -->

                        <!-- Выбор даты -->
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

                        <!-- Количество мест -->
                        <div class="mt-4">
                            <InputLabel for="seats" value="Количество мест" />
                            <TextInput id="seats" v-model="form.seats" type="number" min="1" :max="tour.max_seats" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.seats" />
                        </div>

                        <!-- ===== ЭТОТ БЛОК ТОЖЕ МОГ ПРОПАСТЬ ===== -->
                        <!-- Кнопка отправки -->
                        <div class="flex items-center justify-end mt-6">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Забронировать
                            </PrimaryButton>
                        </div>
                        <!-- ==================================== -->
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>