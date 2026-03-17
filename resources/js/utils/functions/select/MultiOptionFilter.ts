import axios from 'axios';
const MultiOptionFilter = async (api:string,params:Record<string,any>) => {
  try {
    const response = await axios.get(route(api),{params:params})
    return response.data.options
  } catch (error: any) {
    console.error(error)
    return []
  } 
}
export default MultiOptionFilter;