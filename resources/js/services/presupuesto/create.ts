import axios from 'axios';
import { NuevoPresupuesto } from '@/types/generales';
const Create = async (data:NuevoPresupuesto) => {
  try {
    const response = await axios.get(route('presupuesto.get.data_orden'),{params:{...data} })
    return { status: true, code: 200, data: response.data };
  } catch (error: any) {
    const status = error.response?.status ?? 0;
    const data = error.response?.data ?? 'Error inesperado';
  if (status === 422 && data.errors) {
    return {
      status: false,
      code: 422,
      validationErrors: data.errors,
      message: 'Error de validación',
    };
  }
  return { status: false, code: status, data };
}

}
export default Create;
