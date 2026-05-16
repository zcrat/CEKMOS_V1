import axios from 'axios'
import MyBasicToast from "@/utils/ToastNotificationBasic";
import ZDCanvas from '@/components/Zcrat/Elements/ZDCanvas.vue';
import { EconomicoForm} from '@/types/OrdenServicio';
import { Ref } from 'vue';
import { ZdAlert } from '../ZdAlert';
import { ImagenUpload } from '@/components/Zcrat/modals/partes/ordenservicio/ImagenesEvidencias.vue';
  
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
export const SaveImagesEvidencia = ({event,Imagenes}: {event:Event,Imagenes:File[]}) => {
    const target = event.target as HTMLInputElement
    if (target.files) {
      if (Array.from(target.files).some(file => !file.type.startsWith('image/'))) {
        MyBasicToast.error('Solo se permiten archivos de imagen');
          return;
      } 
      Array.from(target.files).forEach(file => { 
        Imagenes.push(file)
      })
    }
    target.value = '';
  }
export function CovertBlobToURL(file: Blob | File) {
    return window.URL.createObjectURL(file)
}
export const DeleteImage=({index,Imagenes,DeleteErrors}:{index:number,Imagenes:File[],DeleteErrors?:(val:string)=>void})=>{
    const registro=Imagenes[index];
    if(registro){
      Imagenes.splice(index, 1);
      DeleteErrors?.('imagenes_evidencia.'+(index+1))
    }
  }
export const DeleteImageDB=async (id:number)=>{
  const confirm=await ZdAlert({ title: '¿Eliminar Archivo?',
    text: 'Se Eliminara El Archivo Y No Se Podra Recuperar'
  });
    if(!confirm){return false}
     try {
      const response = await axios.delete(route('Cortana.Imagenes.Delete'), {params:{'id':id,origen:'ordenservicio'}});
      MyBasicToast.success(response.data.message);
       return true;
    } catch (error:any) {
      if (error.response) { 
       MyBasicToast.error(error.response.data.message ?? 'Error No Especificado');
      } else {
        MyBasicToast.error('Error al conectar con el servidor');
      }
      return false;
    }
}
export const DeleteImagesNew=(Imagenes:File[],DeleteErrors?:(val:string)=>void )=>{
    if (Imagenes.length > 0) {
      for (let i = Imagenes.length - 1; i >= 0; i--) {
        Imagenes.splice(i, 1);
        DeleteErrors?.('imagenes_evidencia.'+(i+1))
      }
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
export const ImageCanvas = async({Canvas,FileName}:{Canvas:InstanceType<typeof ZDCanvas> | null,FileName:string})=>{
    const Image =await Canvas?.getCanvasBlobZise();
    if(Image != null){
      return new File([Image], FileName, {
        type: Image.type,
      });
    } else {
      return null;
    }
    
  }
export const ToggleUploadFiles = async({id,estatus}:{id:number,estatus:boolean})=>{
    const confirm=await ZdAlert({ title:'Actualizacion de Archivos', text:estatus?'Cancelar que los usuarios cambien, eliminen y agregen archivos a la Orden De Servicio' : 'Permitir que los usuarios cambien, eliminen y agregen archivos a la Orden De Servicio'});
    if(!confirm){return}
    axios.put(route('Cortana.Orden.Toggle.Upload.Files'), {'id':id})
    .then(response => {
      const data = response.data.message;
       MyBasicToast.success(data);
      })
    .catch(error => {
       MyBasicToast.error(error.response.data.message ?? 'Error No Especificado');
    });
    
  }