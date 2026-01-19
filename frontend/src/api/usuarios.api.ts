import http from './http'
import type { Usuario } from '../types/usuario'
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

export async function paginateUsuariosApi(params: {
  page?: number
  per_page?: number
  status?: string
  busca?: string
}): Promise<PaginateResponse<Usuario> & { last_page: number }> {
  const { data } = await http.get<ApiPaginateResponse<Usuario>>('/v1/usuarios/paginate', { params })

  return {
    items: data.data ?? [],
    total: data.links?.total ?? 0,
    page: data.links?.current_page ?? (params.page ?? 1),
    per_page: data.links?.per_page ?? (params.per_page ?? 10),
    last_page: data.links?.last_page ?? 1,
  }
}

export async function cadastrarUsuarioApi(payload: {
  nome: string
  email: string
  password: string
  papeis_ids: number[]
}): Promise<Usuario> {
  const fd = new FormData()
  fd.append('nome', payload.nome)
  fd.append('email', payload.email)
  fd.append('password', payload.password)

  payload.papeis_ids.forEach((id) => fd.append('papeis_ids[]', String(id)))

  const { data } = await http.post<Usuario>('/v1/usuarios/cadastrar', fd)
  return data
}

export async function carregarUsuarioApi(id: number): Promise<Usuario> {
  const { data } = await http.get<Usuario>(`/v1/usuarios/carregar/${id}`)
  return data
}

export async function atualizarUsuarioApi(
  id: number,
  payload: { nome: string; email: string; status: number; password?: string; papeis_ids: number[] }
): Promise<void> {
  const body: Record<string, any> = {
    nome: payload.nome,
    email: payload.email,
    status: payload.status,
    papeis_ids: payload.papeis_ids,
  }

  if (payload.password && payload.password.trim()) {
    body.password = payload.password
    body.password_confirmation = payload.password
  }

  await http.put(`/v1/usuarios/atualizar/${id}`, body)
}

export async function excluirUsuarioApi(id: number): Promise<void> {
  await http.delete(`/v1/usuarios/excluir/${id}`)
}