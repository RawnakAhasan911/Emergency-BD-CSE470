import { createApp } from 'vue';
import './style.css'; 
import App from './App.vue';
import router from './router'; // 1. Import the router configuration

const app = createApp(App);

// 2. Tell Vue to use the router before mounting
app.use(router); 

// 3. Mount the application
app.mount('#app');