import axios from 'axios';
const GetModulosDisponibles = async () => {
  try {
    const response = await axios.get(route('select.modulos.disponibles.usuario'))
    return response.data.options
  } catch (error: any) {
    console.error(error)
    return [];
  } 
}
export default GetModulosDisponibles;