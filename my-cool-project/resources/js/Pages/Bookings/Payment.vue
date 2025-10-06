<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    booking: Object,
});

const form = useForm({});

// --- НАЧАЛО НОВОЙ, ПРАВИЛЬНОЙ ЛОГИКИ GOOGLE PAY ---

// 1. Базовая конфигурация API Google Pay
const baseRequest = {
    apiVersion: 2,
    apiVersionMinor: 0
};

// 2. Конфигурация для получения токена (как будто мы работаем через Stripe)
// Это стандартная практика для тестов.
const tokenizationSpecification = {
    type: 'PAYMENT_GATEWAY',
    parameters: {
        gateway: 'example', // Используем 'example' для тестов
        gatewayMerchantId: 'exampleGatewayMerchantId'
    }
};

// 3. Описываем разрешенные типы карт
const cardPaymentMethod = {
    type: 'CARD',
    parameters: {
        allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
        allowedCardNetworks: ['VISA', 'MASTERCARD', 'AMEX']
    },
    tokenizationSpecification: tokenizationSpecification
};

// 4. Главный объект с информацией о платеже
const paymentDataRequest = {
    ...baseRequest,
    allowedPaymentMethods: [cardPaymentMethod],
    transactionInfo: {
        totalPriceStatus: 'FINAL',
        totalPrice: (props.booking.total_minor / 100).toFixed(2), // Берем нашу цену
        currencyCode: 'USD',
        countryCode: 'US' // Важно для правил
    },
    merchantInfo: {
        merchantName: 'BookingLite',
        merchantId: '12345678901234567890' // Тестовый ID, как в инструкции
    }
};

let paymentsClient; // Объявляем клиента здесь, чтобы он был доступен везде

// Эта функция будет вызвана, когда компонент загрузится
onMounted(() => {
    paymentsClient = new google.payments.api.PaymentsClient({ environment: 'TEST' });

    // --- НАЧАЛО ИСПРАВЛЕНИЯ ---
    const isReadyToPayRequest = { ...baseRequest, allowedPaymentMethods: [cardPaymentMethod] };

    paymentsClient.isReadyToPay(isReadyToPayRequest)
    // --- КОНЕЦ ИСПРАВЛЕНИЯ ---

        .then(function(response) {
            console.log('Ответ от isReadyToPay:', response);
            if (response.result) {
                const button = paymentsClient.createButton({
                    onClick: onGooglePaymentButtonClicked,
                    allowedPaymentMethods: [cardPaymentMethod]
                });
                document.getElementById('google-pay-button-container').appendChild(button);
            } else {
                // ... (наш код для вывода ошибки)
            }
        })
        .catch(function(err) {
            console.error('Критическая ошибка инициализации Google Pay:', err);
        });
});
// Эта функция вызывается при клике на кнопку
function onGooglePaymentButtonClicked() {
    // 7. Показываем шторку оплаты с нашими параметрами
    paymentsClient.loadPaymentData(paymentDataRequest)
        .then(function(paymentData){
            console.log('Платежные данные от Google:', paymentData);

            // 8. Отправляем тестовый токен на наш бэкенд
            const paymentToken = paymentData.paymentMethodData.tokenizationData.token;
            form.transform(() => ({
                payment_token: paymentToken
            })).post(route('bookings.payment.store', props.booking.id));

        }).catch(function(err){
            console.error('Ошибка оплаты:', err);
        });
}
// --- КОНЕЦ НОВОЙ ЛОГИКИ ---
</script>

<template>
    <Head :title="`Оплата брони #${booking.id}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8 text-gray-900">
                        <h2 class="text-2xl font-bold mb-4">Оплата бронирования</h2>
                        <div class="space-y-2 mb-6">
                            <p><strong>Тур:</strong> {{ booking.tour.title }}</p>
                            <p class="text-xl font-bold"><strong>К оплате:</strong> {{ (booking.total_minor / 100).toFixed(2) }} у.е.</p>
                        </div>

                        <div id="google-pay-button-container"></div>

                        <p v-if="form.processing" class="mt-4 text-sm text-gray-600">Отправка данных на сервер...</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>