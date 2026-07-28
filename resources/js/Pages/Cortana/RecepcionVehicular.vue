<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {computed,ref} from 'vue'
import Search from '@/components/Zcrat/Inputs/Search.vue';
import Table from '@/components/Zcrat/Elements/Table.vue'
import Dropdown from '@/components/Zcrat/Elements/Dropdown.vue'
import Button from '@/components/Zcrat/Inputs/Button.vue';
import MultiOptionFilter from '@/components/Zcrat/Filters/MultiOptionFilter.vue';
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import empresasselect from '@/components/Zcrat/Filters/empresasselect.vue'
import {
    type AccionSeguimientoOrdenServicio,
    RecepcionesVehiculares,
} from '@/types/generales';
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import OrdenServicio from '@/components/Zcrat/modals/OrdenServicio.vue';
import { OrderKeyProp } from '@/types/tablecomponent';
import { ToggleUploadFiles } from '@/utils/functions/ordenservicio';
import { useEcho } from '@laravel/echo-vue';
import PDFDemo from '@/components/Zcrat/modals/PDFDemo.vue';
import InspeccionVehicularModal from '@/components/Zcrat/modals/InspeccionVehicularModal.vue';
import CambiarModuloOrdenServicioModal from '@/components/Zcrat/modals/CambiarModuloOrdenServicioModal.vue';
import CambiarTallerOrdenServicioModal from '@/components/Zcrat/modals/CambiarTallerOrdenServicioModal.vue';
import SubcontratosOrdenServicioModal from '@/components/Zcrat/modals/SubcontratosOrdenServicioModal.vue';
import { useAuth } from '@/composables/useAuth';
import AsignarUsuarioOrdenServicioModal from '@/components/Zcrat/modals/AsignarUsuarioOrdenServicioModal.vue';
import IconActions from '@/components/Zcrat/Elements/IconActions.vue';
import axios from 'axios';
import { ZdAlert } from '@/utils/ZdAlert';
import MyBasicToast from '@/utils/ToastNotificationBasic';
import { useAccionesSeguimiento } from '@/services/orden-servicio/acciones';

const currentPage=ref<number>(1)
const itemsPerPage=ref<number>(10)
const totalItems=ref<number>(0)
const items=ref<RecepcionesVehiculares[]>([])
const search = ref<string>('');
const empresa = ref<string|null>(null);
const estatus = ref<string[]>([]);
const modulos = ref<string[]>([]);
const loading = ref<boolean>(true);
const message_empty=ref<string>('No hay recepciones vehiculares para mostrar')
const statusParams = { categoria_id: 2 }
const orderBy=ref<null|OrderKeyProp>(null)
const ModalOrdenServicio = ref<InstanceType<typeof OrdenServicio> | null>(null);
const pdf = ref<InstanceType<typeof PDFDemo> | null>(null);
const inspeccionModal = ref<InstanceType<typeof InspeccionVehicularModal> | null>(null);
const selectedOrder = ref<RecepcionesVehiculares | null>(null);
const actionModal = ref<
  'taller' | 'modulo' | 'subcontratos' | 'usuario' | null
>(null);
const refreshKey = ref(0);
const { can } = useAuth();
const { accionesPorEstatus } = useAccionesSeguimiento();
const canManageReception = can('crear_ordenes_servicio');
const canChangeModule = can('cambiar_modulo_presupuestos');
const canRollbackStatus = can('retroceder_seguimiento');
interface DaTaUpdateWebSocket extends Record<string,any> {
    id:number
}
const params = computed(() => ({
  search: search.value,
  estatus: estatus.value,
  empresa: empresa.value,
  modulos: modulos.value,
  refresh: refreshKey.value,
}))

const openAction = (
  action: 'taller' | 'modulo' | 'subcontratos' | 'usuario',
  order: RecepcionesVehiculares,
) => {
  selectedOrder.value = order;
  actionModal.value = action;
};

const closeAction = () => {
  actionModal.value = null;
};

const refreshOrders = () => {
  refreshKey.value += 1;
};

const statusActionIcons: Record<string, string> = {
  iniciar_diagnostico: 'fa-solid fa-stethoscope',
  terminar_diagnostico: 'fa-solid fa-clipboard-check',
  terminar_mano_obra: 'fa-solid fa-screwdriver-wrench',
  revisar_mano_obra: 'fa-solid fa-magnifying-glass',
  aprobar_mano_obra: 'fa-solid fa-circle-check',
  denegar_mano_obra: 'fa-solid fa-circle-xmark',
  corregir_mano_obra: 'fa-solid fa-screwdriver-wrench',
  entregar_unidad: 'fa-solid fa-car-side',
  reingresar_unidad: 'fa-solid fa-rotate-left',
  reiniciar_diagnostico: 'fa-solid fa-stethoscope',
};

