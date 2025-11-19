import { useCancelableGet } from './useCancelableGet';
import { mapVehiculoToPresupuesto } from '@/helpers/mapVehiculoToPresupuesto';
import { NuevoPresupuesto } from '@/types/generales';
import { getVehiculoDatos } from '@/services/vehiculoService';
import { Ref } from 'vue';
export const useVehiculoFetcher = (
  presupuesto: NuevoPresupuesto,
  cancelFlag: Ref<boolean>
) => {
  const { get } = useCancelableGet();

  const fetchvehiculo = async (filter: string, value: string) => {
    const result = await get(route('Vehiculo.Get.Datos'), { filter, value });

    if (result.status && result.data) {
      cancelFlag.value = true;
      mapVehiculoToPresupuesto(result.data, filter, presupuesto);
      cancelFlag.value = false;
    }
  };

  return { fetchvehiculo };
  const fetchvehiculo2 = async (filter: string, value: string) => {
    const controller = new AbortController();
    cancelFlag.value = false;

    try {
      // Usamos la función del servicio para centralizar la llamada
      const result = await getVehiculoDatos(filter, value, controller.signal);

      if (result.status && result.data) {
        cancelFlag.value = true;
        mapVehiculoToPresupuesto(result.data, filter, presupuesto);
        cancelFlag.value = false;
      }

    } catch (error) {
      cancelFlag.value = false;
      // Aquí podrías manejar el error si quieres
      console.error('Error fetchvehiculoing vehicle data:', error);
    }
  };
};

