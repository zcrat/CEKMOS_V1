import axios from 'axios'
import MyBasicToast from "@/utils/ToastNotificationBasic";
import ZDCanvas from '@/components/Zcrat/Elements/ZDCanvas.vue';
import { EconomicoForm, ImagenesForm } from '@/types/OrdenServicio';
import { Ref } from 'vue';
  
export const GetImageTipoVehiculo = async ({Canvas,Tipo}:{Canvas:InstanceType<typeof ZDCanvas> | null,Tipo:number}) => {
    try {
      const response = await axios.get(route('image.tipo.vehiculo', { type: Tipo }), {
        responseType: 'blob'
      });
      Canvas?.dibujarImagen(response.data);
    } catch (error:any) {
      if (error.response) {
        const blob = error.response.data;
        const contentType = error.response.headers['content-type'];
        if (contentType && contentType.includes('application/json')) {
          const text = await blob.text();
          const jsonError = JSON.parse(text);
          MyBasicToast.error(jsonError.message || 'Error al encontrar la imagen de referencia');
        } else {
          MyBasicToast.error('Error desconocido del servidor');
        }
      } else {
        MyBasicToast.error('Error al conectar con el servidor');
      }
    }
}
export const SaveImagesEvidencia = ({event,Imagenes}: {event:Event,Imagenes:ImagenesForm[]}) => {
    const target = event.target as HTMLInputElement
    if (target.files) {
      if (Array.from(target.files).some(file => !file.type.startsWith('image/'))) {
        MyBasicToast.error('Solo se permiten archivos de imagen');
          return;
      } 
      Array.from(target.files).forEach(file => { 
        Imagenes.push({ tipo_id: 3, image: file })
      })
    }
    target.value = '';
  }
export function CovertBlobToURL(file: Blob) {
    return window.URL.createObjectURL(file)
}
export const DeleteImage=({index,Imagenes}:{index:number,Imagenes:ImagenesForm[]})=>{
    const registro=Imagenes[index];
    if(registro){
      if(registro.ids?.id){
        // Aquí podrías hacer una petición al servidor para eliminar la imagen usando el ID
        // Por ejemplo: await axios.delete(`/api/imagenes/${registro.id}`);
      }else{
        Imagenes.splice(index, 1);
      }
    }
  }
export const DeleteImagesNew=(Imagenes:ImagenesForm[])=>{
    const imagenesNuevas = Imagenes.filter(img => !img.ids?.id);
    if (imagenesNuevas.length > 0) {
      imagenesNuevas.forEach((img) => {
        const index = Imagenes.findIndex(i => i.image === img.image);
        if (index !== -1) {
          Imagenes.splice(index, 1);
        }
      });
    }else{
      MyBasicToast.error('No hay imágenes nuevas para eliminar');
    }
  }
export const GetDataVehiculoEconomico=(Economico:EconomicoForm)=>{
    if(Economico.economico){
      if(Economico.economico.length < 5){
        MyBasicToast.error('Longitud mínima de N# economico es de 5 caracteres');
        return;
      }
      axios.get(route('Vehiculo.Get.Datos'), {
        params: { search: Economico.economico ,filtro:'economico'}
      })
      .then(response => {
        const data = response.data.datos;
        if (data) {
          Economico.placas = data.placas;
          Economico.vin = data.vin;
          Economico.anio = data.año;
          Economico.marca = data.modelo.marca.descripcion;
          Economico.modelo = data.modelo.descripcion;
          Economico.tipo_id = data.tipo_id;
        } else {
          if(!Economico.placas){
            MyBasicToast.warning('Crearas un nuevo vehículo, no se encontraron datos con el economico proporcionado');
          }
        }
      })
      .catch(error => {
        console.error('Error al obtener los datos del vehículo:', error);
      });
    }
  }
export const GetDataVehiculoPlacas=(Economico:EconomicoForm)=>{
    if(Economico.placas){
      console.log(Economico.placas)
      if(Economico.placas.length < 6){
        MyBasicToast.error('Longitud mínima de N# placas es de 6 caracteres');
        return;
      }
      axios.get(route('Vehiculo.Get.Datos'), {
        params: { search: Economico.placas ,filtro:'placas'}
      })
      .then(response => {
        const data = response.data.datos;
        if (data) {
          Economico.economico = data.economico;
          Economico.vin = data.vin;
          Economico.anio = data.año;
          Economico.marca = data.modelo.marca.descripcion;
          Economico.modelo = data.modelo.descripcion;
          Economico.tipo_id = data.tipo_id;
        } else {
          if(!Economico.economico){
            MyBasicToast.warning('Crearas un nuevo vehículo, no se encontraron datos con el economico proporcionado');
          }
        }
      })
      .catch(error => {
        console.error('Error al obtener los datos del vehículo:', error);
      });
    }
  }
export const SaveCarAndFirma = async({Carro,Firma,Imagenes}:{Carro:InstanceType<typeof ZDCanvas> | null,Firma:InstanceType<typeof ZDCanvas> | null,Imagenes:ImagenesForm[]})=>{
    const carro =await Carro?.getCanvasBlob();
    const existingIndexCarro = Imagenes.findIndex(img => img.tipo_id === 1);
    if(carro != null){
      if (existingIndexCarro !== -1) {
        Imagenes[existingIndexCarro].image = carro;
      } else {
        Imagenes.push({tipo_id:1,image:carro});
      }
    } else {
      if (existingIndexCarro !== -1) {
        Imagenes.splice(existingIndexCarro, 1);
      }
    }
    const firma=await Firma?.getCanvasBlob();
    const existingIndexFirma = Imagenes.findIndex(img => img.tipo_id === 2);
    if(firma != null){
      if (existingIndexFirma !== -1) {
        Imagenes[existingIndexFirma].image = firma;
      } else {
        Imagenes.push({tipo_id:2,image:firma});
      }
    } else {
      if (existingIndexFirma !== -1) {
        Imagenes.splice(existingIndexFirma, 1);
      }
    }
  }