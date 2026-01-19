import http from './http'
import type { LoginResponse, PerfilResponse } from '../types/auth'

export async function loginApi(email: string, password: string): Promise<LoginResponse> {
  const fd = new FormData()
  fd.append('email', email)
  fd.append('password', password)

  const { data } = await http.post<LoginResponse>('/login', fd)
  return data
}

export async function perfilApi(): Promise<PerfilResponse> {
  const { data } = await http.get<PerfilResponse>('/v1/auth/perfil')
  return data
}