const changeStatus = async (
  order: RecepcionesVehiculares,
  action: AccionSeguimientoOrdenServicio,
) => {
  const confirmed = await ZdAlert({
    title: action.descripcion,
    text: `La orden está en "${order.estatus}". ¿Deseas continuar?`,
    confirmButtonText: action.descripcion,
  });

  if (!confirmed) return;

  try {
    const response = await axios.patch(
      route('ordenes-servicio.seguimiento.update', order.id),
      { accion: action.clave },
    );
    MyBasicToast.success(response.data.message);
    refreshOrders();
  } catch (error) {
    MyBasicToast.error(
      axios.isAxiosError(error)
        ? error.response?.data?.message ?? 'No fue posible cambiar el estatus'
        : 'No fue posible cambiar el estatus',
    );
  }
};

const rollbackStatus = async (order: RecepcionesVehiculares) => {
  const confirmed = await ZdAlert({
    title: 'Retroceder seguimiento',
    text: `Se eliminará el último estatus de la orden, actualmente "${order.estatus}". ¿Deseas continuar?`,
    confirmButtonText: 'Eliminar último estatus',
  });

  if (!confirmed) return;

  try {
    const response = await axios.delete(
      route('ordenes-servicio.seguimiento.destroy-last', order.id),
    );
    MyBasicToast.success(response.data.message);
    refreshOrders();
  } catch (error) {
    MyBasicToast.error(
      axios.isAxiosError(error)
        ? error.response?.data?.message ?? 'No fue posible retroceder el seguimiento'
        : 'No fue posible retroceder el seguimiento',
    );
  }
};

const ActionRecepciones = (row: RecepcionesVehiculares) => [
  ...(canRollbackStatus && row.tiene_seguimiento
    ? [{
        text:'RS',
        title:'Retroceder seguimiento',
        onClick:()=>rollbackStatus(row),
        classname:'border-red-300 text-red-700 hover:border-red-500 hover:bg-red-50 hover:text-red-800'
      }]
    : []),
  ...accionesPorEstatus(row.estatus).map(action => ({
    icon:statusActionIcons[action.clave] ?? 'fa-solid fa-forward-step',
    title:action.descripcion,
    onClick:()=>changeStatus(row, action),
    classname:action.clave.includes('denegar')
      ? 'border-red-300 bg-red-50 text-red-700 hover:border-red-500 hover:bg-red-100'
      : 'border-green-300 bg-green-50 text-green-700 hover:border-green-500 hover:bg-green-100'
  })),
  ...(canManageReception ? [
    {
      icon:'fa-solid fa-screwdriver-wrench',
      title:'Cambiar taller',
      onClick:()=>openAction('taller', row),
      classname:'border-amber-300 bg-amber-50 text-amber-700 hover:border-amber-500 hover:bg-amber-100'
    },
    {
      icon:'fa-solid fa-handshake',
      title:'Subcontratos',
      onClick:()=>openAction('subcontratos', row),
      classname:'border-violet-300 bg-violet-50 text-violet-700 hover:border-violet-500 hover:bg-violet-100'
    },
    {
      icon:'fa-solid fa-user-gear',
      title:row.user_asignado
        ? 'Cambiar usuario'
        : 'Asignar usuario',
      onClick:()=>openAction('usuario', row),
      classname:'border-gray-300 bg-gray-50 text-gray-700 hover:border-gray-500 hover:bg-gray-100'
    },
  ] : []),
  ...(canChangeModule ? [{
    icon:'fa-solid fa-right-left',
    title:'Cambiar módulo',
    onClick:()=>openAction('modulo', row),
    classname:'border-blue-300 bg-blue-50 text-blue-700 hover:border-blue-500 hover:bg-blue-100'
  }] : []),
];

const showActionsColumn = computed(
  () => items.value.some((row) => ActionRecepciones(row).length > 0),
);


useEcho(
  `ordenes_servicio`, '.update', (data:DaTaUpdateWebSocket) => {
    console.log(data)
    items.value = items.value.map((item)=>{
        if(item.id === data.id){
            return {...item,...data}
        }
        return item
    })
  }
)
useEcho(
  `ordenes_servicio`, '.delete', (data:{id:number}) => {
    items.value = items.value.filter((item)=>item.id !== data.id)
  }
)


</script>

