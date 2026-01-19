<template>
  <div class="container py-4" style="max-width: 1100px">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h2 class="mb-0">Tarefas Internas</h2>
        <small class="text-muted">Gerencie e acompanhe suas tarefas</small>
      </div>

      <div class="d-flex align-items-center gap-3">
        <div class="text-end lh-sm">
          <div class="fw-semibold" v-if="auth.user">{{ auth.user.nome }}</div>
          <small class="text-muted" v-if="auth.user">{{ auth.user.email }}</small>
          <small class="text-muted" v-else>Carregando perfil...</small>
        </div>

        <button v-if="podeCriarTarefa" class="btn btn-primary btn-sm" @click="novaTarefa" :disabled="store.loading">
          <i class="bi bi-plus-lg me-1"></i>
          Nova tarefa
        </button>

        <button v-if="auth.can('usuario.listar')" class="btn btn-outline-secondary btn-sm"
          @click="router.push('/usuarios')">
          <i class="bi bi-people me-1"></i>
          Usuários
        </button>

        <button class="btn btn-outline-secondary btn-sm" @click="logout">
          <i class="bi bi-box-arrow-right me-1"></i>
          Sair
        </button>
      </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-body">
        <div class="row g-2 align-items-end">
          <div class="col-12 col-md-4">
            <label class="form-label mb-1">Busca</label>
            <input v-model.trim="store.filtros.busca" type="text" class="form-control"
              placeholder="Título ou descrição..." :disabled="store.loading" @keyup.enter="reload" />
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label mb-1">Concluída</label>
            <select
              v-model="store.filtros.completed"
              class="form-select"
              @change="reload"
              :disabled="store.loading"
            >
              <option value="">Todos</option>
              <option value="1">Sim</option>
              <option value="0">Não</option>
            </select>
          </div>

          <div class="col-12 col-md-2">
            <label class="form-label mb-1">De</label>
            <input v-model="store.filtros.created_from" type="date" class="form-control" @change="reload"
              :disabled="store.loading" />
          </div>

          <div class="col-12 col-md-2">
            <label class="form-label mb-1">Até</label>
            <input v-model="store.filtros.created_to" type="date" class="form-control" @change="reload"
              :disabled="store.loading" />
          </div>

          <div class="col-12 col-md-1 d-grid">
            <button class="btn btn-primary" @click="reload" :disabled="store.loading" title="Pesquisar"
              aria-label="Pesquisar">
              <i class="bi bi-search"></i>
            </button>
          </div>

          <div class="col-12 col-md-1 d-grid">
            <button class="btn btn-outline-secondary" @click="limparFiltros" :disabled="store.loading"
              title="Limpar filtros" aria-label="Limpar filtros">
              <i class="bi bi-arrow-clockwise"></i>
            </button>
          </div>

        </div>
      </div>
    </div>

    <!-- Mensagens -->
    <div v-if="Object.keys(store.errors || {}).length" class="alert alert-danger alert-dismissible fade show"
      role="alert">
      <div class="d-flex align-items-start gap-2">
        <i class="bi bi-exclamation-triangle mt-1"></i>

        <div class="flex-grow-1">
          <div class="fw-semibold">Detalhes do erro</div>

          <ul class="mb-0 mt-2">
            <li v-for="(msgs, field) in store.errors" :key="field">
              <strong>{{ field }}:</strong>
              <ul class="mb-0">
                <li v-for="(m, i) in msgs" :key="i">{{ m }}</li>
              </ul>
            </li>
          </ul>
        </div>
      </div>

      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
        @click="store.errors = {}; store.error = ''"></button>
    </div>

    <!-- Tabela -->
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th class="ps-3">ID</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Concluída</th>
                <th>Criada</th>
                <th>Atualizada</th>
                <th v-if="podeVerAcoes" class="text-end pe-3" style="width: 320px">Ações</th>
              </tr>
            </thead>

            <tbody>
              <tr v-if="store.loading">
                <td :colspan="podeVerAcoes ? 7 : 6" class="py-4 text-center">
                  <div class="d-inline-flex align-items-center gap-2">
                    <div class="spinner-border spinner-border-sm" aria-hidden="true"></div>
                    <span>Carregando...</span>
                  </div>
                </td>
              </tr>

              <tr v-else-if="store.items.length === 0">
                <td :colspan="podeVerAcoes ? 7 : 6" class="py-4 text-center text-muted">
                  Nenhuma tarefa encontrada com os filtros atuais.
                </td>
              </tr>

              <tr v-else v-for="t in store.items" :key="t.id">
                <td class="ps-3 fw-semibold">{{ t.id }}</td>
                <td>{{ t.title }}</td>
                <td>{{ t.description || '-' }}</td>
                <td>
                  <span :class="badgeCompletedClass(t.completed)">
                    {{ t.completed ? 'Sim' : 'Não' }}
                  </span>
                </td>
                <td>{{ t.created_at || '-' }}</td>
                <td>{{ t.updated_at || '-' }}</td>

                <td v-if="podeVerAcoes" class="text-end pe-3">
                  <div class="btn-group" role="group">
                    <!-- NOVO: Editar -->
                    <button
                      v-if="podeEditar"
                      class="btn btn-sm btn-outline-primary"
                      @click="abrirEditar(t.id)"
                      :disabled="store.loading || editLoading"
                    >
                      <i class="bi bi-pencil me-1"></i>
                      Editar
                    </button>

                    <button
                      v-if="podeAlternarConclusao(t.completed)"
                      class="btn btn-sm"
                      :class="t.completed ? 'btn-warning' : 'btn-success'"
                      @click="toggleConclusao(t.id, t.completed)"
                      :disabled="store.loading"
                    >
                      <i class="bi me-1" :class="t.completed ? 'bi-arrow-counterclockwise' : 'bi-check2'"></i>
                      {{ t.completed ? 'Reabrir' : 'Concluir' }}
                    </button>

                    <button
                      v-if="podeExcluir"
                      class="btn btn-sm btn-danger"
                      @click="excluir(t.id)"
                      :disabled="store.loading"
                    >
                      <i class="bi bi-trash me-1"></i>
                      Excluir
                    </button>
                  </div>

                  <div v-if="!podeEditar && !podeAlternarConclusao(t.completed) && !podeExcluir"
                    class="text-muted small d-inline-flex align-items-center gap-2">
                    <i class="bi bi-lock"></i>
                    <span>Sem ações disponíveis</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">
          Página {{ store.page }} • Exibindo {{ store.items.length }} itens
        </small>
        <div class="d-flex gap-2">
          <button
            class="btn btn-outline-secondary btn-sm"
            @click="prevPage"
            :disabled="store.loading || store.page <= 1"
          >
            Anterior
          </button>
          <button class="btn btn-outline-secondary btn-sm" @click="nextPage"
            :disabled="store.loading || store.page >= store.last_page">
            Próxima
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Nova Tarefa -->
  <div class="modal fade" id="modalNovaTarefa" tabindex="-1" aria-hidden="true" ref="modalEl">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form @submit.prevent="salvarNovaTarefa" novalidate>
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-plus-lg me-2"></i>
              Nova tarefa
            </h5>
            <button type="button" class="btn-close" :disabled="saving" @click="fecharModal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Título</label>
              <input
                v-model.trim="form.title"
                class="form-control"
                maxlength="255"
                placeholder="Ex: Estudar Laravel"
                :class="{ 'is-invalid': !!formErrors.title }"
                :disabled="saving"
              />
              <div class="invalid-feedback" v-if="formErrors.title">
                {{ formErrors.title }}
              </div>
            </div>

            <div class="mb-0">
              <label class="form-label">Descrição (opcional)</label>
              <textarea
                v-model.trim="form.description"
                class="form-control"
                rows="3"
                placeholder="Detalhes da tarefa..."
                :disabled="saving"
              ></textarea>
            </div>

            <div class="alert alert-danger mt-3 mb-0" v-if="formErrors.geral">
              <i class="bi bi-exclamation-triangle me-2"></i>
              {{ formErrors.geral }}
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" :disabled="saving" @click="fecharModal">
              Cancelar
            </button>

            <button type="submit" class="btn btn-primary" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
              Salvar tarefa
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- NOVO: Modal Editar Tarefa -->
  <div class="modal fade" id="modalEditarTarefa" tabindex="-1" aria-hidden="true" ref="modalEditEl">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form @submit.prevent="salvarEdicao" novalidate>
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-pencil me-2"></i>
              Editar tarefa <span v-if="editForm.id">#{{ editForm.id }}</span>
            </h5>
            <button
              type="button"
              class="btn-close"
              :disabled="editSaving || editLoading"
              @click="fecharEditar"
              aria-label="Close"
            ></button>
          </div>

          <div class="modal-body">
            <div v-if="editLoading" class="py-4 text-center text-muted">
              <div class="d-inline-flex align-items-center gap-2">
                <div class="spinner-border spinner-border-sm" aria-hidden="true"></div>
                <span>Carregando tarefa...</span>
              </div>
            </div>

            <div v-else>
              <div class="mb-3">
                <label class="form-label">Título</label>
                <input
                  v-model.trim="editForm.title"
                  class="form-control"
                  maxlength="255"
                  placeholder="Ex: Estudar Laravel"
                  :class="{ 'is-invalid': !!editErrors.title }"
                  :disabled="editSaving"
                />
                <div class="invalid-feedback" v-if="editErrors.title">
                  {{ editErrors.title }}
                </div>
              </div>

              <div class="mb-0">
                <label class="form-label">Descrição (opcional)</label>
                <textarea
                  v-model.trim="editForm.description"
                  class="form-control"
                  rows="3"
                  placeholder="Detalhes da tarefa..."
                  :disabled="editSaving"
                ></textarea>
              </div>

              <div class="alert alert-danger mt-3 mb-0" v-if="editErrors.geral">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ editErrors.geral }}
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" :disabled="editSaving" @click="fecharEditar">
              Cancelar
            </button>

            <button type="submit" class="btn btn-primary" :disabled="editSaving || editLoading">
              <span v-if="editSaving" class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
              Salvar alterações
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '../stores/auth.store'
import { useTarefasStore } from '../stores/tarefas.store'
import { useRoute, useRouter } from 'vue-router'
import { alertConfirm, alertError, alertSuccess } from '../utils/alert'
import { carregarTarefaApi } from '../api/tarefas.api'
import { Modal } from 'bootstrap'

