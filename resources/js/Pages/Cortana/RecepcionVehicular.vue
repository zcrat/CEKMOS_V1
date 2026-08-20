<script setup lang="ts">
import Dropdown from '@/components/Zcrat/Elements/Dropdown.vue';
import IconActions from '@/components/Zcrat/Elements/IconActions.vue';
import Table from '@/components/Zcrat/Elements/Table.vue';
import Datapicker from '@/components/Zcrat/Elements/ZDDataPicker.vue';
import EmpresasFilter from '@/components/Zcrat/Filters/EmpresasFilter.vue';
import EstatusRecepcionesFilter from '@/components/Zcrat/Filters/EstatusRecepcionesFilter.vue';
import ModulosOrdenServicioFilter from '@/components/Zcrat/Filters/ModulosOrdenServicioFilter.vue';
import TalleresFilter from '@/components/Zcrat/Filters/TalleresFilter.vue';
import UsuarioAsignadoFilter from '@/components/Zcrat/Filters/UsuarioAsignadoFilter.vue';
import Pagination from '@/components/Zcrat/Filters/pagination.vue';
import Button from '@/components/Zcrat/Inputs/Button.vue';
import Search from '@/components/Zcrat/Inputs/Search.vue';
import AsignarUsuarioOrdenServicioModal from '@/components/Zcrat/modals/AsignarUsuarioOrdenServicioModal.vue';
import CambiarModuloOrdenServicioModal from '@/components/Zcrat/modals/CambiarModuloOrdenServicioModal.vue';
import CambiarTallerOrdenServicioModal from '@/components/Zcrat/modals/CambiarTallerOrdenServicioModal.vue';
import EntregaUnidadOrdenServicioModal from '@/components/Zcrat/modals/EntregaUnidadOrdenServicioModal.vue';
import InspeccionVehicularModal from '@/components/Zcrat/modals/InspeccionVehicularModal.vue';
import OrdenServicio from '@/components/Zcrat/modals/OrdenServicio.vue';
import PDFDemo from '@/components/Zcrat/modals/PDFDemo.vue';
import SubcontratosOrdenServicioModal from '@/components/Zcrat/modals/SubcontratosOrdenServicioModal.vue';
import ValesAlmacenModal from '@/components/Zcrat/modals/ValesAlmacenModal.vue';
import { useRecepcionVehicularPage } from '@/composables/useRecepcionVehicularPage';
import AppLayout from '@/layouts/AppLayout.vue';
import { option, RecepcionesVehiculares } from '@/types/generales';
import { OrderKeyProp } from '@/types/tablecomponent';
import { computed, ref } from 'vue';

const currentPage = ref<number>(1);
const itemsPerPage = ref<number>(10);
const totalItems = ref<number>(0);
const items = ref<RecepcionesVehiculares[]>([]);
const search = ref<string>('');
const empresa = ref<option | null>(null);
const estatus = ref<(number | string)[]>([]);
const modulos = ref<(number | string)[]>([]);
const talleres = ref<(number | string)[]>([]);
const usuarioAsignado = ref<option | null>(null);
const fechas = ref<Date[] | null>(null);
const loading = ref<boolean>(true);
const message_empty = ref<string>('No hay recepciones vehiculares para mostrar');
const orderBy = ref<null | OrderKeyProp>(null);
const ModalOrdenServicio = ref<InstanceType<typeof OrdenServicio> | null>(null);
const pdf = ref<InstanceType<typeof PDFDemo> | null>(null);
const inspeccionModal = ref<InstanceType<typeof InspeccionVehicularModal> | null>(null);
const valesAlmacenModal = ref<InstanceType<typeof ValesAlmacenModal> | null>(null);
const { accionesRecepcion, actionModal, closeAction, formatDate, opcionesRecepcion, refreshKey, refreshOrders, selectedOrder, showActionsColumn } =
    useRecepcionVehicularPage(items, {
        openOrder: (id) => ModalOrdenServicio.value?.Open(id),
        openPdf: (id) => pdf.value?.Open(id),
        openInspection: (id) => inspeccionModal.value?.Open(id),
        openWarehouseVouchers: (order) => valesAlmacenModal.value?.Open(order),
    });

const params = computed(() => ({
    search: search.value,
    estatus: estatus.value,
    empresa: empresa.value?.value ?? null,
    modulos: modulos.value,
    talleres: talleres.value,
    usuario_asignado: usuarioAsignado.value?.value ?? null,
    fechas: fechas.value?.length === 2 ? fechas.value.map((date) => date.toISOString()) : null,
    refresh: refreshKey.value,
}));
</script>

