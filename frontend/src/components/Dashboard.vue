<template>
    <div class="min-h-screen bg-[#111] text-white p-8 font-mono relative">
      
      <!-- ========================================== -->
      <!-- 1. SYSTEM HEADER                           -->
      <!-- ========================================== -->
      <nav class="border-b border-[#333] pb-4 mb-8 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-rose-600 tracking-tighter">EMERGENCY BD // TERMINAL</h1>
        <button @click="handleLogout" class="text-sm text-gray-400 hover:text-white border border-[#333] px-4 py-1 transition-colors">
          TERMINATE SESSION
        </button>
      </nav>
  
      <!-- ========================================== -->
      <!-- 2. STATUS GRID                             -->
      <!-- ========================================== -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="border border-[#333] p-6 bg-black">
          <h3 class="text-gray-500 text-xs mb-2">ACTIVE IDENTITY</h3>
          <p class="text-xl">Authenticated Citizen</p>
        </div>
        <div class="border border-[#333] p-6 bg-black">
          <h3 class="text-gray-500 text-xs mb-2">SYSTEM STATUS</h3>
          <p class="text-xl text-green-500">OPERATIONAL</p>
        </div>
        <div class="border border-[#333] p-6 bg-black">
          <h3 class="text-gray-500 text-xs mb-2">CONTRIBUTION POINTS</h3>
          <p class="text-xl text-rose-600">02</p>
        </div>
      </div>
  
      <!-- ========================================== -->
      <!-- 3. LIVE RADAR MAP                          -->
      <!-- ========================================== -->
      <div class="border border-[#333] bg-black p-4">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-gray-500 text-xs tracking-widest">LIVE RADAR // GEOLOCATION</h3>
          <span class="text-xs text-rose-600 animate-pulse">● TRACKING ACTIVE</span>
        </div>
        
        <!-- The Map Container -->
        <div id="map" class="h-[500px] w-full border border-[#222] z-0"></div>
      </div>
  
      <!-- ========================================== -->
      <!-- 4. FLOATING REPORT BUTTON                  -->
      <!-- ========================================== -->
      <button 
        @click="showReportModal = true"
        class="fixed bottom-8 right-8 bg-rose-600 text-white font-bold tracking-widest px-8 py-4 shadow-[0_0_20px_rgba(225,29,72,0.4)] hover:bg-rose-700 hover:scale-105 transition-all z-40"
      >
        [ ! ] REPORT EMERGENCY
      </button>
  
      <!-- ========================================== -->
      <!-- 5. REPORTING MODAL                         -->
      <!-- ========================================== -->
      <div v-if="showReportModal" class="fixed inset-0 bg-black/90 z-50 flex justify-center items-center p-4">
        <div class="border border-rose-600 bg-[#111] w-full max-w-2xl p-8 relative">
          
          <!-- Close Button -->
          <button @click="showReportModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-rose-600">
            [ CLOSE ]
          </button>
  
          <h2 class="text-2xl font-bold text-rose-600 mb-6 border-b border-[#333] pb-2">TRANSMIT INCIDENT REPORT</h2>
  
          <form @submit.prevent="submitReport" class="space-y-6">
            
            <!-- Feature 14: Crime Type -->
            <div>
              <label class="block text-sm text-gray-400 mb-2">Incident Classification</label>
              <select v-model="reportForm.type" required class="w-full bg-black border border-[#333] p-3 text-white focus:outline-none focus:border-rose-600">
                <option value="" disabled>Select Type...</option>
                <option value="theft">Robbery / Theft</option>
                <option value="assault">Physical Assault</option>
                <option value="fire">Fire Hazard</option>
                <option value="medical">Medical Emergency</option>
                <option value="traffic">Severe Traffic / Accident</option>
              </select>
            </div>
  
            <!-- Feature 14: Intensity Level -->
            <div>
              <label class="block text-sm text-gray-400 mb-2">Threat Intensity</label>
              <div class="flex gap-4">
                <label class="flex-1 text-center border border-[#333] p-3 cursor-pointer hover:bg-[#222]" :class="{'bg-yellow-900/30 border-yellow-500 text-yellow-500': reportForm.intensity === 'low'}">
                  <input type="radio" v-model="reportForm.intensity" value="low" class="hidden"> LOW
                </label>
                <label class="flex-1 text-center border border-[#333] p-3 cursor-pointer hover:bg-[#222]" :class="{'bg-orange-900/30 border-orange-500 text-orange-500': reportForm.intensity === 'medium'}">
                  <input type="radio" v-model="reportForm.intensity" value="medium" class="hidden"> MEDIUM
                </label>
                <label class="flex-1 text-center border border-[#333] p-3 cursor-pointer hover:bg-[#222]" :class="{'bg-rose-900/30 border-rose-600 text-rose-600': reportForm.intensity === 'high'}">
                  <input type="radio" v-model="reportForm.intensity" value="high" class="hidden"> CRITICAL
                </label>
              </div>
            </div>
  
            <!-- Feature 17: Evidence Upload -->
            <div>
              <label class="block text-sm text-gray-400 mb-2">Attach Evidence (Photo/Video)</label>
              <input type="file" @change="handleFileUpload" accept="image/*,video/*" class="w-full bg-black border border-[#333] p-2 text-white text-sm file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-[#222] file:text-white hover:file:bg-[#333]">
            </div>
  
            <!-- Feature 12: Anonymity Toggle -->
            <div class="flex items-center gap-3 border border-[#333] p-4 bg-black">
              <input type="checkbox" v-model="reportForm.isAnonymous" id="anon" class="w-5 h-5 accent-rose-600">
              <label for="anon" class="text-sm cursor-pointer select-none">
                <span class="text-white">Transmit Anonymously</span>
                <span class="block text-gray-500 text-xs">Your identity will be hidden from the public map.</span>
              </label>
            </div>
  
            <!-- Submit -->
            <button type="submit" class="w-full bg-rose-600 text-white font-bold tracking-widest py-4 mt-4 hover:bg-rose-700 transition-colors">
              TRANSMIT TO ADMIN
            </button>
          </form>
  
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, reactive } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from 'axios'; // WE NEED AXIOS TO SEND THE DATA!
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  
  const router = useRouter();
  const showReportModal = ref(false);
  
  // 1. Upgraded Form State (Now holds coordinates!)
  const reportForm = reactive({
    type: '',
    intensity: 'medium',
    isAnonymous: false,
    evidence: null,
    latitude: null, // Added
    longitude: null // Added
  });
  
  const handleLogout = () => {
    localStorage.removeItem('auth_token');
    router.push('/');
  };
  
  const handleFileUpload = (event) => {
    reportForm.evidence = event.target.files[0];
  };
  
  // 2. The Real Transmission Logic
  const submitReport = async () => {
    const isConfirmed = window.confirm("Are you sure you want to transmit this emergency report to the authorities?");
    if (!isConfirmed) return;
  
    // Make sure we have a GPS lock before sending
    if (!reportForm.latitude || !reportForm.longitude) {
      alert("SYSTEM ERROR: Cannot verify location coordinates. Please wait for GPS lock.");
      return;
    }
  
    // 3. Package the data using FormData (Required for Files)
    const formData = new FormData();
    formData.append('type', reportForm.type);
    formData.append('intensity', reportForm.intensity);
    formData.append('isAnonymous', reportForm.isAnonymous); // Convert boolean to text for sending
    formData.append('latitude', reportForm.latitude);
    formData.append('longitude', reportForm.longitude);
    
    if (reportForm.evidence) {
      formData.append('evidence', reportForm.evidence);
    }
  
    try {
      // Get your passport (token)
      const token = localStorage.getItem('auth_token');
      
      // Shoot it to the backend!
      const response = await axios.post('http://127.0.0.1:8000/api/reports', formData, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'multipart/form-data' // Tells Laravel a file is coming
        }
      });
      
      alert(response.data.message);
      
      // Reset form and close modal
      showReportModal.value = false;
      reportForm.type = '';
      reportForm.intensity = 'medium';
      reportForm.isAnonymous = false;
      reportForm.evidence = null;
      
    } catch (error) {
      console.error("Transmission Error:", error);
      alert("TRANSMISSION FAILED. Check network connection or server status.");
    }
  };
  
  // Map Initialization
  // Map Initialization
  onMounted(async () => {
    // 1. Initialize map (Centered on Dhaka initially)
    const map = L.map('map').setView([23.8103, 90.4125], 13);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; OpenStreetMap',
      subdomains: 'abcd',
      maxZoom: 20
    }).addTo(map);

    // 2. Request user geolocation (Where the Citizen is currently standing)
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((position) => {
          reportForm.latitude = position.coords.latitude;
          reportForm.longitude = position.coords.longitude;
          map.setView([reportForm.latitude, reportForm.longitude], 14);

          // Blue icon for the user's personal location
          const userIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color: #3b82f6; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 10px #3b82f6;"></div>`,
            iconSize: [16, 16],
            iconAnchor: [8, 8]
          });

          L.marker([reportForm.latitude, reportForm.longitude], { icon: userIcon }).addTo(map)
            .bindPopup('<b style="color: black;">Your Current Position</b>')
            .openPopup();
      });
  }

  // 3. NEW: Fetch and Plot Approved Emergencies
  try {
    const token = localStorage.getItem('auth_token');
    const response = await axios.get('http://127.0.0.1:8000/api/reports/map', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    
    const approvedReports = response.data;

    // Loop through the database records and drop a pin for each one
    approvedReports.forEach(report => {
      // Determine color based on intensity
      let pinColor = '#eab308'; // Default Yellow (low)
      if (report.intensity === 'medium') pinColor = '#f97316'; // Orange
      if (report.intensity === 'high') pinColor = '#e11d48'; // Red

      const incidentIcon = L.divIcon({
        className: 'custom-div-icon',
        html: `<div style="background-color: ${pinColor}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 15px ${pinColor};"></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
      });

      L.marker([report.latitude, report.longitude], { icon: incidentIcon }).addTo(map)
        .bindPopup(`
          <b style="color: ${pinColor}; text-transform: uppercase;">${report.type} HAZARD</b><br>
          <span style="color: black;">Intensity: ${report.intensity.toUpperCase()}</span><br>
          <span style="color: gray; font-size: 10px;">ID: #${report.id}</span>
        `);
    });

    } catch (error) {
    console.error("Failed to load radar data:", error);
    }
  });

  </script>