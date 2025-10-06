<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';



const props = defineProps({
    hosts: Array,
    tour: {
        type: Object,
        default: null,
    }
    
});

const isEditing = !!props.tour;

const form = useForm({
    title: props.tour?.title ?? '',
    description: props.tour?.description ?? '',
    price_minor: props.tour?.price_minor ?? 0,
    max_seats: props.tour?.max_seats ?? 10,
    host_id: props.tour?.host_id ?? null,
    images: [],
    activities: [],
    dates: [],
});

const submit = () => {
    if (isEditing) {
        form.put(route('admin.tours.update', props.tour.id), {
            _method: 'put',
        });
    } else {
        form.post(route('admin.tours.store'));
    }
};

const addActivity = () => {
    form.activities.push({ title: '', description: '', image: null });
};
const removeActivity = (index) => {
    form.activities.splice(index, 1);
};

const addDate = () => {
    form.dates.push('');
};
const removeDate = (index) => {
    form.dates.splice(index, 1);
};

// --- ОБРАБОТЧИКИ ФАЙЛОВ ---
const handleImagesUpload = (event) => {
    form.images = Array.from(event.target.files); // Превращаем FileList в массив
};
const handleActivityImageUpload = (event, index) => {
    form.activities[index].image = event.target.files[0];
};


</script>

<template>
    <Head title="Создание тура" />

    <AppLayout>
        <template #header>
            <!-- <h2 class="font-semibold text-xl text-gray-800 leading-tight">Создание нового тура</h2> -->
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ isEditing ? 'Редактирование тура' : 'Создание нового тура' }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Блок основной информации -->
                            <div class="space-y-4 p-4 border rounded-md">
                                <h3 class="font-semibold text-lg">Основная информация</h3>
                                <div>
                                    <InputLabel for="title" value="Название тура" />
                                    <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required />
                                    <InputError class="mt-2" :message="form.errors.title" />
                                </div>
                                <div>
                                    <InputLabel for="description" value="Краткое описание (для карточки)" />
                                    <textarea id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" v-model="form.description" rows="3"></textarea>
                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="price_minor" value="Цена (в центах)" />
                                        <TextInput id="price_minor" type="number" class="mt-1 block w-full" v-model="form.price_minor" required />
                                        <InputError class="mt-2" :message="form.errors.price_minor" />
                                    </div>
                                    <div>
                                        <InputLabel for="max_seats" value="Макс. мест" />
                                        <TextInput id="max_seats" type="number" class="mt-1 block w-full" v-model="form.max_seats" required />
                                        <InputError class="mt-2" :message="form.errors.max_seats" />
                                    </div>
                                </div>
                                <div>
                                    <InputLabel for="host_id" value="Ведущий тура" />
                                    <select id="host_id" v-model="form.host_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option :value="null">-- Не выбран --</option>
                                        <option v-for="host in hosts" :key="host.id" :value="host.id">
                                            {{ host.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.host_id" />
                                </div>
                            </div>
                            
                            <div class="p-4 border rounded-md">
                                <InputLabel for="images" value="Галерея изображений (можно выбрать несколько)" />
                                <input id="images" type="file" @change="handleImagesUpload" multiple class="mt-1 block w-full" />
                                <InputError class="mt-2" :message="form.errors.images" />
                            </div>

                            <div class="space-y-4 p-4 border rounded-md">
                                <h3 class="font-semibold text-lg">Программа тура ("Что вы будете делать")</h3>
                                <div v-for="(activity, index) in form.activities" :key="index" class="p-4 border rounded-md space-y-2 relative bg-gray-50">
                                    <TextInput type="text" v-model="activity.title" placeholder="Заголовок (напр. Посещение Матенадарана)" class="w-full" />
                                    <textarea v-model="activity.description" placeholder="Описание" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                                    <input type="file" @change="handleActivityImageUpload($event, index)" class="w-full text-sm" />
                                    <img v-if="activity.imagePreview" :src="activity.imagePreview" class="h-20 w-auto rounded-md mt-2" alt="Preview">
                                    <button type="button" @click="removeActivity(index)" class="absolute top-2 right-2 text-2xl text-red-500 hover:text-red-700">&times;</button>
                                </div>
                                <button type="button" @click="addActivity" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Добавить активность</button>
                            </div>
                            
                            <div class="space-y-4 p-4 border rounded-md">
                                 <h3 class="font-semibold text-lg">Доступные даты</h3>
                                 <div v-for="(date, index) in form.dates" :key="index" class="flex items-center gap-2">
                                    <TextInput type="date" v-model="form.dates[index]" class="w-full" />
                                    <button type="button" @click="removeDate(index)" class="text-2xl text-red-500 hover:text-red-700">&times;</button>
                                 </div>
                                 <button type="button" @click="addDate" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Добавить дату</button>
                            </div>

                            <div class="flex justify-end mt-8">
                                <PrimaryButton :disabled="form.processing">Сохранить тур</PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>