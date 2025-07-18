
import { useToast, POSITION } from 'vue-toastification'

const toast = useToast()

const MyBasicToast = {
  success: (msg = "Solicitud Realizada Correctamente") =>
    toast.success(msg, {
        timeout: 2000,
        position:POSITION.TOP_RIGHT,
        closeOnClick: true,
        pauseOnHover: false,
        hideProgressBar:true
    }),
  error: (msg = "Error Al Procesar La Solicitud") =>
    toast.error(msg, {
        timeout: 2000,
        position:POSITION.TOP_RIGHT,
        closeOnClick: true,
        pauseOnHover: false,
        hideProgressBar:true
    }),
}
export default MyBasicToast
