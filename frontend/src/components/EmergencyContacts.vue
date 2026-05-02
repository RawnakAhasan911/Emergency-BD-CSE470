<template>
    <!-- Feature 13: Emergency contact numbers/sites -->
    <div class="fixed bottom-0 left-0 w-full bg-black border-t border-cyber-neonPink p-3 z-50 shadow-[0_-5px_15px_rgba(255,0,60,0.15)] font-mono">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between text-xs sm:text-sm text-gray-400">
        
        <!-- System Status / Alert Indicator -->
        <div class="flex items-center gap-2 mb-2 md:mb-0">
          <span class="text-cyber-neonPink font-bold tracking-widest uppercase animate-pulse">
            [ Emergency Protocol Active ]
          </span>
        </div>
  
        <!-- Emergency Quick Links -->
        <div class="flex flex-wrap justify-center gap-4 md:gap-8">
          
          <!-- National Emergency -->
          <a href="tel:999" class="hover:text-white transition flex items-center gap-1 group cursor-pointer">
            <span class="text-cyber-neonCyan group-hover:text-cyber-neonPink transition">National Emergency:</span>
            <span class="font-bold text-white tracking-wider text-base">999</span>
          </a>
  
          <!-- DMP Headquarters -->
          <a href="tel:+8802223381967" class="hover:text-white transition flex items-center gap-1 group cursor-pointer">
            <span class="text-cyber-neonCyan group-hover:text-cyber-neonPink transition">DMP Police HQ:</span>
            <span class="font-bold text-white tracking-wider">+880 223381967</span>
          </a>
  
          <!-- External Website Link -->
          <a 
            href="https://www.police.gov.bd/" 
            target="_blank" 
            rel="noopener noreferrer" 
            class="hover:text-white transition flex items-center gap-1 group border-l border-gray-700 pl-4 md:pl-8"
          >
            <span class="text-cyber-neonCyan group-hover:text-white transition underline underline-offset-4">
              BD Police Web Portal ↗
            </span>
          </a>
  
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  // This is a purely static UI component to fulfill Feature 13.
  // No complex reactive logic is needed here!
  </script>