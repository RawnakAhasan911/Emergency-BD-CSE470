<template>
  <div class="min-h-screen bg-[#111] text-white font-mono flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-md p-8">
      
      <!-- Header Tabs: Swap between ACCESS (Login) and REGISTER -->
      <div class="flex justify-between items-center mb-8 border-b border-[#333] pb-4 select-none">
        <h2 
          @click="isLoginMode = true; errorMessage = ''"
          :class="isLoginMode ? 'text-rose-600' : 'text-gray-500 hover:text-white cursor-pointer'"
          class="text-2xl font-bold tracking-widest transition-colors"
        >
          ACCESS
        </h2>
        <h2 
          @click="isLoginMode = false; errorMessage = ''"
          :class="!isLoginMode ? 'text-rose-600' : 'text-gray-500 hover:text-white cursor-pointer'"
          class="text-2xl font-bold tracking-widest transition-colors"
        >
          REGISTER
        </h2>
      </div>

      <!-- Error Message Box (Feature 5: Feedback Messages) -->
      <div v-if="errorMessage" class="border border-rose-600 text-rose-600 p-4 mb-6 text-sm">
        [ SYSTEM ERROR ]: {{ errorMessage }}
      </div>

      <!-- ========================================== -->
      <!-- 1. LOGIN FORM                              -->
      <!-- ========================================== -->
      <form v-if="isLoginMode" @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label class="block text-sm text-gray-400 mb-2">Email Identification</label>
          <input v-model="email" type="email" required class="w-full bg-black border border-[#333] p-3 text-white focus:outline-none focus:border-rose-600">
        </div>

        <div>
          <label class="block text-sm text-gray-400 mb-2">Passcode</label>
          <div class="relative">
            <input 
              v-model="password" 
              :type="isPasswordVisible ? 'text' : 'password'" 
              required 
              maxlength="16" 
              class="w-full bg-black border border-[#333] p-3 text-white focus:outline-none focus:border-rose-600 pr-12"
              placeholder="MAX 16 CHARS"
            >
            <!-- Feature 6: Interactive Password Visibility Toggle -->
            <button type="button" @click="togglePassword" class="absolute right-3 top-3 text-[10px] text-gray-500 hover:text-rose-600 font-bold">
              {{ isPasswordVisible ? 'HIDE' : 'SHOW' }}
            </button>
          </div>
        </div>

        <button type="submit" :disabled="isLoading" class="w-full bg-rose-600 text-white font-bold tracking-widest py-4 mt-6 hover:bg-rose-700 transition-colors disabled:opacity-50">
          {{ isLoading ? 'AUTHORIZING...' : 'AUTHORIZE ACCESS' }}
        </button>
      </form>

      <!-- ========================================== -->
      <!-- 2. REGISTER FORM                           -->
      <!-- ========================================== -->
      <form v-else @submit.prevent="handleRegister" class="space-y-6">
        <div>
          <label class="block text-sm text-gray-400 mb-2">Citizen Name</label>
          <input v-model="name" type="text" required class="w-full bg-black border border-[#333] p-3 text-white focus:outline-none focus:border-rose-600">
        </div>

        <div>
          <label class="block text-sm text-gray-400 mb-2">Email Identification</label>
          <input v-model="email" type="email" required class="w-full bg-black border border-[#333] p-3 text-white focus:outline-none focus:border-rose-600">
        </div>

        <div>
          <label class="block text-sm text-gray-400 mb-2">Create Passcode</label>
          <div class="relative">
            <input 
              v-model="password" 
              :type="isPasswordVisible ? 'text' : 'password'" 
              required 
              maxlength="16"
              class="w-full bg-black border border-[#333] p-3 text-white focus:outline-none focus:border-rose-600 pr-12"
            >
            <button type="button" @click="togglePassword" class="absolute right-3 top-3 text-[10px] text-gray-500 hover:text-rose-600 font-bold">
              {{ isPasswordVisible ? 'HIDE' : 'SHOW' }}
            </button>
          </div>
        </div>

        <div>
          <label class="block text-sm text-gray-400 mb-2">Confirm Passcode</label>
          <input 
            v-model="confirmPassword" 
            :type="isPasswordVisible ? 'text' : 'password'" 
            required 
            maxlength="16"
            class="w-full bg-black border border-[#333] p-3 text-white focus:outline-none focus:border-rose-600"
          >
        </div>

        <button type="submit" :disabled="isLoading" class="w-full bg-rose-600 text-white font-bold tracking-widest py-4 mt-6 hover:bg-rose-700 transition-colors disabled:opacity-50">
          {{ isLoading ? 'INITIALIZING...' : 'CREATE IDENTITY' }}
        </button>
      </form>
      
      <!-- Feature 11: Guest Entry (Redirects to Dashboard) -->
      <div class="mt-8 text-center border-t border-[#222] pt-6">
        <p class="text-gray-600 mb-4 text-xs tracking-widest uppercase">Limited Clearance Access</p>
        <button @click="router.push('/dashboard')" class="border border-[#333] text-gray-400 px-6 py-2 hover:text-white hover:border-white transition-colors text-sm">
          ENTER AS GUEST
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

// UI and Mode State
const isLoginMode = ref(true);
const isPasswordVisible = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');

// Form Data
const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');

// Feature 6 Logic
const togglePassword = () => {
  isPasswordVisible.value = !isPasswordVisible.value;
};

// --- REGISTER LOGIC ---
const handleRegister = async () => {
  errorMessage.value = '';
  isLoading.value = true;
  try {
    await axios.post('http://127.0.0.1:8000/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value
    });
    
    alert("Identity Created! You may now access the system.");
    
    // Reset and switch to login
    password.value = '';
    confirmPassword.value = '';
    isLoginMode.value = true; 

  } catch (error) {
    errorMessage.value = error.response?.data?.message || "Registration failed. Verify your connection.";
  } finally {
    isLoading.value = false;
  }
};

// --- LOGIN LOGIC ---
const handleLogin = async () => {
  errorMessage.value = '';
  isLoading.value = true;
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/login', {
      email: email.value,
      password: password.value,
    });
    
    // Secure storage of session data
    localStorage.setItem('auth_token', response.data.token);
    localStorage.setItem('user_role', response.data.user.role);
    
    alert("Access Granted!");
    
    // Feature 1 & 2: Role-Based Routing
    if (response.data.user.role === 'admin') {
      router.push('/admin');
    } else {
      router.push('/dashboard');
    }

  } catch (error) {
    errorMessage.value = error.response?.data?.message || "Authorization Denied. Check credentials.";
  } finally {
    isLoading.value = false;
  }
};
</script>