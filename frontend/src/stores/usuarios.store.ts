import { defineStore } from 'pinia'
import type { Usuario } from '../types/usuario'
import type { Papel } from '../types/papel'
import { listarPapeisApi } from '../api/papeis.api'
import {
  paginateUsuariosApi,
  cadastrarUsuarioApi,
  atualizarUsuarioApi,
  excluirUsuarioApi,
  carregarUsuarioApi,
} from '../api/usuarios.api'

export const useUsuariosStore = defineStore('usuarios', {
  state: () => ({
    items: [] as Usuario[],
    total: 0,
    page: 1,
    per_page: 10,
    last_page: 1,

    papeis: [] as Papel[],
    loadingPapeis: false,

    loading: false,
    error: '' as string,
    errors: {} as Record<string, string[]>,

    filtros: { status: '', busca: '' },
  }),

  actions: {
    async fetchPapeis(force = false) {
      if (this.loadingPapeis) return
      if (this.papeis.length && !force) return

      this.loadingPapeis = true
      try {
        this.papeis = await listarPapeisApi()
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao carregar papéis.'
        throw e
      } finally {
        this.loadingPapeis = false
      }
    },

    async fetch(force = false) {
      if (this.loading && !force) return
      this.loading = true
      this.error = ''
      this.errors = {}

      try {
        const r = await paginateUsuariosApi({
          page: this.page,
          per_page: this.per_page,
          status: this.filtros.status || undefined,
          busca: this.filtros.busca || undefined,
        })

        this.items = r.items
        this.total = r.total
        this.page = r.page
        this.per_page = r.per_page
        this.last_page = r.last_page ?? Math.max(1, Math.ceil(this.total / this.per_page))
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao carregar usuários.'
        this.errors = e?.response?.data?.errors ?? {}
        throw e
      } finally {
        this.loading = false
      }
    },

    async criar(payload: { nome: string; email: string; password: string; papeis_ids: number[] }) {
      this.loading = true
      this.error = ''
      this.errors = {}

      try {
        await cadastrarUsuarioApi(payload)
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao cadastrar usuário.'
        if (e?.response?.status === 422) this.errors = e?.response?.data?.errors ?? {}
        throw e
      } finally {
        this.loading = false
      }
    },

    async carregar(id: number): Promise<Usuario> {
      this.loading = true
      this.error = ''
      this.errors = {}
      try {
        return await carregarUsuarioApi(id)
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao carregar usuário.'
        throw e
      } finally {
        this.loading = false
      }
    },

    async atualizar(id: number, payload: { nome: string; email: string; status: number; password?: string; papeis_ids: number[] }) {
      this.loading = true
      this.error = ''
      this.errors = {}

      try {
        await atualizarUsuarioApi(id, payload)
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao atualizar usuário.'
        if (e?.response?.status === 422) this.errors = e?.response?.data?.errors ?? {}
        throw e
      } finally {
        this.loading = false
      }
    },

    async excluir(id: number) {
      this.loading = true
      this.error = ''
      this.errors = {}

      try {
        await excluirUsuarioApi(id)
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao excluir usuário.'
        throw e
      } finally {
        this.loading = false
      }
    },
  },
})
