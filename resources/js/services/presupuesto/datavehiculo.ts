export const GetDatosVehiculo = async (filter:string,value:string) => {
  if (abortgetvehiculo.value) {
    abortgetvehiculo.value.abort();
  }

  abortgetvehiculo.value = new AbortController();

  try {
    const Vehiculo:Vehiculo|null = await axios.get(route('Vehiculo.Get.Datos'),{params:{filter,value},signal: abortgetvehiculo.value?.signal, })
    if(Vehiculo){
      cancelgetvehiculodata = true;
      if(filter==='economico') presupuesto.placas=Vehiculo.placas;
      if(filter==='placas') presupuesto.economico=Vehiculo.economico;
      presupuesto.vin=Vehiculo.vin;
      presupuesto.marca=Vehiculo.modelo?.marca?.descripcion??'';
      presupuesto.modelo=Vehiculo.modelo?.descripcion??'';
      presupuesto.año=Vehiculo.año;
      cancelgetvehiculodata = false;
    }
  } catch (error: any) {
    if (axios.isCancel(error)) {
      console.log('Solicitud cancelada');
    } else {
      console.error('Error al obtener datos del vehículo:', error);
    }
  } 
}