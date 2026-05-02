import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    // Proxy API requests to the Laravel backend
    proxy: {
      '/api': {
        target: 'http://localhost:8000', // Your Laravel server port
        changeOrigin: true,
        headers: {
          Accept: 'application/json',
        }
      }
    }
  }
})