const store = useTarefasStore()
const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const podeCriarTarefa = computed(() => auth.canCriarTarefa)
const podeEditar = computed(() => auth.canAtualizarTarefa)
const podeExcluir = computed(() => auth.canExcluirTarefa)
const podeVerAcoes = computed(() =>
  auth.canAtualizarTarefa || auth.canConcluirTarefa || auth.canReabrirTarefa || auth.canExcluirTarefa
)

const modalEl = ref<HTMLElement | null>(null)
let modalInstance: Modal | null = null

const modalEditEl = ref<HTMLElement | null>(null)
let modalEditInstance: Modal | null = null

const form = reactive({
  title: '',
  description: '',
})

const formErrors = reactive<Record<string, string>>({})
const saving = ref(false)
const tentouSalvar = ref(false)

/** NOVO: estado do modal de edição */
const editForm = reactive({
  id: null as number | null,
  title: '',
  description: '',
  completed: false,
})
const editErrors = reactive<Record<string, string>>({})
const editSaving = ref(false)
const editTentouSalvar = ref(false)
const editLoading = ref(false)

onMounted(async () => {
  if (auth.isAuthenticated && !auth.user) {
    await auth.loadPerfil()
  }

  if (route.query.welcome === '1') {
    await alertSuccess('Bem-vindo!', auth.user?.nome ? `Olá, ${auth.user.nome}.` : undefined)

    const q = { ...route.query }
    delete q.welcome
    router.replace({ query: q })
  }

  if (modalEl.value) {
    modalInstance = new Modal(modalEl.value, { backdrop: 'static' })
  }
  if (modalEditEl.value) {
    modalEditInstance = new Modal(modalEditEl.value, { backdrop: 'static' })
  }

  try {
    await store.fetch()
  } catch {
    await alertError('Erro', store.error || 'Falha ao carregar tarefas.')
  }
})

