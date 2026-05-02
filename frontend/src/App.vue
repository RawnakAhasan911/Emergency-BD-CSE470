<template>
    <div class="min-h-screen bg-cyber-bg font-mono flex flex-col relative pb-16">
      
      <!-- Top Navigation Bar (Only visible if not on the Auth screen) -->
      <header v-if="currentView !== 'auth'" class="bg-black border-b-2 border-cyber-neonCyan p-4 flex justify-between items-center z-50 shadow-[0_5px_15px_rgba(0,243,255,0.1)]">
        <div class="flex items-center gap-3">
          <div class="w-3 h-3 rounded-full bg-cyber-neonPink animate-pulse"></div>
          <h1 class="text-cyber-neonCyan text-xl sm:text-2xl font-bold tracking-widest uppercase">Emergency BD</h1>
        </div>
        
        <div class="flex items-center gap-4">
          <!-- Display User Info or Guest Status -->
          <span v-if="user" class="text-cyber-neonPink text-sm hidden sm:inline-block">
            Welcome, {{ user.name }} | Tokens: <span class="font-bold text-white">{{ user.points }}</span>
          </span>
          <span v-else class="text-gray-500 text-sm hidden sm:inline-block">
            Guest Access Active
          </span>
  
          <!-- Admin Toggle Button (Only visible if the logged-in user is an admin) -->
          <button 
            v-if="user && user.role === 'admin'" 
            @click="toggleAdminView" 
            class="bg-gray-800 text-cyber-neonCyan border border-cyber-neonCyan px-3 py-1 text-xs hover:bg-cyber-neonCyan hover:text-black transition uppercase"
          >
            {{ currentView === 'admin' ? 'Exit Terminal' : 'Admin Override' }}
          </button>
  
          <!-- Logout / Exit Button -->
          <button @click="logout" class="bg-transparent border border-cyber-neonPink text-cyber-neonPink px-4 py-1 text-xs hover:bg-cyber-neonPink hover:text-white transition uppercase">
            {{ user ? 'Disconnect' : 'Exit Guest Mode' }}
          </button>
        </div>
      </header>
  
      <!-- Main Content Area -->
      <main class="flex-grow">
        
        <!-- View 1: Authentication / Login -->
        <AuthComponent v-if="currentView === 'auth'" />
  
        <!-- View 2: Admin Dashboard -->
        <AdminDashboard v-else-if="currentView === 'admin'" />
  
        <!-- View 3: Main Citizen Dashboard (Map + Form) -->
        <div v-else-if="currentView === 'dashboard'" class="max-w-7xl mx-auto p-4 sm:p-8 space-y-8">
          <!-- Feature 14, 15, 16: Interactive Map -->
          <MapComponent />
          
          <!-- Feature 7, 9, 12, 14, 17, 18: Reporting Form -->
          <ReportForm />
        </div>
  
      </main>
  
      <!-- Feature 13: Global Emergency Contacts Footer -->
      <EmergencyContacts />
  
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, onBeforeUnmount } from 'vue';
  import axios from 'axios';
  
  // Import all child components
  import AuthComponent from './components/AuthComponent.vue';
  import AdminDashboard from './components/AdminDashboard.vue';
  import MapComponent from './components/MapComponent.vue';
  import ReportForm from './components/ReportForm.vue';
  import EmergencyContacts from './components/EmergencyContacts.vue';
  
  // State Management ('auth', 'dashboard', or 'admin')
  const currentView = ref('auth'); 
  const user = ref(null);
  
  // --- Initialization & Token Verification ---
  
  const checkSession = async () => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      try {
        // Fetch the logged-in user's details to check roles/points
        const response = await axios.get('/api/user');
        user.value = response.data;
        
        // Route based on role
        if (user.value.role === 'admin') {
          currentView.value = 'admin';
        } else {
          currentView.value = 'dashboard';
        }
      } catch (error) {
        // Token is invalid or expired
        logout();
      }
    } else {
      currentView.value = 'auth';
    }
  };
  
  onMounted(() => {
    checkSession();
  
    // Listen for the custom events emitted by AuthComponent.vue
    window.addEventListener('auth-success', checkSession);
    
    window.addEventListener('guest-access', () => {
      user.value = null;
      currentView.value = 'dashboard';
    });
  
    // Listen for report submissions to refresh user points
    window.addEventListener('report-submitted', checkSession);
  });
  
  onBeforeUnmount(() => {
    window.removeEventListener('auth-success', checkSession);
    window.removeEventListener('guest-access', null);
    window.removeEventListener('report-submitted', checkSession);
  });
  
  // --- Actions ---
  
  const toggleAdminView = () => {
    currentView.value = currentView.value === 'admin' ? 'dashboard' : 'admin';
  };
  
  const logout = async () => {
    if (user.value) {
      try {
        await axios.post('/api/logout');
      } catch (e) {
        console.error('Logout failed on server-side');
      }
    }
    
    // Clear local session data
    localStorage.removeItem('auth_token');
    delete axios.defaults.headers.common['Authorization'];
    user.value = null;
    currentView.value = 'auth';
  };
  </script>
  
  <style>
  /* Global Cyberpunk Base Styles */
  body {
    background-color: #0a0a0a;
    color: #fff;
    margin: 0;
    padding: 0;
  }
  </style>