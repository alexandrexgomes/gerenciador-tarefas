export type Tarefa = {
  id: number
  title: string
  description: string | null
  completed: boolean
  completed_desc?: string
  created_at?: string
  updated_at?: string
}