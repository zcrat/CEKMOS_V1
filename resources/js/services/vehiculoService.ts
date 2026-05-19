import axios from 'axios';

export const getVehiculoDatos = async (
  filter: string,
  value: string,
  signal: AbortSignal
) => {
  try {
    const response = await axios.get(route('Vehiculo.Get.Datos'), {
      params: { filter, value },
      signal,
    });
    return { status: true, data: response.data };
  } catch (error: any) {
    if (axios.isCancel(error)) {
      console.log('Solicitud cancelada');
    } else {
      console.error('Error al obtener datos del vehículo:', error);
    }
    return { status: false, data: null };
  }
};