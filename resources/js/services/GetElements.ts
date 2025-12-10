import axios from 'axios'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import { Ref } from 'vue'
interface ApiResponse {
  items: any[],
  totalItems: number,
  totalpages: number,
}
 const GetElements = async (
  loading: Ref<boolean>,
  items: Ref<any[]>,
  totalItems: Ref<number>,
  totalpages: Ref<number>,
  api: string,
  params?:any
) => {
  try {
    loading.value = true

    const response = await axios.get<ApiResponse>(route(api),{
      params: params ?? {}
    });
    items.value = response.data.items ?? []
    totalItems.value = response.data.totalItems ?? 0
    totalpages.value = response.data.totalpages ?? 0
  } catch (error: any) {
    if (axios.isAxiosError(error)) {
      const status = error.response?.status
      const message = error.response?.data?.message

      if (status === 422) {
        MyBasicToast.error('Error de validación')
      } else if (status === 404) {
        MyBasicToast.error('Recurso no encontrado')
      } else if (status === 500) {
        MyBasicToast.error(message || 'Error del servidor')
      } else {
        MyBasicToast.error('Error inesperado en la solicitud')
      }
    } else {
      console.error('Error desconocido:', error)
    }
  } finally {
    loading.value = false
  }
}
export default GetElements;