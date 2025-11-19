import axios from 'axios';
const GetNivelesGasolina = async () => {
  try {
    const response = await axios.get(route('select.niveles.combustible'))
    return response.data.options
  } catch (error: any) {
    console.error(error)
    return null
  } 
}
export default GetNivelesGasolina;