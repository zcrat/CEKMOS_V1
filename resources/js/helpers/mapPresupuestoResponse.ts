import { datagetpresupuestos, NuevoPresupuesto } from '@/types/generales';
import { Ref } from 'vue';
export const mapPresupuestoResponse = (
  response: datagetpresupuestos,
  presupuesto: NuevoPresupuesto,
  empresa: Ref<any>,
  cliente: Ref<any>,
  vehiculoconcepto: Ref<any>
) => {
  Object.assign(presupuesto, response.presupuesto);
  empresa.value = response.empresa;
  cliente.value = response.cliente;
  vehiculoconcepto.value = response.vehiculo_concepto;
};