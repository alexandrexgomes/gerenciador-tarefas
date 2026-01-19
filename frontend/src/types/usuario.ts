import type { Papel } from './papel'

export type Usuario = {
  id: number
  nome: string
  email: string
  status: number
  status_desc: string
  papeis?: Papel[]
}