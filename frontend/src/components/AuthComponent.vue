<template>
  <div class="min-h-screen bg-[#111] text-white font-mono flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-md p-8">
      
      <!-- Header Tabs: Click these to swap modes! -->
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

      <!-- Error Message Box -->
      <div v-if="errorMessage" class="border border-rose-600 text-rose-600 p-4 mb-6">
        {{ errorMessage }}
      </div>

      <!-- ========================================== -->
      <!-- 1. LOGIN FORM (Shows when ACCESS is active)-->
      <!-- ========================================== -->
      <form v-if="isLoginMode" @submit.prevent="handleLogin" class="space-y-6">
        
        <div>
          <label class="block text-sm text-gray-400 mb-2">Email Identification</label>
          <input v-model="email" type="email" required class="w-full bg-black border border-[#333] p-3 text-white focus:outline-none focus:border-rose-600">
        </div>

        <div>
          <label class="block text-sm text-gray-400 mb-2">Passcode</label>
          <input v-model="password" type="password" required class="w-full bg-black border border-[#333] p-3 text-white focus:outline-none focus:border-rose-600">
        </div>

        <button type="submit" :disabled="isLoading" class="w-full bg-rose-600 text-white font-bold tracking-widest py-4 mt-6 hover:bg-rose-700 transition-colors disabled:opacity-50">
          {{ isLoading ? 'AUTHORIZING...' : 'AUTHORIZE ACCESS' }}
        </button>
      </form>

      <!-- ========================================== -->
      <!-- 2. REGISTER FORM (Shows when REGISTER is active) -->
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
          <input v-model="password" type="password" required class="w-full bg-black border border-[#333] p-3 text-white focus:outline-none focus:border-rose-600">
        </div>

        <div>
          <label class="block text-sm text-gray-400 mb-2">Confirm Passcode</label>
          <input v-model="confirmPassword" type="password" required class="w-full bg-black border border-[#333] p-3 text-white focus:outline-none focus:border-rose-600">
        </div>

        <button type="submit" :disabled="isLoading" class="w-full bg-rose-600 text-white font-bold tracking-widest py-4 mt-6 hover:bg-rose-700 transition-colors disabled:opacity-50">
          {{ isLoading ? 'INITIALIZING...' : 'CREATE IDENTITY' }}
        </button>
      </form>
      
      <!-- Guest Link -->
      <div class="mt-8 text-center">
        <p class="text-gray-500 mb-4">Or continue with limited clearance</p>
        <button class="border border-[#333] text-gray-400 px-6 py-2 hover:text-white transition-colors">
          ENTER AS GUEST
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

// TRUE = Show Login (Access) | FALSE = Show Register
const isLoginMode = ref(true); 

// Form Data Variables
const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');

// UI Variables
const errorMessage = ref('');
const isLoading = ref(false);

// --- REGISTRATION LOGIC ---
const handleRegister = async () => {
  errorMessage.value = '';
  isLoading.value = true;
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value
    });
    
    alert("Identity Created! You may now access the system.");
    
    // Clear the form and switch to Login tab automatically!
    password.value = '';
    confirmPassword.value = '';
    isLoginMode.value = true; 

  } catch (error) {
    if (error.response && error.response.data.message) {
       errorMessage.value = error.response.data.message;
    } else {
       errorMessage.value = "Registration failed. Check network.";
    }
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
    
    // 1. Save the token to local storage so App.vue can find it
    localStorage.setItem('auth_token', response.data.token);
    
    // 2. Alert the user
    alert("Access Granted!");
    
    // 3. Tell App.vue to re-check the session and route the user!
    window.dispatchEvent(new Event('auth-success'));

  } catch (error) {
    if (error.response && error.response.data.message) {
       errorMessage.value = error.response.data.message;
    } else {
       errorMessage.value = "Access Denied. Check credentials.";
    }
  } finally {
    isLoading.value = false;
  }
};
</script>