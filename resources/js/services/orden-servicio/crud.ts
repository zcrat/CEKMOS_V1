import axios from 'axios';
import { responseBasic } from '@/types/generales';
import { OrdenServicioForm } from '@/types/OrdenServicio';

export const Create = async (data: OrdenServicioForm): Promise<responseBasic> => {
  try {
    const formData = new FormData();

    // 🔹 recorrer todo
    Object.entries(data).forEach(([key, value]) => {
      if (value === null || value === undefined) return;
      if (value instanceof Blob) {
        formData.append(key, value);
        return;
      }
      if (Array.isArray(value)) {
        value.forEach((item, index) => {
          if (item instanceof Blob) {
            formData.append(`${key}[]`, item);
          } else {
            formData.append(`${key}[]`, JSON.stringify(item));
          }
        });
        return;
      }
      if (typeof value === 'object') {
        if (value instanceof Date) {
          formData.append(key, value.toISOString()); // o formato que necesites
          return;
        }
        Object.entries(value).forEach(([subkey, subvalue]) => {
          if (subvalue != null && subvalue !== undefined) {
            formData.append(`${key}[${subkey}]`, String(subvalue));
          } 
        });
        return;
      }
      formData.append(key, String(value));
    });

    const response = await axios.post(
      route('Cortana.OrdenServicio.Create'),
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    );

    return { status: true, code: 200, data: response.data };

  } catch (error: any) {
    const status = error.response?.status ?? 0;
    const data = error.response?.data ?? 'Error inesperado';

    if (status === 422 && data.errors) {
      return {
        status: false,
        code: 422,
        data: {
          message: 'Error de validación',
        },
        validationErrors: data.errors,
      };
    }

    return { status: false, code: status, data };
  }
};