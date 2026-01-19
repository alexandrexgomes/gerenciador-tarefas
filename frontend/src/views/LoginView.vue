<template>
  <div class="min-vh-100 d-flex align-items-center bg-light">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <div class="text-center mb-4">
                <div class="mb-2">
                  <i class="bi bi-check2-square fs-3 me-2"></i>
                </div>
                <h1 class="h4 mb-1">Gerenciador de Tarefas</h1>
                <p class="text-muted mb-0">Acesse para continuar</p>
              </div>

              <form @submit.prevent="onSubmit" novalidate>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input
                    v-model.trim="email"
                    type="email"
                    class="form-control"
                    placeholder="seu@email.com"
                    autocomplete="username"
                    required
                    :disabled="auth.loading"
                  />
                </div>

                <div class="mb-3">
                  <label class="form-label">Senha</label>
                  <input
                    v-model="password"
                    type="password"
                    class="form-control"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                    :disabled="auth.loading"
                  />
                </div>

                <div v-if="auth.error" class="alert alert-danger py-2" role="alert">
                  {{ auth.error }}
                </div>

                <button
                  class="btn btn-primary w-100"
                  type="submit"
                  :disabled="auth.loading">
                  <span
                    v-if="auth.loading"
                    class="spinner-border spinner-border-sm me-2"
                    aria-hidden="true"
                  ></span>
                  {{ auth.loading ? 'Entrando...' : 'Entrar' }}
                </button>                
              </form>

              <div class="text-center mt-3">
                <small class="text-muted">API: {{ apiBase }}</small>
              </div>
            </div>
          </div>

          <p class="text-center text-muted mt-3 mb-0">
            <small>© {{ new Date().getFullYear() }}</small>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.store'
import { alertError} from '../utils/alert'

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const password = ref('')

const apiBase = computed(() => import.meta.env.VITE_API_URL)

async function onSubmit() {
  if (!email.value || !password.value) return

  try {
    await auth.login(email.value, password.value)
    router.push({ name: 'dashboard', query: { welcome: '1' } })
  } catch (e){
    await alertError('Falha no login', auth.error || 'Verifique suas credenciais e tente novamente.')
  }
}
</script>