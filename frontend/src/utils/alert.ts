import Swal from 'sweetalert2'

const toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 2200,
  timerProgressBar: true,
})

export function alertSuccess(title: string, text?: string) {
  return toast.fire({ icon: 'success', title, text })
}

export function alertError(title: string, text?: string) {
  return Swal.fire({ icon: 'error', title, text })
}

export async function alertConfirm(title: string, text?: string, confirmText = 'Confirmar') {
  const r = await Swal.fire({
    icon: 'warning',
    title,
    text,
    showCancelButton: true,
    confirmButtonText: confirmText,
    cancelButtonText: 'Voltar',
    reverseButtons: true,
  })
  return r.isConfirmed
}
