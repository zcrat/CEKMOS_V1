import { useCancelableGet } from '@/composables/useCancelableGet';
import { mapPresupuestoResponse } from '@/helpers/mapPresupuestoResponse';
import { NuevoPresupuesto } from '@/types/generales';
import { Ref } from 'vue';

export const usePresupuestoFetcher = (
  presupuesto: NuevoPresupuesto,
  empresa: Ref<any>,
  cliente: Ref<any>,
  vehiculoconcepto: Ref<any>
) => {
  const { get } = useCancelableGet();

  const fetchDatosPresupuesto = async (orden_servicio: string) => {
    const result = await get(route('Presupuesto.Get.Data_Orden'), { orden_servicio });

    if (result.status && result.data) {
      mapPresupuestoResponse(result.data, presupuesto, empresa, cliente, vehiculoconcepto);
    }
  };

  return { fetchDatosPresupuesto };
};