<template>
    <AppLayout title="Recepciones Vehiculares" :loading="loading" loading-message="Cargando recepciones vehiculares">
        <template #header>
                <Button text="Nueva" @click="ModalOrdenServicio?.Open(null)"  />
        </template>
        <template #filtering>
            <div class="flex gap-2 flex-col lg:flex-row">
                <div class="flex gap-2 flex-row">
                    <Search Classdiv="sm:w-[25rem] w-full" placeholder="Buscar Por Order De Servicio, PLacas o Economico" v-model="search"/>
                    <empresasselect v-model="empresa" :canNew="false"/>
                </div>
                <div class="flex gap-2 items-end justify-between sm:justify-start">
                    <MultiOptionFilter v-model:selectedIds="estatus" api="select.status" :params="statusParams" label="Estatus"/>
                    <MultiOptionFilter v-model:selectedIds="modulos" api="select.modulos.disponibles.usuario" label="Modulos"/>
                    <Datapicker />
                </div>
            </div>
            <Pagination 
                api="recepcionesvehiculares.read"
                :params="params" 
                v-model:currentPage="currentPage" 
                v-model:itemsPerPage="itemsPerPage" 
                v-model:totalItems="totalItems"
                v-model:items="items"
                v-model:loading="loading"
                changesItems
            />
        </template>
        <template #content>
            <Table v-if="items.length>0" 
                v-model:OrderKey="orderBy"
                :titles="[
                    {title:'No. Orden',CanOrder:{'key':'orden',types:'desc'}},
                    {title:'No. Seguimiento'},
                    {title:'Ubicacion'},
                    {title:'Empresa'},
                    {title:'Taller'},
                    {title:'Usuario asignado'},
                    {title:'Vehiculo',subtittles:[
                        {title:'Economico'},
                        {title:'Placas'},
                        {title:'Marca'},
                        {title:'Modelo'},
                    ]},
                    {title:'Entrada'},
                    {title:'Estatus'},
                    {title:'Subcontratos'},
                    ...(showActionsColumn ? [{title:'Acciones'}] : []),
                    {title:'opciones'},
                ]"
                :rows="items.map(function(row){return {
                    classname:'bg-grey-300',
                    columns:[
                        {element:row.orden, classname:'capitalize'},
                        {element:row.seguimiento, classname:'capitalize'},
                        {element:row.ubicacion, classname:'capitalize'},
                        {element:row.empresa, classname:'lowercase'},
                        {element:row.taller, classname:'capitalize'},
                        {element:row.usuario_asignado, classname:'capitalize'},
                        {element:row.economico,classname:'uppercase'},
                        {element:row.placas,classname:'uppercase'},
                        {element:row.marca, classname:'uppercase'},
                        {element:row.modelo,classname:'uppercase'},
                        {element:row.creacion, classname:'uppercase'},
                        {element:row.estatus, classname:'uppercase'},
                        {
                            element:row.tiene_subcontrato_activo ? 'Activo' : (row.subcontratos_count > 0
                                ? row.subcontratos_count
                                : '-'),
                            classname:'text-center'
                        },
                        ...(showActionsColumn ? [{
                            element: ActionRecepciones(row).length > 0
                                ? IconActions
                                : '-',
                            props: {
                                actions: ActionRecepciones(row)
                            }
                        }] : []),
                        {element: Dropdown,
                            props: {
                                triggerText:'Opciones',
                                options: [
                                    {
                                        label:'Editar Recepcion', 
                                        onClick:()=>{ModalOrdenServicio?.Open(row.id)},
                                        classname:['hover:text-gray-800']
                                    },
                                    {
                                        label:'Ver PDF', 
                                        onClick:()=>{pdf?.Open(row.id)},
                                        classname:['hover:text-gray-800']
                                    },
                                    {
                                        label:'Capturar / editar inspección',
                                        onClick:()=>{inspeccionModal?.Open(row.id)},
                                        classname:['hover:text-gray-800']
                                    },
                                    {
                                        label:((row.cambiar_archivos ? 'Desactivar ' : 'Activar ') + 'Cambios en los Archivos'), 
                                        onClick:()=>{ToggleUploadFiles({id:row.rv_id,estatus:row.cambiar_archivos})},
                                        classname:['hover:text-gray-800']
                                    },
                                ].filter(Boolean)
                                ,
                                contentClasses:{
                                    bg:'bg-gray-300'
                                }
                            }
                        }
                    ]
                }})" 
                
                    classname="tabla mt-2">
            </Table>
            <div  class="flex-1 justify-center items-center flex" v-else>
                <span class="text-[1.5rem]">{{ message_empty }}</span>
            </div>
        </template>
    </AppLayout>
    <OrdenServicio ref="ModalOrdenServicio"/>
    <InspeccionVehicularModal ref="inspeccionModal"/>
    <PDFDemo ref="pdf"/>
    <CambiarTallerOrdenServicioModal
        :show="actionModal === 'taller'"
        :orden-servicio-id="selectedOrder?.id ?? null"
        :taller-actual="selectedOrder
            ? { value: selectedOrder.taller_id ?? '', label: selectedOrder.taller }
            : null"
        @close="closeAction"
        @saved="refreshOrders"
    />
    <CambiarModuloOrdenServicioModal
        :show="actionModal === 'modulo'"
        :orden-servicio-id="selectedOrder?.id ?? null"
        :modulo-actual="selectedOrder
            ? { value: selectedOrder.modulo_id, label: selectedOrder.modulo }
            : null"
        @close="closeAction"
        @saved="refreshOrders"
    />
    <SubcontratosOrdenServicioModal
        :show="actionModal === 'subcontratos'"
        :orden-servicio-id="selectedOrder?.id ?? null"
        :orden="selectedOrder?.orden ?? ''"
        @close="closeAction"
        @saved="refreshOrders"
    />
    <AsignarUsuarioOrdenServicioModal
        :show="actionModal === 'usuario'"
        :orden-servicio-id="selectedOrder?.id ?? null"
        :usuario-actual="selectedOrder?.user_asignado
            ? {
                value: selectedOrder.user_asignado,
                label: selectedOrder.usuario_asignado,
            }
            : null"
        @close="closeAction"
        @saved="refreshOrders"
    />
</template>
