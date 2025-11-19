import { Vehiculo, NuevoPresupuesto } from '@/types/generales';

export const mapVehiculoToPresupuesto = (
  vehiculo: Vehiculo,
  filter: string,
  presupuesto: NuevoPresupuesto
) => {
  if (filter === 'economico') presupuesto.placas = vehiculo.placas;
  if (filter === 'placas') presupuesto.economico = vehiculo.economico;

  presupuesto.vin = vehiculo.vin;
  presupuesto.marca = vehiculo.modelo?.marca?.descripcion ?? '';
  presupuesto.modelo = vehiculo.modelo?.descripcion ?? '';
  presupuesto.año = vehiculo.año;
};