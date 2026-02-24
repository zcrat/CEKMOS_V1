import axios from 'axios';
const GetEstatusFilter = async (categoria:number) => {
  try {
    const response = await axios.get(route('select.status'),{params:{'categoria_id':categoria}})
    return response.data.options
  } catch (error: any) {
    console.error(error)
    return []
  } 
}
export default GetEstatusFilter;