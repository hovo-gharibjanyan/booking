<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ tour: Object, userVote: String });

const localLikes = ref(props.tour.likes_count);
const localDislikes = ref(props.tour.dislikes_count);

const userVote = ref(props.userVote);

const vote = (type) => {
    if (router.processing) return;

    const originalVote = userVote.value;
    let originalLikes = localLikes.value;
    let originalDislikes = localDislikes.value;
    
    if (userVote.value === type) {
        type === 'like' ? localLikes.value-- : localDislikes.value--;
        userVote.value = null;
    } else {
        if(originalVote === 'like') localLikes.value--;
        if(originalVote === 'dislike') localDislikes.value--;
        
        type === 'like' ? localLikes.value++ : localDislikes.value++;
        userVote.value = type;
    }

    router.post(route('tours.vote', props.tour.id), {
        type: type,
    }, {
        preserveScroll: true,
        onError: () => {
            localLikes.value = originalLikes;
            localDislikes.value = originalDislikes;
            userVote.value = originalVote;
        },
    });
};
</script>

<template>
    <div class="flex items-center space-x-4">
        <button @click.prevent="vote('like')" class="flex items-center space-x-1 text-gray-500 hover:text-green-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" :class="{ 'text-green-500': userVote === 'like' }">
                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.562 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.821 2.333l-.414.622A2 2 0 006 10.333z" />
            </svg>
            <span class="font-semibold text-sm">{{ localLikes }}</span>
        </button>
        <button @click.prevent="vote('dislike')" class="flex items-center space-x-1 text-gray-500 hover:text-red-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" :class="{ 'text-red-500': userVote === 'dislike' }">
                <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.106-1.79l-.05-.025A4 4 0 0011.057 2H5.641a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.438 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.821-2.333l.414-.622A2 2 0 0014 9.667z" />
            </svg>
            <span class="font-semibold text-sm">{{ localDislikes }}</span>
        </button>
    </div>
</template>