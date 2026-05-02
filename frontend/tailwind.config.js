/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        cyber: {
          bg: '#0a0a0a',
          panel: '#111111',
          neonCyan: '#00f3ff',
          neonPink: '#ff003c',
        }
      },
    },
  },
  plugins: [],
}