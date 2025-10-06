<script setup>
import { ref } from 'vue';

// "Реактивная" переменная, которая отвечает за показ/скрытие чата
const isOpen = ref(false);
// Переменная для хранения текста, который вводит пользователь
const userMessage = ref('');
// Массив для хранения истории сообщений
const messages = ref([
    { id: 1, text: 'Здравствуйте! Я ваш персональный ассистент BookingBot. Чем могу помочь?', sender: 'bot' }
]);


const sendMessage = async () => { 
    if (!userMessage.value.trim()) return;

    const currentMessage = userMessage.value;
    messages.value.push({ id: Date.now(), text: currentMessage, sender: 'user' });
    userMessage.value = '';

    messages.value.push({ id: 'thinking', text: '...', sender: 'bot' });

    try {
        const response = await fetch('/api/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: currentMessage })
        });

        if (!response.ok) throw new Error('Ошибка сервера');

        const data = await response.json();

        messages.value.pop();
        messages.value.push({ id: Date.now(), text: data.reply, sender: 'bot' });
    } catch (error) {
        console.error("Ошибка чата:", error);
        messages.value.pop();
        messages.value.push({ id: 'error', text: 'Не удалось получить ответ.', sender: 'bot' });
    }
};
</script>

<template>
    <!-- Плавающая кнопка-иконка -->
    <div class="fixed bottom-5 right-5 z-50">
        <button @click="isOpen = !isOpen" class="bg-indigo-600 text-white p-4 rounded-full shadow-lg hover:bg-indigo-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
        </button>
    </div>

    <!-- Окно чата (появляется, если isOpen === true) -->
    <div v-if="isOpen" class="fixed bottom-20 right-5 z-50 w-96 h-[500px] bg-white rounded-lg shadow-2xl flex flex-col">
        <!-- Шапка чата -->
        <div class="bg-indigo-600 text-white p-4 rounded-t-lg flex justify-between items-center">
            <h3 class="font-semibold">BookingBot Ассистент</h3>
            <button @click="isOpen = false" class="text-white hover:text-gray-200">&times;</button>
        </div>

        <!-- Окно с сообщениями -->
        <div class="flex-1 p-4 overflow-y-auto">
            <div v-for="message in messages" :key="message.id" class="mb-4">
                <div :class="message.sender === 'bot' ? 'text-left' : 'text-right'">
                    <div 
                        class="inline-block p-3 rounded-lg"
                        :class="message.sender === 'bot' ? 'bg-gray-200 text-gray-800' : 'bg-indigo-500 text-white'"
                    >
                        {{ message.text }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Форма для ввода сообщения -->
        <div class="p-4 border-t">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input 
                    v-model="userMessage"
                    type="text" 
                    placeholder="Спросите что-нибудь..." 
                    class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                >
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Отпр.
                </button>
            </form>
        </div>
    </div>
</template>