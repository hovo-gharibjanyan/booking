<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    tour: Object,
    isFavorited: Boolean,
});

const localIsFavorited = ref(props.isFavorited);

const toggleFavorite = () => {
    const originalState = localIsFavorited.value;
    localIsFavorited.value = !localIsFavorited.value; 

    if (localIsFavorited.value) {
        router.post(route('tours.favorite', props.tour.id), {}, {
            preserveScroll: true,
            onError: () => {
                localIsFavorited.value = originalState; 
            }
        });
    } else {
        router.delete(route('tours.unfavorite', props.tour.id), {
            preserveScroll: true,
            onError: () => {
                localIsFavorited.value = originalState; 
            }
        });
    }
};

</script>

<template>
    <button 
        @click.prevent="toggleFavorite" 
        class="absolute top-4 left-4 z-10 p-3 bg-white/90 backdrop-blur-sm rounded-full hover:bg-white hover:scale-110 transition-all duration-200 shadow-lg"
    >
        <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-6 w-6 transition-all duration-200" 
            :class="localIsFavorited ? 'text-red-500 fill-current scale-110' : 'text-gray-700'" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.5l1.318-1.182a4.5 4.5 0 116.364 6.364L12 21.818l-7.682-7.682a4.5 4.5 0 010-6.364z" />
        </svg>
    </button>
</template>