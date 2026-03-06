import { useDebounceFn } from '@vueuse/core'
import { Ref } from 'vue';
import axios from 'axios'
import MyBasicToast from '../ToastNotificationBasic';
interface GetElementsProps{
        api:string,
        params:() => Record<string,any>,
        rows:Ref<any[]>,
        totalRows:Ref<number>,
        loading:Ref<boolean>,
    }
const GetElementsFuntion=({loading,params,rows,totalRows,api}:GetElementsProps)=>{
    let controller: AbortController | null = null;
    
    const GetElements = async () => {
        try {
            if (controller) {
                controller.abort();
            }
            controller = new AbortController();

            loading.value = true;

            const response = await axios.get(route('getusers'), {
                params: params(),
                signal: controller.signal
            });

            rows.value = response.data.elements || [];
            totalRows.value = response.data.totalElements || 0;

        } catch (error: any) {

            if (error.name === 'CanceledError' || error.code === 'ERR_CANCELED') {
                return;
            }
            if (error.response?.status === 500) {
                MyBasicToast.error(error.response.data.message || 'Error del servidor');
            } else {
                console.error('Error:', error);
            }
        }finally {
            if (!controller?.signal.aborted) {
                loading.value = false;
            }
        }
    };
    const debouncedGetElements = useDebounceFn(() => {
        GetElements()
    }, 400);
    
    return {debouncedGetElements,GetElements}
}
export default GetElementsFuntion;