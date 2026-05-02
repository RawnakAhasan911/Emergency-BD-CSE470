<template>
    <div class="p-8 bg-cyber-bg min-h-screen text-white font-mono">
      <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl text-cyber-neonCyan mb-2 uppercase tracking-widest font-bold">Admin Override Terminal</h1>
        <p class="text-gray-400 mb-10 border-b border-gray-700 pb-4">Emergency BD System Management Dashboard</p>
  
        <!-- Feature 3: Registered User Management View -->
        <section class="mb-16">
          <h2 class="text-2xl text-cyber-neonPink border-l-4 border-cyber-neonPink pl-3 mb-6 uppercase">User Matrix</h2>
          
          <div class="overflow-x-auto border border-cyber-neonCyan bg-cyber-panel">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-black text-cyber-neonCyan border-b border-cyber-neonCyan">
                  <th class="p-4">ID</th>
                  <th class="p-4">Citizen Name</th>
                  <th class="p-4">Status</th>
                  <th class="p-4">Points</th>
                  <th class="p-4 text-right">Administrative Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id" class="border-b border-gray-800 hover:bg-gray-900 transition">
                  <td class="p-4 text-gray-500">#{{ user.id }}</td>
                  <td class="p-4">
                    <div class="font-bold">{{ user.name }}</div>
                    <div class="text-xs text-gray-400">{{ user.email }}</div>
                  </td>
                  <td class="p-4">
                    <span v-if="user.is_suspended" class="text-red-500 bg-red-900/30 px-2 py-1 text-xs uppercase">Suspended</span>
                    <span v-else class="text-green-500 bg-green-900/30 px-2 py-1 text-xs uppercase">Active</span>
                  </td>
                  <td class="p-4 text-cyber-neonPink font-bold">{{ user.points }}</td>
                  <td class="p-4 flex justify-end gap-2">
                    
                    <!-- Feature 9: Admin can give points -->
                    <button @click="givePoints(user.id)" class="bg-gray-800 text-green-400 border border-green-500 px-3 py-1 hover:bg-green-500 hover:text-black text-xs transition">
                      +1 Pt
                    </button>
                    
                    <!-- Feature 10: Toggle Account Status -->
                    <button @click="toggleStatus(user)" class="bg-gray-800 text-yellow-400 border border-yellow-500 px-3 py-1 hover:bg-yellow-500 hover:text-black text-xs transition">
                      {{ user.is_suspended ? 'Reactivate' : 'Suspend' }}
                    </button>
  
                    <!-- Feature 20: Danger Zone (Hard Delete User) & Feature 19: Confirmation pop up -->
                    <button @click="deleteUser(user.id)" class="bg-gray-800 text-cyber-neonPink border border-cyber-neonPink px-3 py-1 hover:bg-cyber-neonPink hover:text-white text-xs transition">
                      ERASE
                    </button>
  
                  </td>
                </tr>
                <tr v-if="users.length === 0">
                  <td colspan="5" class="p-6 text-center text-gray-500">No citizens found in the database.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
  
        <!-- Feature 4: Crime Report Monitoring View -->
        <section>
          <h2 class="text-2xl text-cyber-neonCyan border-l-4 border-cyber-neonCyan pl-3 mb-6 uppercase">Incoming Reports (Pending Moderation)</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="report in pendingCrimes" :key="report.id" class="bg-cyber-panel p-5 border border-gray-700 hover:border-cyber-neonCyan transition relative group">
              
              <div class="absolute top-0 right-0 bg-black text-gray-500 px-2 py-1 text-xs border-b border-l border-gray-700">
                ID: {{ report.id }}
              </div>
  
              <p class="mb-2 mt-2"><span class="text-gray-400 text-sm">Type:</span> <br><span class="text-xl text-white uppercase font-bold">{{ report.crime_type }}</span></p>
              <p class="mb-4"><span class="text-gray-400 text-sm">Intensity Level:</span> <span class="text-cyber-neonPink font-bold">{{ report.intensity }} / 5</span></p>
              
              <div class="bg-black p-3 text-sm text-gray-300 mb-4 h-24 overflow-y-auto border border-gray-800">
                {{ report.description || 'No description provided by the reporter.' }}
              </div>
  
              <div class="text-xs text-gray-500 mb-6">
                📍 Coordinates: {{ report.latitude }}, {{ report.longitude }}
              </div>
  
              <div class="flex flex-col gap-2">
                <div class="flex gap-2">
                  <!-- Feature 8: Only Admin can accept an entry -->
                  <button @click="moderateCrime(report.id, 'approved')" class="flex-1 bg-cyber-neonCyan text-black py-2 text-sm font-bold uppercase hover:bg-white transition">
                    Approve
                  </button>
                  <button @click="moderateCrime(report.id, 'rejected')" class="flex-1 bg-transparent border border-cyber-neonPink text-cyber-neonPink py-2 text-sm font-bold uppercase hover:bg-cyber-neonPink hover:text-white transition">
                    Reject
                  </button>
                </div>
                
                <!-- Feature 20: Danger Zone (Hard Delete Crime) -->
                <button @click="deleteCrime(report.id)" class="w-full bg-red-900 text-white py-1 text-xs uppercase hover:bg-red-700 transition">
                  Permanently Delete Record
                </button>
              </div>
  
            </div>
            
            <div v-if="pendingCrimes.length === 0" class="col-span-full bg-cyber-panel p-8 text-center border border-gray-800 text-gray-500">
              No pending crime reports require moderation at this time.
            </div>
          </div>
        </section>
  
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  
  // To use this file, ensure Axios sends the Bearer token with requests!
  // Example: axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
  
  const users = ref([]);
  const pendingCrimes = ref([]);
  
  // --- Data Fetching ---
  
  const fetchDashboardData = async () => {
      try {
          // NOTE: You will need to add two simple GET routes in your api.php 
          // and AdminController to fetch these arrays. 
          const userRes = await axios.get('/api/admin/users');
          users.value = userRes.data.data;
  
          const crimeRes = await axios.get('/api/admin/crimes/pending');
          pendingCrimes.value = crimeRes.data.data;
      } catch (error) {
          console.error("Failed to load dashboard data", error);
      }
  };
  
  onMounted(() => {
      fetchDashboardData();
  });
  
  // --- Feature Actions ---
  
  // Feature 9
  const givePoints = async (userId) => {
      if (!confirm('Add 1 token to this user?')) return;
      try {
          await axios.post(`/api/admin/users/${userId}/assign-points`, { points: 1 });
          fetchDashboardData(); // Refresh list
      } catch (error) {
          alert('Failed to assign points.');
      }
  };
  
  // Feature 10
  const toggleStatus = async (user) => {
      const action = user.is_suspended ? 'reactivate' : 'suspend';
      if (!confirm(`Are you sure you want to ${action} this account?`)) return; // Feature 19
      try {
          await axios.post(`/api/admin/users/${user.id}/toggle-status`);
          fetchDashboardData(); 
      } catch (error) {
          alert(error.response?.data?.error || 'Failed to toggle status.');
      }
  };
  
  // Feature 8
  const moderateCrime = async (crimeId, status) => {
      if (!confirm(`Mark this report as ${status}?`)) return;
      try {
          await axios.post(`/api/admin/crimes/${crimeId}/moderate`, { status });
          // Remove it from the UI immediately without reloading the whole page
          pendingCrimes.value = pendingCrimes.value.filter(c => c.id !== crimeId);
      } catch (error) {
          alert('Moderation action failed.');
      }
  };
  
  // Feature 20 (Danger Zone: Users)
  const deleteUser = async (userId) => {
      if (!confirm('DANGER: Are you sure you want to permanently erase this user? This cannot be undone.')) return;
      try {
          await axios.delete(`/api/admin/users/${userId}`);
          users.value = users.value.filter(u => u.id !== userId);
      } catch (error) {
          alert(error.response?.data?.error || 'Failed to delete user.');
      }
  };
  
  // Feature 20 (Danger Zone: Crimes)
  const deleteCrime = async (crimeId) => {
      if (!confirm('DANGER: Permanently delete this crime report and its media evidence?')) return;
      try {
          await axios.delete(`/api/admin/crimes/${crimeId}`);
          pendingCrimes.value = pendingCrimes.value.filter(c => c.id !== crimeId);
      } catch (error) {
          alert('Failed to delete report.');
      }
  };
  </script>
  
  <style scoped>
  /* Custom scrollbar for descriptions to keep the cards uniform */
  ::-webkit-scrollbar {
    width: 6px;
  }
  ::-webkit-scrollbar-track {
    background: #000;
  }
  ::-webkit-scrollbar-thumb {
    background: #333;
  }
  ::-webkit-scrollbar-thumb:hover {
    background: #00f3ff;
  }
  </style>