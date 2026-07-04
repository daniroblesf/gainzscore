import { createRouter, createWebHistory } from 'vue-router'

import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RankingView from '../views/RankingView.vue'
import ResultsView from '../views/ResultsView.vue'
import TrackerView from '../views/TrackerView.vue'

const routes = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
  },
  {
    path: '/home',
    name: 'home',
    component: HomeView,
  },
  {
    path: '/tracker',
    name: 'tracker',
    component: TrackerView,
  },
  {
    path: '/ranking',
    name: 'ranking',
    component: RankingView,
  },
  {
    path: '/results',
    name: 'results',
    component: ResultsView,
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/login',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
