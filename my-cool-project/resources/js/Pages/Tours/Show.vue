<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'; // <-- Используем наш главный лэйаут
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import FavoriteButton from '@/Components/FavoriteButton.vue';
import VoteButtons from '@/Components/VoteButtons.vue';

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
const parallaxOffset = ref(0);

// Parallax scroll effect
const handleScroll = () => {
    parallaxOffset.value = window.scrollY * 0.5;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

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
        <!-- Hero Section with Parallax Effect -->
        <section 
            v-if="tour.images && tour.images.length > 0"
            class="relative w-full overflow-hidden"
            style="height: 80vh;"
        >
            <div 
                class="absolute inset-0 bg-cover bg-center brightness-50"
                :style="{
                    backgroundImage: `url(/storage/${tour.images[0].url})`,
                    transform: `translateY(${parallaxOffset}px)`
                }"
            ></div>
            
            <!-- Favorite Button on Hero Image -->
            <FavoriteButton v-if="$page.props.auth.user" :tour="tour" :is-favorited="tour.is_favorited" />
            
        </section>

        <!-- Title Section with Overlap -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-32 relative z-20">
            <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="flex-1">
                        <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                            {{ tour.title }}
                        </h1>
                        <p v-if="tour.description" class="text-lg text-gray-600 leading-relaxed">
                            {{ tour.description }}
                        </p>
                        <VoteButtons v-if="$page.props.auth.user" :tour="tour" :user-vote="tour.user_vote" />
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <div class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-full font-semibold text-sm flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ tour.duration || 'Полный день' }}</span>
                        </div>
                        <div class="bg-green-50 text-green-700 px-4 py-2 rounded-full font-semibold text-sm flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>До {{ tour.max_seats }} гостей</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="content" class="py-16 bg-gradient-to-b from-white via-gray-50 to-white">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Image Gallery -->
                        <div v-if="tour.images && tour.images.length > 1" class="bg-white rounded-3xl shadow-lg overflow-hidden">
                            <div class="grid grid-cols-2 gap-2 p-4">
                                <div v-for="(image, index) in tour.images.slice(1, 5)" :key="image.id" 
                                     class="relative h-48 rounded-xl overflow-hidden group cursor-pointer">
                                    <img :src="`/storage/${image.url}`" :alt="`Tour image ${index + 2}`" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Host Section -->
                        <div v-if="tour.host" class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-3xl shadow-lg p-8 border border-indigo-100">
                            <div class="flex items-start space-x-6">
                                <div class="relative">
                                    <img :src="tour.host.avatar_url" class="h-24 w-24 rounded-2xl ring-4 ring-white shadow-xl" alt="Host Avatar">
                                    <div class="absolute -bottom-2 -right-2 bg-green-500 w-6 h-6 rounded-full border-4 border-white"></div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider bg-white px-3 py-1 rounded-full">Ваш гид</span>
                                    </div>
                                    <h2 class="text-3xl font-black text-gray-900 mb-3">{{ tour.host.name }}</h2>
                                    <p class="text-gray-700 leading-relaxed">{{ tour.host.bio }}</p>
                                    <div class="flex items-center gap-4 mt-4">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <span class="text-sm font-bold text-gray-700">4.9</span>
                                        </div>
                                        <span class="text-sm text-gray-500">•</span>
                                        <span class="text-sm text-gray-600 font-medium">150+ туров</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activities Section -->
                        <div class="bg-white rounded-3xl shadow-lg p-8 border-t-4 border-indigo-500">
                             <div class="flex items-center gap-3 mb-8">
                                 <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                 </svg>
                                 <h2 class="text-3xl font-black text-gray-900">Программа тура</h2>
                             </div>
                             <div class="space-y-5">
                                 <div v-for="(activity, index) in tour.activities" :key="activity.id" 
                                      class="group relative bg-gradient-to-br from-gray-50 to-white p-6 rounded-2xl border-2 border-gray-100 hover:border-indigo-200 hover:shadow-lg transition-all duration-300">
                                     <div class="flex items-start gap-5">
                                         <div class="flex-shrink-0">
                                             <div class="w-12 h-12 bg-indigo-600 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                 {{ index + 1 }}
                                             </div>
                                         </div>
                                         <div class="flex-1">
                                             <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">{{ activity.title }}</h3>
                                             <p class="text-gray-600 leading-relaxed">{{ activity.description }}</p>
                                         </div>
                                         <img v-if="activity.image_url" :src="`/storage/${activity.image_url}`" 
                                              class="hidden md:block w-32 h-32 rounded-xl object-cover shadow-md group-hover:shadow-xl transition-shadow duration-300">
                                     </div>
                                 </div>
                             </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div id="booking" class="sticky top-24 bg-white p-8 rounded-3xl shadow-2xl border-2 border-gray-100">
                            
                            <div v-if="$page.props.auth.user">
                                <!-- Price Badge -->
                                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white p-6 rounded-2xl mb-6 shadow-lg">
                                    <p class="text-sm font-semibold uppercase tracking-wide mb-1">От</p>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-5xl font-black">{{ tour.price_minor || '9,999' }}</span>
                                        <span class="text-xl font-semibold">₽</span>
                                    </div>
                                    <p class="text-sm text-indigo-100 mt-2">на человека</p>
                                </div>
                                
                                <h2 class="text-2xl font-bold mb-6 text-gray-900">Забронировать тур</h2>
                                <form @submit.prevent="submit" class="space-y-5">
                                    <div>
                                        <InputLabel for="customer_phone" value="📱 Ваш контактный телефон" class="font-semibold" />
                                        <TextInput id="customer_phone" v-model="form.customer_phone" type="tel" class="mt-2 block w-full rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" required placeholder="+7 (999) 123-45-67" />
                                        <InputError class="mt-2" :message="form.errors.customer_phone" />
                                    </div>
                                    <div>
                                        <InputLabel for="booking_date" value="📅 Выберите дату" class="font-semibold" />
                                        <select id="booking_date" v-model="form.booking_date" class="mt-2 block w-full border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-xl shadow-sm py-3" required>
                                            <option value="" disabled>-- Выберите дату --</option>
                                            <option v-for="tourDate in tour.dates" :key="tourDate.id" :value="tourDate.date">
                                                {{ tourDate.date }}
                                            </option>
                                        </select>
                                        <div v-if="availableSeats !== null" class="mt-3 flex items-center gap-2 bg-green-50 text-green-700 px-4 py-2 rounded-lg">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-semibold">Свободно мест: {{ availableSeats }}</span>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.booking_date" />
                                    </div>
                                    <div>
                                        <InputLabel for="seats" value="👥 Количество гостей" class="font-semibold" />
                                        <TextInput id="seats" v-model="form.seats" type="number" min="1" :max="tour.max_seats" class="mt-2 block w-full rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" required />
                                        <InputError class="mt-2" :message="form.errors.seats" />
                                    </div>
                                    <div class="pt-2">
                                        <PrimaryButton 
                                            :class="{ 'opacity-25': form.processing }" 
                                            :disabled="form.processing"
                                            class="w-full py-5 text-lg font-bold rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-xl hover:shadow-2xl transform hover:scale-[1.02] transition-all duration-200"
                                        >
                                            <span class="flex items-center justify-center gap-2">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Забронировать сейчас
                                            </span>
                                        </PrimaryButton>
                                        <p class="text-center text-xs text-gray-500 mt-3">Бесплатная отмена за 24 часа</p>
                                    </div>
                                </form>
                            </div>

                            <div v-else class="text-center">
                                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-8 rounded-2xl mb-6">
                                    <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Готовы к приключениям?</h2>
                                    <p class="text-gray-600 leading-relaxed">
                                        Войдите или зарегистрируйтесь, чтобы забронировать этот незабываемый тур
                                    </p>
                                </div>
                                <div class="space-y-3">
                                    <Link :href="route('login')" class="block w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-bold text-lg shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                                        Войти
                                    </Link>
                                    <Link :href="route('register')" class="block w-full py-4 bg-white border-2 border-gray-200 text-gray-700 rounded-2xl font-bold text-lg hover:border-indigo-300 hover:bg-gray-50 transition-all duration-200">
                                        Создать аккаунт
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

