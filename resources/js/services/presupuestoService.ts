import axios from 'axios';

export const getDatosPresupuesto = async (
  orden_servicio: string,
  signal: AbortSignal
) => {
  try {
    const response = await axios.get(route('Presupuesto.Get.Data_Orden'), {
      params: { orden_servicio },
      signal,
    });
    return { status: true, data: response.data };
  } catch (error: any) {
    if (axios.isCancel(error)) {
      console.log('Solicitud cancelada');
    } else {
      console.error('Error al obtener datos del presupuesto:', error);
    }
    return { status: false, data: null };
  }
};