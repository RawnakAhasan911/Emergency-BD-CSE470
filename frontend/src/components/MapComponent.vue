<template>
    <div class="relative w-full h-[600px] border-2 border-cyber-neonCyan rounded-sm overflow-hidden font-mono shadow-[0_0_20px_rgba(0,243,255,0.2)]">
      
      <!-- Loading State Overlay -->
      <div v-if="isLoading" class="absolute inset-0 z-[1000] bg-black/80 flex items-center justify-center">
        <div class="text-cyber-neonCyan animate-pulse uppercase tracking-widest font-bold">
          Initializing Spatial Uplink...
        </div>
      </div>
  
      <!-- The Map Container -->
      <div id="crime-map" class="w-full h-full z-0"></div>
  
      <!-- Map Legend -->
      <div class="absolute bottom-4 left-4 z-[500] bg-black/90 border border-gray-700 p-3 text-xs shadow-lg backdrop-blur-sm">
        <h3 class="text-gray-400 uppercase tracking-widest border-b border-gray-700 pb-1 mb-2">Threat Legend</h3>
        <div class="flex flex-col gap-2">
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-[#ff003c] shadow-[0_0_8px_#ff003c]"></span>
            <span class="text-white">Critical (Murder/Assault)</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-[#ffaa00] shadow-[0_0_8px_#ffaa00]"></span>
            <span class="text-white">High (Robbery/Theft)</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-[#00f3ff] shadow-[0_0_8px_#00f3ff]"></span>
            <span class="text-white">Alert (Accident/Other)</span>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, onBeforeUnmount } from 'vue';
  import 'leaflet/dist/leaflet.css';
  import L from 'leaflet';
  import axios from 'axios';
  
  const map = ref(null);
  const isLoading = ref(true);
  let markerLayer = null;
  
  // Ensure we fix Leaflet's default icon paths issue if needed later, 
  // but since we are using custom divIcons, we don't strictly need the default images.
  
  const initMap = async () => {
    // Center map on Bangladesh
    map.value = L.map('crime-map', {
      center: [23.6850, 90.3563],
      zoom: 7,
      zoomControl: false // Move zoom control to bottom right
    });
  
    L.control.zoom({ position: 'bottomright' }).addTo(map.value);
  
    // Feature 14: Dark Cyber Map Tiles (CartoDB Dark Matter)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
      subdomains: 'abcd',
      maxZoom: 20
    }).addTo(map.value);
  
    // Create a layer group to easily clear and re-render markers
    markerLayer = L.layerGroup().addTo(map.value);
  
    await fetchAndRenderMarkers();
    
    isLoading.value = false;
  };
  
  // Feature 15: Fetch Map Data
  const fetchAndRenderMarkers = async () => {
    try {
      const response = await axios.get('/api/crimes/map');
      const crimes = response.data.data;
      
      // Clear existing markers before drawing new ones
      markerLayer.clearLayers();
  
      crimes.forEach(crime => {
        // Feature 16: Color Coded Markers
        const markerColor = getMarkerColor(crime.crime_type);
        
        // Custom pulsing HTML marker
        const customIcon = L.divIcon({
          className: 'custom-crime-marker',
          html: `
            <div class="relative flex h-4 w-4">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: ${markerColor};"></span>
              <span class="relative inline-flex rounded-full h-4 w-4" style="background-color: ${markerColor}; box-shadow: 0 0 10px ${markerColor};"></span>
            </div>
          `,
          iconSize: [16, 16],
          iconAnchor: [8, 8] // Center the anchor
        });
  
        // Bind a cyberpunk-styled popup
        const popupContent = `
          <div class="bg-black text-white p-2 border border-gray-700 min-w-[150px] font-mono text-sm">
            <div class="uppercase font-bold border-b border-gray-700 pb-1 mb-1" style="color: ${markerColor}">
              ${crime.crime_type}
            </div>
            <div class="text-gray-400 text-xs mb-1">Intensity: ${crime.intensity}/5</div>
            <div>${crime.area || 'Unknown Area'}</div>
            <div class="text-gray-500 text-[10px] mt-1">${crime.city || ''}</div>
          </div>
        `;
  
        L.marker([crime.latitude, crime.longitude], { icon: customIcon })
          .bindPopup(popupContent, {
            className: 'cyber-popup', // Custom CSS class for popup styling
            closeButton: false
          })
          .addTo(markerLayer);
      });
  
    } catch (error) {
      console.error('Failed to load map data:', error);
    }
  };
  
  // Feature 16: Logic for colors
  const getMarkerColor = (type) => {
    const t = type.toLowerCase();
    if (t.includes('murder') || t.includes('assault')) return '#ff003c'; // Cyber Pink/Red
    if (t.includes('robbery') || t.includes('theft')) return '#ffaa00'; // Warning Orange
    return '#00f3ff'; // Default Cyan
  };
  
  onMounted(() => {
    initMap();
    
    // Feature 15 Supplement: Polling for live updates (Temporary solution)
    // In a production app, use Laravel Echo/Pusher here instead of setInterval
    const pollInterval = setInterval(() => {
        if(!isLoading.value) {
          fetchAndRenderMarkers();
        }
    }, 30000); // Check for new markers every 30 seconds
  
    // Cleanup
    onBeforeUnmount(() => {
      clearInterval(pollInterval);
      if(map.value) map.value.remove();
    });
  });
  </script>
  
  <style>
  /* Leaflet Popup Customization to match dark theme */
  .cyber-popup .leaflet-popup-content-wrapper,
  .cyber-popup .leaflet-popup-tip {
    background: #000;
    border-radius: 0;
    border: 1px solid #333;
  }
  .cyber-popup .leaflet-popup-content {
    margin: 0;
  }
  </style>