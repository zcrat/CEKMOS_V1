import axios from 'axios';
import { ref } from 'vue';

export const useCancelableGet = () => {
  const controller = ref<AbortController | null>(null);

  const get = async (url: string, params: any) => {
    if (controller.value) controller.value.abort();
    controller.value = new AbortController();

    try {
      const response = await axios.get(url, {
        params,
        signal: controller.value.signal,
      });
      return { status: true, data: response.data };
    } catch (error: any) {
      if (axios.isCancel(error)) {
        console.log('Solicitud cancelada');
      } else {
        console.error('Error en solicitud:', error);
      }
      return { status: false, data: null };
    }
  };

  return { get };
};