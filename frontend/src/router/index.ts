import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import { useAuthStore } from '../stores/auth.store'
import { pinia } from '../plugins/pinia'
import UsuariosView from '../views/UsuariosView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/dashboard' },
    { path: '/login', name: 'login', component: LoginView },
    { path: '/dashboard', name: 'dashboard', component: DashboardView, meta: { requiresAuth: true } },
    { path: '/usuarios', name: 'usuarios', component: UsuariosView, meta: { requiresAuth: true, perm: 'usuario.listar' } },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore(pinia)
  if (to.meta.requiresAuth && !auth.isAuthenticated) return '/login'
  if (auth.isAuthenticated && !auth.user) { try { await auth.loadPerfil() } catch {} }
  if (to.path === '/login' && auth.isAuthenticated) return '/dashboard'
  if (to.meta.perm) {
    const perm = String(to.meta.perm)
    if (!auth.can(perm)) return '/dashboard'
  }
})

export default router