async function reload() {
  if (store.filtros.created_from && store.filtros.created_to) {
    if (store.filtros.created_from > store.filtros.created_to) {
      await alertError('Filtro inválido', 'A data "De" não pode ser maior que a data "Até".')
      return
    }
  }
  store.page = 1
  await store.fetch()
}

function logout() {
  auth.logout()
  router.push('/login')
}

function limparFiltros() {
  store.filtros.busca = ''
  store.filtros.completed = ''
  store.filtros.created_from = ''
  store.filtros.created_to = ''
  reload()
}

function limparForm() {
  form.title = ''
  form.description = ''
  Object.keys(formErrors).forEach((k) => delete formErrors[k])
}

function limparEditForm() {
  editForm.id = null
  editForm.title = ''
  editForm.description = ''
  editForm.completed = false
  Object.keys(editErrors).forEach((k) => delete editErrors[k])
}

function badgeCompletedClass(completed: boolean) {
  const base = 'badge text-uppercase'
  return completed ? `${base} bg-success` : `${base} bg-secondary`
}

async function prevPage() {
  if (store.page <= 1) return
  store.page--
  await store.fetch()
}

async function nextPage() {
  if (store.page >= store.last_page) return
  store.page++
  await store.fetch()
}

function novaTarefa() {
  limparForm()
  tentouSalvar.value = false
  modalInstance?.show()
}

