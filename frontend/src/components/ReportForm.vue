<template>
    <div class="bg-cyber-panel p-6 sm:p-8 border-2 border-cyber-neonCyan relative shadow-[0_0_15px_#00f3ff20] text-white font-mono max-w-2xl mx-auto">
      
      <div class="flex justify-between items-start mb-6 border-b border-gray-700 pb-4">
        <h2 class="text-2xl text-cyber-neonCyan uppercase tracking-widest font-bold">File Incident Report</h2>
        
        <!-- Feature 18: User can request points from Admin -->
        <button type="button" @click="requestPoints" class="text-xs bg-gray-800 text-green-400 border border-green-500 px-3 py-1 hover:bg-green-500 hover:text-black transition uppercase tracking-wider">
          Request Tokens
        </button>
      </div>
  
      <!-- Feature 5: On-Screen Error and Feedback Messages -->
      <div v-if="feedbackMsg" :class="feedbackType === 'error' ? 'text-cyber-neonPink border-cyber-neonPink bg-red-900/20' : 'text-cyber-neonCyan border-cyber-neonCyan bg-cyan-900/20'" class="mb-6 p-3 border text-sm uppercase">
        {{ feedbackMsg }}
      </div>
  
      <form @submit.prevent="submitReport" class="space-y-6">
        
        <!-- Feature 9: Crime Reporting Form Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-gray-400 text-xs uppercase">Victim Name (Optional)</label>
            <input type="text" v-model="form.victim_name" class="w-full bg-black text-white p-2 mt-1 outline-none border border-gray-700 focus:border-cyber-neonCyan">
          </div>
          <div>
            <label class="text-gray-400 text-xs uppercase">Incident Type *</label>
            <select v-model="form.crime_type" required class="w-full bg-black text-white p-2 mt-1 outline-none border border-gray-700 focus:border-cyber-neonCyan">
              <option value="robbery">Robbery / Theft</option>
              <option value="murder">Murder / Homicide</option>
              <option value="assault">Assault / Violence</option>
              <option value="accident">Severe Accident</option>
              <option value="harassment">Harassment</option>
            </select>
          </div>
        </div>
  
        <!-- Feature 14: Keep track of crime type and intensity -->
        <div>
          <label class="flex justify-between text-gray-400 text-xs uppercase mb-2">
            <span>Threat Intensity (1-5) *</span>
            <span class="text-cyber-neonPink font-bold text-sm">Level: {{ form.intensity }}</span>
          </label>
          <input type="range" min="1" max="5" v-model="form.intensity" class="w-full accent-cyber-neonPink cursor-pointer">
        </div>
  
        <div>
          <label class="text-gray-400 text-xs uppercase">Detailed Description *</label>
          <textarea v-model="form.description" required rows="3" maxlength="1000" placeholder="Provide precise details of the incident..." class="w-full bg-black text-white p-2 mt-1 outline-none border border-gray-700 focus:border-cyber-neonCyan"></textarea>
        </div>
  
        <!-- Feature 7 & 12: Location Access & Reverse Geocoding -->
        <div class="p-4 border border-gray-800 bg-black relative">
          <div class="absolute -top-3 left-3 bg-black px-2 text-cyber-neonCyan text-xs uppercase">Spatial Coordinates</div>
          
          <div class="flex flex-col sm:flex-row gap-2 mb-3 mt-2">
            <button type="button" @click="autoDetectLocation" class="bg-gray-800 text-cyber-neonCyan border border-cyber-neonCyan px-4 py-2 hover:bg-cyber-neonCyan hover:text-black transition uppercase text-xs">
              Auto GPS Link
            </button>
            <div class="flex-1 relative">
              <input type="text" v-model="addressSearch" @keyup.enter="searchManualAddress" placeholder="Or search manual address and press Enter..." class="w-full bg-gray-900 text-white p-2 border border-gray-700 focus:border-cyber-neonCyan outline-none text-xs">
            </div>
          </div>
  
          <div v-if="form.latitude" class="text-xs text-gray-500 grid grid-cols-2 gap-2 mt-2">
            <p>LAT: <span class="text-white">{{ form.latitude.toFixed(6) }}</span></p>
            <p>LNG: <span class="text-white">{{ form.longitude.toFixed(6) }}</span></p>
            <p>AREA: <span class="text-white">{{ form.area || 'N/A' }}</span></p>
            <p>CITY: <span class="text-white">{{ form.city || 'N/A' }}</span></p>
          </div>
        </div>
  
        <!-- Feature 17: Photo/video/news article as evidence -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-gray-800 bg-black relative">
          <div class="absolute -top-3 left-3 bg-black px-2 text-cyber-neonPink text-xs uppercase">Evidence Attachment</div>
          
          <div class="mt-2">
            <label class="text-gray-400 text-xs uppercase block mb-1">Upload Media (Img/Video)</label>
            <input type="file" @change="handleFileUpload" accept="image/*,video/mp4" class="w-full text-xs text-gray-400 file:mr-4 file:py-1 file:px-3 file:border-0 file:text-xs file:bg-gray-800 file:text-cyber-neonPink hover:file:bg-gray-700 cursor-pointer">
          </div>
          
          <div class="mt-2">
            <label class="text-gray-400 text-xs uppercase block mb-1">External News Link</label>
            <input type="url" v-model="form.media_link" placeholder="https://..." class="w-full bg-gray-900 text-white p-2 outline-none border border-gray-700 focus:border-cyber-neonPink text-xs">
          </div>
        </div>
  
        <!-- Feature 12: Anonymous Reporting -->
        <div class="flex items-center gap-3 pt-2">
          <input type="checkbox" id="anon" v-model="form.is_anonymous" class="w-4 h-4 accent-cyber-neonCyan bg-gray-700 border-gray-600">
          <label for="anon" class="text-sm text-gray-300 uppercase cursor-pointer hover:text-white transition">Protect Identity (Submit Anonymously)</label>
        </div>
  
        <!-- Submit Button -->
        <!-- Feature 19: Confirmation pop up triggered in JS -->
        <button type="submit" :disabled="isSubmitting" class="w-full bg-cyber-neonPink text-white py-3 mt-4 font-bold tracking-widest uppercase hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
          {{ isSubmitting ? 'Transmitting...' : 'Execute Submission (-1 Token)' }}
        </button>
  
      </form>
    </div>
  </template>
  
  <script setup>
  import { reactive, ref } from 'vue';
  import axios from 'axios';
  
  // State
  const isSubmitting = ref(false);
  const feedbackMsg = ref('');
  const feedbackType = ref('');
  const addressSearch = ref('');
  
  const form = reactive({
    victim_name: '',
    crime_type: 'robbery',
    intensity: 3,
    description: '',
    latitude: null,
    longitude: null,
    area: '',
    city: '',
    is_anonymous: false,
    media: null,
    media_link: ''
  });
  
  // Feature 18: Request Points
  const requestPoints = async () => {
      try {
          await axios.post('/api/points/request');
          showFeedback("Token request dispatched to Admin.", 'success');
      } catch (error) {
          showFeedback("Failed to request tokens.", 'error');
      }
  };
  
  // Feature 17: Handle File Input
  const handleFileUpload = (event) => {
      form.media = event.target.files[0];
  };
  
  // Feature 7 & 12: Auto GPS & Reverse Geocoding
  const autoDetectLocation = () => {
    if (navigator.geolocation) {
      showFeedback("Establishing satellite uplink...", 'success');
      navigator.geolocation.getCurrentPosition(
        async (position) => {
          form.latitude = position.coords.latitude;
          form.longitude = position.coords.longitude;
          await reverseGeocode(form.latitude, form.longitude);
          showFeedback("Location acquired.", 'success');
        },
        (error) => showFeedback('GPS Signal Lost. Please enter address manually.', 'error')
      );
    } else {
      showFeedback("Geolocation is not supported by your browser.", 'error');
    }
  };
  
  const reverseGeocode = async (lat, lng) => {
      try {
          const res = await axios.get(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
          if(res.data && res.data.address) {
              form.area = res.data.address.suburb || res.data.address.neighbourhood || res.data.address.road || '';
              form.city = res.data.address.city || res.data.address.state || '';
          }
      } catch(err) {
          console.error("Geocoding failed", err);
      }
  };
  
  // Feature 7 Supplement: Manual Address Search
  const searchManualAddress = async () => {
      if (!addressSearch.value) return;
      try {
          showFeedback("Scanning database for coordinates...", 'success');
          const res = await axios.get(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(addressSearch.value + ', Bangladesh')}`);
          if (res.data && res.data.length > 0) {
              form.latitude = parseFloat(res.data[0].lat);
              form.longitude = parseFloat(res.data[0].lon);
              await reverseGeocode(form.latitude, form.longitude);
              showFeedback("Coordinates locked.", 'success');
          } else {
              showFeedback("Location not found.", 'error');
          }
      } catch (err) {
          showFeedback("Address search failed.", 'error');
      }
  };
  
  // Form Submission
  const submitReport = async () => {
      if (!form.latitude || !form.longitude) {
          showFeedback("Spatial coordinates are required before submission.", 'error');
          return;
      }
  
      // Feature 19: Confirmation pop up
      if (!confirm('WARNING: Submitting this report will permanently deduct 1 Token from your account. Proceed?')) return;
      
      isSubmitting.value = true;
      
      // Feature 17: We MUST use FormData because we are uploading a physical file (image/video)
      const formData = new FormData();
      formData.append('crime_type', form.crime_type);
      formData.append('intensity', form.intensity);
      formData.append('description', form.description);
      formData.append('latitude', form.latitude);
      formData.append('longitude', form.longitude);
      formData.append('is_anonymous', form.is_anonymous ? 1 : 0);
      
      if (form.victim_name) formData.append('victim_name', form.victim_name);
      if (form.area) formData.append('area', form.area);
      if (form.city) formData.append('city', form.city);
      if (form.media) formData.append('media', form.media);
      if (form.media_link) formData.append('media_link', form.media_link);
  
      try {
          await axios.post('/api/crimes/report', formData, {
              headers: { 'Content-Type': 'multipart/form-data' }
          });
          showFeedback("Report transmitted successfully. Awaiting Admin verification.", 'success');
          
          // Reset essential form fields
          form.description = '';
          form.victim_name = '';
          form.media = null;
          form.media_link = '';
          // Emit event so parent can refresh points/data if needed
          window.dispatchEvent(new CustomEvent('report-submitted'));
  
      } catch (error) {
          showFeedback(error.response?.data?.error || "Transmission failed.", 'error');
      } finally {
          isSubmitting.value = false;
      }
  };
  
  const showFeedback = (msg, type) => {
      feedbackMsg.value = msg;
      feedbackType.value = type;
      
      // Auto-hide success messages after 5 seconds
      if(type === 'success') {
          setTimeout(() => { if(feedbackMsg.value === msg) feedbackMsg.value = ''; }, 5000);
      }
  };
  </script>