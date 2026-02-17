import axios from 'axios';
import { FormEmployee } from '@/types/generales';
const Create = async (data:FormEmployee) => {
  try {
    const response = await axios.post(route('employees.create'),{...data})
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