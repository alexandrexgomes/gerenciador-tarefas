<template>
    <div class="container py-4" style="max-width: 1100px">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-0">Usuários</h2>
                <small class="text-muted">Gerencie usuários do sistema</small>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary btn-sm" @click="voltarDashboard">
                    <i class="bi bi-arrow-left me-1"></i>
                    Tarefas
                </button>

                <button v-if="auth.canCriarUsuario" class="btn btn-primary btn-sm" @click="abrirNovoUsuario"
                    :disabled="store.loading">
                    <i class="bi bi-person-plus me-1"></i>
                    Novo usuário
                </button>

                <button class="btn btn-outline-secondary btn-sm" @click="logout">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    Sair
                </button>
            </div>
        </div>

        <!-- Filtros-->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-3">
                        <label class="form-label mb-1">Status</label>
                        <select v-model="store.filtros.status" class="form-select" @change="reload"
                            :disabled="store.loading">
                            <option value="">Todos</option>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-7">
                        <label class="form-label mb-1">Busca (nome ou e-mail)</label>
                        <input v-model.trim="store.filtros.busca" class="form-control"
                            placeholder="Ex: admin@tarefas.local" @keyup.enter="reload" :disabled="store.loading" />
                    </div>

                    <div class="col-6 col-md-1 d-grid">
                        <button class="btn btn-primary" @click="reload" :disabled="store.loading" title="Pesquisar"
                            aria-label="Pesquisar">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <div class="col-6 col-md-1 d-grid">
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
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Status</th>
                                <th v-if="podeVerAcoes" class="text-end pe-3" style="width: 260px">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-if="store.loading">
                                <td :colspan="podeVerAcoes ? 5 : 4" class="py-4 text-center">
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <div class="spinner-border spinner-border-sm" aria-hidden="true"></div>
                                        <span>Carregando...</span>
                                    </div>
                                </td>
                            </tr>

                            <tr v-else-if="store.items.length === 0">
                                <td :colspan="podeVerAcoes ? 5 : 4" class="py-4 text-center text-muted">
                                    Nenhum usuário encontrado com os filtros atuais.
                                </td>
                            </tr>

                            <tr v-else v-for="u in store.items" :key="u.id">
                                <td class="ps-3 fw-semibold">{{ u.id }}</td>
                                <td>{{ u.nome }}</td>
                                <td>{{ u.email }}</td>
                                <td>
                                    <span :class="badgeStatusClass(u.status)">{{ u.status_desc }}</span>
                                </td>

                                <td v-if="podeVerAcoes" class="text-end pe-3">
                                    <div class="btn-group" role="group">
                                        <button v-if="auth.canAtualizarUsuario" class="btn btn-sm btn-outline-primary"
                                            @click="abrirEdicao(u.id)" :disabled="store.loading">
                                            <i class="bi bi-pencil-square me-1"></i>
                                            Editar
                                        </button>

                                        <button v-if="auth.canExcluirUsuario && !isInativo(u)" class="btn btn-sm btn-outline-danger"
                                            @click="excluir(u.id)" :disabled="store.loading">
                                            <i class="bi bi-trash me-1"></i>
                                            Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- paginação -->
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Página {{ store.page }} • Exibindo {{ store.items.length }} itens
                </small>

                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" @click="prevPage"
                        :disabled="store.loading || store.page <= 1">
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

    <!-- Modal: Usuário (create/edit) -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true" ref="modalEl">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="salvar" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi" :class="modo === 'create' ? 'bi-person-plus' : 'bi-pencil-square'"></i>
                            <span class="ms-2">{{ modo === 'create' ? 'Novo usuário' : 'Editar usuário' }}</span>
                        </h5>
                        <button type="button" class="btn-close" :disabled="saving" @click="fecharModal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input v-model.trim="form.nome" class="form-control" maxlength="120"
                                :class="{ 'is-invalid': !!formErrors.nome }" :disabled="saving" />
                            <div class="invalid-feedback" v-if="formErrors.nome">{{ formErrors.nome }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input v-model.trim="form.email" type="email" class="form-control" maxlength="150"
                                :class="{ 'is-invalid': !!formErrors.email }" :disabled="saving" />
                            <div class="invalid-feedback" v-if="formErrors.email">{{ formErrors.email }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select v-model.number="form.status" class="form-select" :disabled="saving">
                                <option :value="1">Ativo</option>
                                <option :value="0">Inativo</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Papéis</label>

                            <div class="border rounded p-2" style="max-height: 160px; overflow: auto">
                                <div v-if="store.loadingPapeis"
                                    class="text-muted small d-flex align-items-center gap-2">
                                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                    Carregando papéis...
                                </div>

                                <div v-else-if="store.papeis.length === 0" class="text-muted small">
                                    Nenhum papel cadastrado.
                                </div>

                                <div v-else class="form-check" v-for="p in store.papeis" :key="p.id">
                                    <input class="form-check-input" type="checkbox" :id="'papel-' + p.id" :value="p.id"
                                        v-model="form.papeis_ids" :disabled="saving" />
                                    <label class="form-check-label" :for="'papel-' + p.id">
                                        {{ p.nome }}
                                    </label>
                                </div>
                            </div>

                            <div class="invalid-feedback d-block" v-if="formErrors.papeis_ids">
                                {{ formErrors.papeis_ids }}
                            </div>

                            <small class="text-muted">Selecione um ou mais papéis para o usuário.</small>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Senha <span v-if="modo === 'create'">(obrigatória)</span><span v-else>(opcional)</span></label>
                            <input v-model="form.password" type="password" class="form-control"
                                :class="{ 'is-invalid': !!formErrors.password }" :disabled="saving" />
                            <div class="invalid-feedback" v-if="formErrors.password">
                                {{ formErrors.password }}
                            </div>
                            <small class="text-muted">Se deixar em branco, não altera.</small>
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
                            {{ modo === 'create' ? 'Cadastrar' : 'Editar usuário' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Modal } from 'bootstrap'

import { useAuthStore } from '../stores/auth.store'
import { useUsuariosStore } from '../stores/usuarios.store'
import { alertConfirm, alertError, alertSuccess } from '../utils/alert'

const router = useRouter()
const auth = useAuthStore()
const store = useUsuariosStore()

const podeVerAcoes = computed(() => auth.canAtualizarUsuario || auth.canExcluirUsuario)

const modalEl = ref<HTMLElement | null>(null)
let modalInstance: Modal | null = null

const modo = ref<'create' | 'edit'>('create')
const editingId = ref<number | null>(null)

const form = reactive({
  nome: '',
  email: '',
  status: 1 as number,
  password: '',
  papeis_ids: [] as number[],
})

const formErrors = reactive<Record<string, string>>({})
const saving = ref(false)

onMounted(async () => {
    if (auth.isAuthenticated && !auth.user) {
        try { await auth.loadPerfil() } catch { }
    }

    if (modalEl.value) {
        modalInstance = new Modal(modalEl.value, { backdrop: 'static' })
    }

    try {
        await store.fetchPapeis()
        await store.fetch()
    } catch {
        await alertError('Erro', store.error || 'Falha ao carregar usuários.')
    }
})

function isInativo(u: any) {
  return u.status === 'Inativo'
}

function badgeStatusClass(status: number) {
    const base = 'badge text-uppercase'
    if (status === 1) return `${base} bg-success`
    return `${base} bg-secondary`
}

async function reload() {
    store.page = 1
    await store.fetch()
}

function limparFiltros() {
    store.filtros.status = ''
    store.filtros.busca = ''
    reload()
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

async function abrirNovoUsuario() {
  await store.fetchPapeis()
  modo.value = 'create'
  editingId.value = null
  limparForm()
  modalInstance?.show()
}

function statusTextToNumber(s: any): number {
  if (typeof s === 'number') return s
  return (String(s).toLowerCase() === 'ativo') ? 1 : 0
}

async function abrirEdicao(id: number) {
  await store.fetchPapeis()

  modo.value = 'edit'
  editingId.value = id
  limparForm()
  modalInstance?.show()

  try {
    const u = await store.carregar(id)
    form.nome = u.nome
    form.email = u.email
    form.status = statusTextToNumber(u.status)
    form.password = ''
    form.papeis_ids = (u.papeis ?? []).map(p => p.id)
  } catch {
    await alertError('Erro', store.error || 'Falha ao carregar usuário.')
    fecharModal()
  }
}

function limparForm() {
  form.nome = ''
  form.email = ''
  form.status = 1
  form.password = ''
  form.papeis_ids = []
  Object.keys(formErrors).forEach(k => delete formErrors[k])
  store.errors = {}
  store.error = ''
}

function fecharModal() {
    modalInstance?.hide()
}

function validar(): boolean {
  Object.keys(formErrors).forEach(k => delete formErrors[k])

  if (!form.nome?.trim()) formErrors.nome = 'Informe o nome.'
  if (!form.email?.trim()) formErrors.email = 'Informe o e-mail.'
  if (!form.papeis_ids.length) formErrors.papeis_ids = 'Selecione pelo menos um papel.'

  if (modo.value === 'create' && !form.password?.trim()) {
    formErrors.password = 'Informe a senha.'
  }

  if (form.password && form.password.length < 6) {
    formErrors.password = 'A senha deve ter no mínimo 6 caracteres.'
  }

  return Object.keys(formErrors).length === 0
}

async function salvar() {
    if (!validar()) return

    saving.value = true
    try {
        if (modo.value === 'create') {
            await store.criar({
                nome: form.nome,
                email: form.email,
                password: form.password,
                papeis_ids: form.papeis_ids,
            })
            await alertSuccess('Criado!', 'Usuário cadastrado com sucesso.')
        } else {
            const id = editingId.value!
            await store.atualizar(id, {
                nome: form.nome,
                email: form.email,
                status: form.status,
                password: form.password || undefined,
                papeis_ids: form.papeis_ids,
            })

            await alertSuccess('Atualizado!', `Usuário #${id} atualizado com sucesso.`)
        }

        fecharModal()
        await reload()
    } catch {
        await alertError('Erro', store.error || 'Não foi possível salvar o usuário.')
    } finally {
        saving.value = false
    }
}

async function excluir(id: number) {
    const ok = await alertConfirm('Excluir usuário?', `Você deseja excluir o usuário #${id}?`, 'Excluir')
    if (!ok) return

    try {
        await store.excluir(id)
        await alertSuccess('Excluído!', `Usuário #${id} excluído com sucesso.`)

        if (store.items.length === 1 && store.page > 1) store.page--
        await store.fetch(true)
    } catch {
        await alertError('Erro', store.error || 'Não foi possível excluir o usuário.')
    }
}

function voltarDashboard() {
    router.push('/dashboard')
}

function logout() {
    auth.logout()
    router.push('/login')
}
</script>