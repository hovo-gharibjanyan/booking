<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'; // <-- Используем наш главный лэйаут
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref , watch } from 'vue';

const props = defineProps({
    tour: Object,
});

const form = useForm({
    tour_id: props.tour.id,
    booking_date: '',
    seats: 1,
    customer_phone: '',
});
console.log('Приехали данные тура:', props.tour);

const availableSeats = ref(null);

watch(() => form.booking_date, async (newDate) => {
    if (newDate) {
        availableSeats.value = 'Загрузка...';
        try {
            const response = await fetch(route('tours.availability', { tour: props.tour.id, date: newDate }));
            if (!response.ok) throw new Error('Ошибка сети');
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
    form.post(route('bookings.store'));
};



</script>

<template>
    <Head :title="tour.title" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-4">
                    <h1 class="text-4xl font-extrabold text-gray-900">{{ tour.title }}</h1>
                </div>

                <div class="grid grid-cols-2 grid-rows-2 gap-2 h-96 mb-8" v-if="tour.images && tour.images.length > 0">
                    <div class="col-span-1 row-span-2">
                        <img :src="`/storage/${tour.images[0].url}`" alt="Tour Image 1" class="w-full h-full object-cover rounded-l-lg">
                    </div>
                    <div v-if="tour.images[1]" class="col-span-1 row-span-1">
                         <img :src="`/storage/${tour.images[1].url}`" alt="Tour Image 2" class="w-full h-full object-cover rounded-tr-lg">
                    </div>
                     <div v-if="tour.images[2]" class="col-span-1 row-span-1">
                         <img :src="`/storage/${tour.images[2].url}`" alt="Tour Image 3" class="w-full h-full object-cover rounded-br-lg">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-2">
                        <div v-if="tour.host" class="flex items-center space-x-4 pb-6 border-b">
                            <img :src="tour.host.avatar_url" class="h-16 w-16 rounded-full" alt="Host Avatar">
                            <div>
                                <h2 class="text-xl font-semibold">Ведущий: {{ tour.host.name }}</h2>
                                <p class="text-gray-600">{{ tour.host.bio }}</p>
                            </div>
                        </div>
                        <div class="py-6 border-b">
                             <h2 class="text-2xl font-bold mb-4">Программа тура</h2>
                             <div v-for="activity in tour.activities" :key="activity.id" class="flex items-start space-x-4 mb-4">
                                 <img v-if="activity.image_url" :src="`/storage/${activity.image_url}`" class="h-24 w-24 rounded-lg object-cover">
                                 <div>
                                     <h3 class="font-semibold">{{ activity.title }}</h3>
                                     <p class="text-gray-600">{{ activity.description }}</p>
                                 </div>
                             </div>
                        </div>
                    </div>

                    <div class="md:col-span-1">
                        <div class="sticky top-8 bg-white p-6 rounded-lg shadow-lg">
                            
                            <div v-if="$page.props.auth.user">
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
                                            
                                            <option v-for="tourDate in tour.dates" :key="tourDate.id" :value="tourDate.date">
                                                {{ tourDate.date }}
                                            </option>
                                        </select>
                                        <div v-if="availableSeats !== null" class="mt-2 text-sm text-gray-600">
                                            Свободных мест: <span class="font-bold text-lg">{{ availableSeats }}</span>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.booking_date" />
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

                            <div v-else class="text-center">
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