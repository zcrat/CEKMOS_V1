<!-- ModalExample.vue -->
<script setup lang="ts">
import BaseModal from '@/components/Zcrat/modals/BasicModal.vue'
import ButtonTogglePYR from '@/components/Zcrat/Inputs/ButtonTogglePYR.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import axios from 'axios'
import { ref, watch } from 'vue' 
import Loanding from '@/components/Zcrat/Elements/Loanding.vue';


const props = defineProps<{ show: boolean , id: number | null }>()
const loanding = ref<boolean>(true)

const allroles =  ref<string[]>([])
const allpermisos = ref<string[]>([])
const userroles =  ref<string[]>([])
const userpermisos =  ref<string[]>([])
const accion =  ref<string>("Obteniendo Roles Y Permisos")

const emit = defineEmits(['update:show'])

const updateVisibility = (val: boolean) => {
  emit('update:show', val)
}
console.log(props.id);
const GetPermisosAndRoles=async ()=>{
    try {
        loanding.value=true;
        accion.value="Obteniendo Roles Y Permisos";
        const response = await axios.get(route('getpermisosuser'),
         { params: { id: props.id } }
        )
        allroles.value=response.data.allroles;
        allpermisos.value=response.data.allpermisos;
        userroles.value=response.data.userroles;
        userpermisos.value=response.data.userpermisos;
    } catch (error : any) {
        if (error.response?.status === 500) {
            MyBasicToast.error(error.response.data.message || 'Error del servidor')
        } else {
            console.error('Error:', error)
        }
    }finally{
        loanding.value=false
    }
}
const ToggleRolesOrPermission=async(rol:string, isRole:boolean)=>{
    try {
        loanding.value=true;
        accion.value=isRole?"Actualizando Roles Y Permisos":"Actualizando Permisos";
        const response = await axios.post(isRole ? route('toggle.role') : route('toggle.permiso'),
         {id: props.id, [isRole ? 'role' : 'permiso']: rol }
        )
        allroles.value = [...response.data.allroles]
        allpermisos.value = [...response.data.allpermisos]
        userroles.value = [...response.data.userroles]
        userpermisos.value = [...response.data.userpermisos]

    } catch (error : any) {
        if (error.response?.status) {
            MyBasicToast.error(error.response.data.message || 'Error del servidor')
        } else {
            console.error('Error:', error)
        }
    }finally{
        loanding.value=false
    }
}
watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      GetPermisosAndRoles()
    }
  }
)


</script>

<template>
  <BaseModal modaltitle="Administrar Permisos Y Roles" :position="'center'" :modelValue="props.show" @update:modelValue="updateVisibility" >
    <Loanding v-if="loanding" :text="accion"/>
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
     <div class="p-2 flex flex-col w-full sm:w-[auto] gap-1 border-solid border-4 rounded border-blue-300">
        <h3 class="text-center font-semibold text-[1.3rem] mb-4">Roles</h3>
        <template v-for="role in allroles" :key="role">
          <ButtonTogglePYR 
            :isnew="!userroles.includes(role)" 
            :text="role"  
            :onClick="()=>{ToggleRolesOrPermission(role, true)}"
          />
        </template>
      </div>
      <div class="p-2 flex flex-col w-full sm:w-[auto] gap-1 border-solid border-4 rounded border-blue-300">
        <h3 class="text-center font-semibold text-[1.3rem] mb-4">Permisos</h3>
        <template v-for="permiso in allpermisos" :key="permiso">
          <ButtonTogglePYR 
            :isnew="!userpermisos.includes(permiso)" 
            :text="permiso"  
            :onClick="()=>{ToggleRolesOrPermission(permiso, false)}" 
          />
        </template>
      </div>
    </div>
  </BaseModal>
</template>