<template>
    <AppLayout title="Recepciones Vehiculares" :loading="loading" loading-message="Cargando recepciones vehiculares">
        <template #header>
            <Button text="Nueva" @click="ModalOrdenServicio?.Open(null)" />
        </template>
        <template #filtering>
            <div class="flex flex-wrap flex-row gap-2 items-end">
                    <Search Classdiv="sm:w-[25rem] w-full" placeholder="Buscar Por Order De Servicio, PLacas o Economico" v-model="search" />
                    <EmpresasFilter v-model="empresa" />
                    <EstatusRecepcionesFilter v-model="estatus" />
                    <ModulosOrdenServicioFilter v-model="modulos" />
                    <TalleresFilter v-model="talleres" />
                    <UsuarioAsignadoFilter v-model="usuarioAsignado" />
                    <Datapicker v-model="fechas" />
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
            <Table
                v-if="items.length > 0"
                v-model:OrderKey="orderBy"
                :titles="[
                    { title: 'No. Orden', CanOrder: { key: 'orden', types: 'desc' } },
                    { title: 'No. Seguimiento' },
                    { title: 'Ubicacion' },
                    { title: 'Empresa' },
                    { title: 'Taller' },
                    { title: 'Usuario asignado' },
                    { title: 'Vehiculo', subtittles: [{ title: 'Economico' }, { title: 'Placas' }, { title: 'Marca' }, { title: 'Modelo' }] },
                    {
                        title: 'Fechas',
                        subtittles: [{ title: 'Entrada' }, { title: 'Salida' }],
                    },
                    { title: 'Estatus' },
                    { title: 'Subcontratos' },
                    ...(showActionsColumn ? [{ title: 'Acciones' }] : []),
                    { title: 'opciones' },
                ]"
                :rows="
                    items.map(function (row) {
                        return {
                            classname: 'bg-grey-300',
                            columns: [
                                { element: row.orden, classname: 'capitalize' },
                                { element: row.seguimiento, classname: 'capitalize' },
                                { element: row.ubicacion, classname: 'capitalize' },
                                { element: row.empresa, classname: 'lowercase' },
                                { element: row.taller, classname: 'capitalize' },
                                { element: row.usuario_asignado, classname: 'capitalize' },
                                { element: row.economico, classname: 'uppercase' },
                                { element: row.placas, classname: 'uppercase' },
                                { element: row.marca, classname: 'uppercase' },
                                { element: row.modelo, classname: 'uppercase' },
                                {
                                    element: formatDate(row.datos_entrada.fecha ?? row.creacion),
                                    classname: 'whitespace-nowrap',
                                },
                                {
                                    element: row.salida ? formatDate(row.salida) : 'En taller',
                                    classname: 'whitespace-nowrap',
                                },
                                { element: row.estatus, classname: 'uppercase' },
                                {
                                    element: row.tiene_subcontrato_activo ? 'Activo' : row.subcontratos_count > 0 ? row.subcontratos_count : '-',
                                    classname: 'text-center',
                                },
                                ...(showActionsColumn
                                    ? [
                                          {
                                              element: accionesRecepcion(row).length > 0 ? IconActions : '-',
                                              props: {
                                                  actions: accionesRecepcion(row),
                                              },
                                          },
                                      ]
                                    : []),
                                {
                                    element: Dropdown,
                                    props: {
                                        triggerText: 'Opciones',
                                        options: opcionesRecepcion(row),
                                        contentClasses: {
                                            bg: 'bg-gray-300',
                                        },
                                    },
                                },
                            ],
                        };
                    })
                "
                classname="tabla mt-2"
            >
            </Table>
            <div class="flex flex-1 items-center justify-center" v-else>
                <span class="text-[1.5rem]">{{ message_empty }}</span>
            </div>
        </template>
    </AppLayout>
    <OrdenServicio ref="ModalOrdenServicio" />
    <InspeccionVehicularModal ref="inspeccionModal" />
    <PDFDemo ref="pdf" />
    <ValesAlmacenModal ref="valesAlmacenModal" />
    <CambiarTallerOrdenServicioModal
        :show="actionModal === 'taller'"
        :orden-servicio-id="selectedOrder?.id ?? null"
        :taller-actual="selectedOrder ? { value: selectedOrder.taller_id ?? '', label: selectedOrder.taller } : null"
        @close="closeAction"
        @saved="refreshOrders"
    />
    <CambiarModuloOrdenServicioModal
        :show="actionModal === 'modulo'"
        :orden-servicio-id="selectedOrder?.id ?? null"
        :modulo-actual="selectedOrder ? { value: selectedOrder.modulo_id, label: selectedOrder.modulo } : null"
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
        :usuario-actual="
            selectedOrder?.user_asignado
                ? {
                      value: selectedOrder.user_asignado,
                      label: selectedOrder.usuario_asignado,
                  }
                : null
        "
        @close="closeAction"
        @saved="refreshOrders"
    />
    <EntregaUnidadOrdenServicioModal
        :show="actionModal === 'entrega'"
        :orden-servicio-id="selectedOrder?.id ?? null"
        :orden="selectedOrder?.orden ?? ''"
        :datos-entrada="selectedOrder?.datos_entrada ?? null"
        @close="closeAction"
        @saved="refreshOrders"
    />
</template>
