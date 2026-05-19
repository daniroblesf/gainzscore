import { createRouter, createWebHashHistory } from 'vue-router'
import TrackerView from '../views/TrackerView.vue'

const routes = [
  {
    path: '/',
    redirect: '/tracker',
  },
  {
    path: '/tracker',
    name: 'tracker',
    component: TrackerView,
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue'),
  },
  {
    path: '/ranking',
    name: 'ranking',
    component: () => import('../views/RankingView.vue'),
  },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes,
})

export default router
