import http from './http'
import type { Tarefa } from '../types/tarefa'
import type { PaginateResponse } from '../types/paginacao'

type ApiPaginateResponse<T> = {
  data: T[]
  links: {
    total: number
    current_page: number
    per_page: number
    last_page: number
  }
}

export async function paginateTarefasApi(params: {
  page?: number
  per_page?: number
  busca?: string
  completed?: string
  created_from?: string
  created_to?: string
}): Promise<PaginateResponse<Tarefa> & { last_page: number }> {
  const { data } = await http.get<ApiPaginateResponse<Tarefa>>('/v1/tarefas/paginate', { params })

  return {
    items: data.data ?? [],
    total: data.links?.total ?? 0,
    page: data.links?.current_page ?? (params.page ?? 1),
    per_page: data.links?.per_page ?? (params.per_page ?? 15),
    last_page: data.links?.last_page ?? 1,
  }
}

export async function criarTarefaApi(payload: {
  title: string
  description?: string
}): Promise<Tarefa> {
  const fd = new FormData()
  fd.append('title', payload.title)
  if (payload.description?.trim()) fd.append('description', payload.description)

  const { data } = await http.post<Tarefa>('/v1/tarefas/criar', fd)
  return data
}

/*export async function atualizarTarefaApi(
  id: number,
  payload: { title: string; description?: string | null; completed?: boolean }
): Promise<Tarefa> {
  const fd = new FormData()
  fd.append('title', payload.title)
  fd.append('_method', 'PUT')
  if (payload.description != null) fd.append('description', payload.description ?? '')
  if (payload.completed != null) fd.append('completed', payload.completed ? '1' : '0')

  const { data } = await http.put<Tarefa>(`/v1/tarefas/atualizar/${id}`, fd)
  return data
}*/

export async function atualizarTarefaApi(
  id: number,
  payload: { title: string; description?: string | null; completed?: boolean }
): Promise<Tarefa> {
  const body: Record<string, any> = {
    title: payload.title,
    description: payload.description ?? null,
  }

  if (payload.completed !== undefined) {
    body.completed = payload.completed
  }

  const { data } = await http.put<Tarefa>(`/v1/tarefas/atualizar/${id}`, body)
  return data
}

export async function concluirTarefaApi(id: number): Promise<Tarefa> {
  const { data } = await http.patch<Tarefa>(`/v1/tarefas/concluir/${id}`)
  return data
}

export async function reabrirTarefaApi(id: number): Promise<Tarefa> {
  const { data } = await http.patch<Tarefa>(`/v1/tarefas/reabrir/${id}`)
  return data
}

export async function excluirTarefaApi(id: number): Promise<void> {
  await http.delete(`/v1/tarefas/excluir/${id}`)
}

export async function carregarTarefaApi(id: number): Promise<Tarefa> {
  const { data } = await http.get<Tarefa>(`/v1/tarefas/carregar/${id}`)
  return data
}