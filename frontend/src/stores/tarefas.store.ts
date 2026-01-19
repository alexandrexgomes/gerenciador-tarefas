import { defineStore } from 'pinia'
import type { Tarefa } from '../types/tarefa'
import {
  paginateTarefasApi,
  criarTarefaApi,
  atualizarTarefaApi,
  concluirTarefaApi,
  reabrirTarefaApi,
  excluirTarefaApi,
} from '../api/tarefas.api'

export const useTarefasStore = defineStore('tarefas', {
  state: () => ({
    items: [] as Tarefa[],
    total: 0,
    page: 1,
    per_page: 8,
    last_page: 1,
    loading: false,
    error: '' as string,
    errors: {} as Record<string, string[]>,
    filtros: {
      busca: '',
      completed: '',
      created_from: '',
      created_to: '',
    },
  }),

  actions: {
    async fetch(force = false) {
      if (this.loading && !force) return

      this.loading = true
      this.error = ''
      this.errors = {}

      try {
        const r = await paginateTarefasApi({
          page: this.page,
          per_page: this.per_page,
          busca: this.filtros.busca || undefined,
          completed: this.filtros.completed || undefined,
          created_from: this.filtros.created_from || undefined,
          created_to: this.filtros.created_to || undefined,
        })

        this.items = r.items
        this.total = r.total
        this.page = r.page
        this.per_page = r.per_page
        this.last_page = r.last_page
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao carregar tarefas.'
        this.errors = e?.response?.data?.errors ?? {}
      } finally {
        this.loading = false
      }
    },

    async criar(payload: { title: string; description?: string }) {
      this.loading = true
      this.error = ''
      this.errors = {}

      try {
        await criarTarefaApi(payload)
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao criar tarefa.'
        if (e?.response?.status === 422) this.errors = e?.response?.data?.errors ?? {}
        throw e
      } finally {
        this.loading = false
      }
    },

    async atualizar(id: number, payload: { title: string; description?: string | null; completed?: boolean }) {
      this.loading = true
      this.error = ''
      this.errors = {}

      try {
        await atualizarTarefaApi(id, payload)
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao atualizar tarefa.'
        if (e?.response?.status === 422) this.errors = e?.response?.data?.errors ?? {}
        throw e
      } finally {
        this.loading = false
      }
    },

    async concluir(id: number) {
      this.loading = true
      this.error = ''
      this.errors = {}

      try {
        await concluirTarefaApi(id)
        await this.fetch(true)
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao concluir tarefa.'
        throw e
      } finally {
        this.loading = false
      }
    },

    async reabrir(id: number) {
      this.loading = true
      this.error = ''
      this.errors = {}

      try {
        await reabrirTarefaApi(id)
        await this.fetch(true)
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao reabrir tarefa.'
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
        await excluirTarefaApi(id)
        await this.fetch(true)
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Falha ao excluir tarefa.'
        throw e
      } finally {
        this.loading = false
      }
    },
  },
})