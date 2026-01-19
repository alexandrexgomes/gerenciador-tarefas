export type LoginResponse = {
  token: string
  tipo: 'bearer' | string
  expira_em_segundos: number
}

export type PerfilResponse = {
  id: number
  nome: string
  email: string
  permissoes: string[]
}