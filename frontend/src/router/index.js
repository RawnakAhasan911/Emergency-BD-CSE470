import { createRouter, createWebHistory } from 'vue-router';
import AuthComponent from '../components/AuthComponent.vue';

// Define the "Map" for your website
const routes = [
  { 
    path: '/', 
    name: 'Login',
    component: AuthComponent 
  },
  { 
    path: '/dashboard', 
    name: 'Dashboard',
    component: () => import('../components/Dashboard.vue') 
  },
  { 
    // NEW: The High Command route for Admins
    path: '/admin', 
    name: 'AdminDashboard',
    component: () => import('../components/AdminDashboard.vue') 
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;