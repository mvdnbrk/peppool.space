<template>
    <div class="relative max-w-xl">
        <form @submit.prevent="submit" class="relative group">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-grow">
                    <input 
                        ref="emailInput"
                        v-model="email"
                        type="email" 
                        required 
                        placeholder="Enter your email" 
                        :disabled="isLoading || isSuccess"
                        class="w-full px-5 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 outline-none transition-all text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 disabled:opacity-50"
                    >
                </div>
                <button 
                    type="submit" 
                    :disabled="isLoading || isSuccess"
                    class="px-8 py-3 bg-green-700 hover:bg-green-800 disabled:bg-green-700/50 cursor-pointer text-white font-bold rounded-xl transition-all shadow-lg shadow-green-700/20 active:scale-95 whitespace-nowrap flex items-center justify-center gap-2 tracking-wider uppercase text-xs"
                >
                    <span v-if="isLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span>{{ buttonText }}</span>
                </button>
            </div>
        </form>

        <!-- Feedback Messages -->
        <transition 
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
        >
            <div v-if="error" class="absolute top-full mt-2 text-sm text-red-600 font-medium">
                {{ error }}
            </div>
            <div v-else-if="isSuccess" class="absolute top-full mt-2 text-sm text-green-600 font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Awesome! You're on the list.
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    apiUrl: {
        type: String,
        required: true
    }
});

const emailInput = ref(null);
const email = ref('');
const isLoading = ref(false);
const isSuccess = ref(false);
const error = ref('');

const buttonText = computed(() => {
    if (isLoading.value) return 'Joining...';
    if (isSuccess.value) return 'Joined!';
    return 'Join Waitlist';
});

const handleHashFocus = () => {
    if (window.location.hash === '#join-waitlist') {
        // Use a slight delay to ensure the scroll has happened or is happening
        setTimeout(() => {
            emailInput.value?.focus();
        }, 100);
    }
};

onMounted(() => {
    handleHashFocus();
    window.addEventListener('hashchange', handleHashFocus);
});

onUnmounted(() => {
    window.removeEventListener('hashchange', handleHashFocus);
});

const submit = async () => {
    isLoading.value = true;
    error.value = '';

    try {
        await axios.post(props.apiUrl, {
            email: email.value
        });
        isSuccess.value = true;
        email.value = '';
    } catch (e) {
        if (e.response && e.response.data && e.response.data.errors) {
            error.value = Object.values(e.response.data.errors)[0][0];
        } else if (e.response && e.response.data && e.response.data.message) {
            error.value = e.response.data.message;
        } else {
            error.value = 'Something went wrong. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
};
</script>
