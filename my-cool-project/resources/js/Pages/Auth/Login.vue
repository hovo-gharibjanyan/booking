<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { auth, googleProvider } from '@/firebase/config';
import { signInWithPopup } from "firebase/auth";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const handleGoogleLogin = () => {
    signInWithPopup(auth, googleProvider)
        .then(async (result) => {
            const user = result.user;
            const idToken = await user.getIdToken();
            const inertiaForm = useForm({ token: idToken });
            inertiaForm.post(route('google.login'));
        })
        .catch((error) => {
            console.error("Ошибка входа через Google:", error);
        });
};


</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
            <div class="flex items-center justify-center mt-4">
                <button
                    @click="handleGoogleLogin"
                    type="button"
                    aria-label="Войти через Google"
                    class="group inline-flex w-full sm:w-auto items-center justify-center gap-3 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition
                        hover:bg-gray-50 hover:shadow-md
                        focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                        disabled:opacity-60 disabled:cursor-not-allowed
                        dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-900"
                >
                    <!-- Google "G" icon -->
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303C33.588,33.91,29.108,37,24,37c-7.18,0-13-5.82-13-13s5.82-13,13-13
                        c3.313,0,6.332,1.262,8.594,3.316l5.657-5.657C34.607,5.109,29.62,3,24,3C12.955,3,4,11.955,4,23s8.955,20,20,20
                        s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                    <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.539,16.14,18.88,13,24,13c3.313,0,6.332,1.262,8.594,3.316l5.657-5.657
                        C34.607,5.109,29.62,3,24,3C16.318,3,9.827,7.164,6.306,14.691z"/>
                    <path fill="#4CAF50" d="M24,43c5.066,0,9.565-1.966,12.971-5.162l-6.004-4.904C29.686,34.985,27.01,36,24,36
                        c-5.071,0-9.339-3.275-10.861-7.817l-6.604,5.09C9.001,38.838,15.914,43,24,43z"/>
                    <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-1.632,4.731-6.078,8-11.303,8c-5.071,0-9.339-3.275-10.861-7.817
                        l-6.604,5.09C9.001,38.838,15.914,43,24,43c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                    </svg>

                    <span class="whitespace-nowrap">Войти через Google</span>
                </button>
            </div>

        </form>
    </GuestLayout>
</template>
