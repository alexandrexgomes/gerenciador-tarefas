import http from './http'
import type { Papel } from '../types/papel'

export async function listarPapeisApi(): Promise<Papel[]> {
  const { data } = await http.get<Papel[]>('/v1/papeis')
  return data
}