function fecharModal() {
  modalInstance?.hide()
}

function validarForm(): boolean {
  Object.keys(formErrors).forEach((k) => delete formErrors[k])

  if (!form.title) formErrors.title = 'Informe o título.'
  if (form.title && form.title.length > 255) formErrors.title = 'O título não pode ter mais que 255 caracteres.'

  return Object.keys(formErrors).length === 0
}

async function salvarNovaTarefa() {
  tentouSalvar.value = true
  if (!validarForm()) return

  saving.value = true
  store.errors = {}
  store.error = ''

  try {
    await store.criar({ title: form.title, description: form.description })
    await alertSuccess('Criada!', 'Tarefa criada com sucesso.')
    fecharModal()
    limparForm()
    await reload()
  } catch {
    await alertError('Erro ao criar', store.error || 'Não foi possível criar a tarefa.')
  } finally {
    saving.value = false
  }
}

function podeAlternarConclusao(completed: boolean) {
  return completed ? auth.canReabrirTarefa : auth.canConcluirTarefa
}

/** NOVO: abrir modal editar e carregar do backend */
async function abrirEditar(id: number) {
  limparEditForm()
  editTentouSalvar.value = false
  editLoading.value = true
  modalEditInstance?.show()

  try {
    const t = await carregarTarefaApi(id)
    editForm.id = t.id
    editForm.title = t.title ?? ''
    editForm.description = t.description ?? ''
    editForm.completed = !!t.completed
  } catch {
    await alertError('Erro', store.error || 'Não foi possível carregar a tarefa.')
    fecharEditar()
  } finally {
    editLoading.value = false
  }
}

function fecharEditar() {
  modalEditInstance?.hide()
}

function validarEditForm(): boolean {
  Object.keys(editErrors).forEach((k) => delete editErrors[k])

  if (!editForm.title) editErrors.title = 'Informe o título.'
  if (editForm.title && editForm.title.length > 255) editErrors.title = 'O título não pode ter mais que 255 caracteres.'

  return Object.keys(editErrors).length === 0
}

async function salvarEdicao() {
  editTentouSalvar.value = true
  if (!validarEditForm()) return
  if (!editForm.id) return

  editSaving.value = true
  store.errors = {}
  store.error = ''

  try {
    // Importante: manda o completed atual junto,
    // porque hoje o backend defaulta completed=false quando não vem no request.
    await store.atualizar(editForm.id, {
      title: editForm.title,
      description: editForm.description,
      completed: editForm.completed,
    })

    await alertSuccess('Atualizada!', `Tarefa #${editForm.id} atualizada com sucesso.`)
    fecharEditar()
    await reload()
  } catch {
    await alertError('Erro ao atualizar', store.error || 'Não foi possível atualizar a tarefa.')
  } finally {
    editSaving.value = false
  }
}

async function toggleConclusao(id: number, completed: boolean) {
  const acao = completed ? 'Reabrir' : 'Concluir'
  const ok = await alertConfirm(`${acao} tarefa?`, `Você deseja ${acao.toLowerCase()} a tarefa #${id}?`, acao)
  if (!ok) return

  try {
    if (completed) await store.reabrir(id)
    else await store.concluir(id)
    await alertSuccess('Ok!', `Tarefa #${id} atualizada com sucesso.`)
  } catch {
    await alertError('Erro', store.error || 'Não foi possível atualizar a tarefa.')
  }
}

async function excluir(id: number) {
  const ok = await alertConfirm('Excluir tarefa?', `Você deseja excluir a tarefa #${id}?`, 'Excluir')
  if (!ok) return

  try {
    await store.excluir(id)
    await alertSuccess('Excluída!', `Tarefa #${id} excluída com sucesso.`)
  } catch {
    await alertError('Erro', store.error || 'Não foi possível excluir a tarefa.')
  }
}

watch([() => form.title, () => form.description], () => {
  if (!tentouSalvar.value) return
  validarForm()
})

watch([() => editForm.title, () => editForm.description], () => {
  if (!editTentouSalvar.value) return
  validarEditForm()
})
</script>
