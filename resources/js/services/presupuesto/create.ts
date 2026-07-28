import axios from 'axios';
import type { NuevoPresupuesto } from '@/types/generales';

type CreatePresupuestoResult =
  | {
      status: true;
      code: number;
      data: {
        id: number;
        message: string;
        orden_servicio: string;
      };
    }
  | {
      status: false;
      code: number;
      validationErrors?: Record<string, string[]>;
      message: string;
    };

const Create = async (
  data: NuevoPresupuesto,
): Promise<CreatePresupuestoResult> => {
  try {
    const response = await axios.post<{
      id: number;
      message: string;
      orden_servicio: string;
    }>(route('presupuesto.create'), data);

    return {
      status: true,
      code: response.status,
      data: response.data,
    };
  } catch (error: unknown) {
    if (!axios.isAxiosError(error)) {
      return {
        status: false,
        code: 0,
        message: 'Error inesperado',
      };
    }

    const status = error.response?.status ?? 0;
    const responseData = error.response?.data as {
      errors?: Record<string, string[]>;
      message?: string;
    } | undefined;

    return {
      status: false,
      code: status,
      validationErrors: responseData?.errors,
      message: responseData?.message ?? (
        status === 0
          ? 'Error de conexión'
          : 'No fue posible crear el presupuesto'
      ),
    };
  }
};

export default Create;
