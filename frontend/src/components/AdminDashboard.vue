<template>
  <div class="min-h-screen bg-[#0a0a0a] text-white p-8 font-mono">
    
    <nav class="border-b border-rose-900 pb-4 mb-8 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-rose-600 tracking-tighter">EMERGENCY BD // HIGH COMMAND</h1>
      <button @click="handleLogout" class="text-sm text-gray-400 hover:text-white border border-[#333] px-4 py-1">
        TERMINATE SESSION
      </button>
    </nav>

    <div class="border border-[#333] bg-black p-6">
      <h2 class="text-xl text-rose-600 mb-4 border-b border-[#333] pb-2">PENDING INCIDENT REPORTS</h2>
      
      <div v-if="isLoading" class="text-gray-500 animate-pulse">FETCHING TRANSMISSIONS...</div>

      <table v-else class="w-full text-left text-sm text-gray-400">
        <thead class="text-xs text-gray-500 uppercase bg-[#111] border-b border-[#333]">
          <tr>
            <th class="px-6 py-3">Report ID</th>
            <th class="px-6 py-3">Reported By</th>
            <th class="px-6 py-3">Classification</th>
            <th class="px-6 py-3">Intensity</th>
            <th class="px-6 py-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="report in pendingReports" :key="report.id" class="border-b border-[#222] hover:bg-[#111]">
            <td class="px-6 py-4 text-white">#{{ report.id }}</td>
            
            <td class="px-6 py-4 text-gray-300">
              {{ report.is_anonymous ? 'ANONYMOUS CITIZEN' : report.user?.name }}
            </td>
            
            <td class="px-6 py-4 text-rose-500 uppercase">{{ report.type }}</td>
            <td class="px-6 py-4 uppercase" :class="getIntensityColor(report.intensity)">
              {{ report.intensity }}
            </td>
            <td class="px-6 py-4 flex gap-2">
              <button @click="handleStatusUpdate(report.id, 'approved')" class="bg-green-900/50 text-green-500 border border-green-700 px-3 py-1 hover:bg-green-900 transition-colors">VERIFY</button>
              <button @click="handleStatusUpdate(report.id, 'rejected')" class="bg-red-900/50 text-red-500 border border-red-700 px-3 py-1 hover:bg-red-900 transition-colors">REJECT</button>
            </td>
          </tr>
          
          <tr v-if="pendingReports.length === 0">
            <td colspan="5" class="px-6 py-8 text-center text-gray-600">NO PENDING REPORTS IN QUEUE.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const pendingReports = ref([]);
const isLoading = ref(true);

const handleLogout = () => {
  localStorage.removeItem('auth_token');
  localStorage.removeItem('user_role');
  router.push('/');
};

// Color coding based on intensity
const getIntensityColor = (intensity) => {
  if (intensity === 'high') return 'text-rose-600 font-bold';
  if (intensity === 'medium') return 'text-orange-500';
  return 'text-yellow-500';
};

// Fetch reports when admin loads the page
const fetchReports = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    const response = await axios.get('http://127.0.0.1:8000/api/reports/pending', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    pendingReports.value = response.data;
  } catch (error) {
    console.error("Failed to fetch reports:", error);
    alert("SYSTEM ERROR: Cannot connect to database.");
  } finally {
    isLoading.value = false;
  }
};

// Feature 8: Accept/Reject Entry
const handleStatusUpdate = async (id, newStatus) => {
  if (!window.confirm(`Are you sure you want to mark this report as ${newStatus.toUpperCase()}?`)) return;

  try {
    const token = localStorage.getItem('auth_token');
    const response = await axios.patch(`http://127.0.0.1:8000/api/reports/${id}/status`, 
      { status: newStatus },
      { headers: { 'Authorization': `Bearer ${token}` } }
    );
    
    alert(response.data.message);
    
    // Remove the handled report from the UI list so it disappears from the pending table
    pendingReports.value = pendingReports.value.filter(report => report.id !== id);
    
  } catch (error) {
    console.error("Failed to update status:", error);
    alert("UPDATE FAILED.");
  }
};

// Run the fetch function as soon as the page loads
onMounted(() => {
  fetchReports();
});
</script>