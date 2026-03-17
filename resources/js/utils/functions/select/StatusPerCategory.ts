import axios from 'axios';
const GetStatusPerCategory = async (category:number) => {
  try {
    const response = await axios.get(route('select.status'),{params:{'categoria_id':category}})
    return response.data.options
  } catch (error: any) {
    console.error(error)
    return []
  } 
}
export default GetStatusPerCategory;