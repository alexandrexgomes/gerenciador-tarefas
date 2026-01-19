import { defineStore } from 'pinia'
import { loginApi, perfilApi } from '../api/auth.api'
import type { PerfilResponse } from '../types/auth'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') as string | null,
    user: null as PerfilResponse | null,
    loading: false,
    error: '' as string,
  }),
  getters: {
    isAuthenticated: (s) => !!s.token,

    can: (s) => (perm: string) => {
      return !!s.user?.permissoes?.includes(perm)
    },

    canListarUsuarios: (s) => !!s.user?.permissoes?.includes('usuario.listar'),
    canCriarUsuario: (s) => !!s.user?.permissoes?.includes('usuario.criar'),
    canAtualizarUsuario: (s) => !!s.user?.permissoes?.includes('usuario.atualizar'),
    canExcluirUsuario: (s) => !!s.user?.permissoes?.includes('usuario.excluir'),

    canCriarTarefa: (s) => !!s.user?.permissoes?.includes('tarefa.criar'),
    canAtualizarTarefa: (s) => !!s.user?.permissoes?.includes('tarefa.atualizar'),
    canExcluirTarefa: (s) => !!s.user?.permissoes?.includes('tarefa.excluir'),
    canConcluirTarefa: (s) => !!s.user?.permissoes?.includes('tarefa.concluir'),
    canReabrirTarefa: (s) => !!s.user?.permissoes?.includes('tarefa.reabrir')
  },
  actions: {
    async login(email: string, password: string) {
      this.loading = true
      this.error = ''
      try {
        const r = await loginApi(email, password)
        this.token = r.token
        localStorage.setItem('token', r.token)
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao logar.'
        throw e
      } finally {
        this.loading = false
      }
    },

    async loadPerfil() {
      if (!this.token) return
      
      this.loading = true
      this.error = ''
      try {
        this.user = await perfilApi()
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao carregar perfil.'
        this.user = null
        throw e
      } finally {
        this.loading = false
      }
    },

    logout() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
    },
  },
})