<script setup lang="ts">
import MyBasicToast from '@/utils/ToastNotificationBasic';
import { ZdAlert } from '@/utils/ZdAlert';
import { usePage } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import axios from 'axios';
import { onMounted, onUnmounted, ref } from 'vue';

interface ImportProgress {
    archivo_id: number;
    estatus: string;
    total_filas: number;
    procesadas: number;
    importadas: number;
    con_error: number;
    porcentaje: number;
}

interface ArchivoSistema {
    id: number;
    nombre_archivo: string;
    tipo_archivo: string;
    usuario: string;
    estatus_resultante: string;
    conceptos: number;
    fecha: string;
    puede_eliminar: boolean;
    tiene_resultado: boolean;
    progreso: ImportProgress;
}

const emit = defineEmits<{
    (event: 'updated'): void;
}>();

const page = usePage();
const userId = Number((page.props.auth as { user?: { id?: number } })?.user?.id ?? 0);
const loading = ref(true);
const deletingId = ref<number | null>(null);
const importaciones = ref<ArchivoSistema[]>([]);
let pollTimer: ReturnType<typeof setTimeout> | null = null;

const activeStatuses = ['pendiente', 'procesando'];

const clearPolling = () => {
    if (pollTimer) clearTimeout(pollTimer);
    pollTimer = null;
};

const schedulePolling = () => {
    clearPolling();
    if (!importaciones.value.some((item) => activeStatuses.includes(item.estatus_resultante))) return;
    pollTimer = setTimeout(() => refresh(), 3000);
};

const refresh = async () => {
    loading.value = true;

    try {
        const response = await axios.get(route('catalogos.conceptos.importaciones'));
        importaciones.value = response.data.items ?? [];
    } catch {
        MyBasicToast.error('No fue posible cargar el historial de importaciones');
    } finally {
        loading.value = false;
        schedulePolling();
    }
};

const deleteImportacion = async (archivo: ArchivoSistema) => {
    const confirmed = await ZdAlert({
        title: 'Eliminar conceptos importados',
        text: `¿Deseas eliminar los ${archivo.conceptos} conceptos importados desde "${archivo.nombre_archivo}"? El registro del archivo se conservará.`,
        confirmButtonText: 'Eliminar conceptos',
    });

    if (!confirmed) return;
    deletingId.value = archivo.id;

    try {
        const response = await axios.delete(route('catalogos.conceptos.importaciones.destroy', archivo.id));
        MyBasicToast.success(response.data.message);
        await refresh();
        emit('updated');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            const messages = Object.values(error.response.data.errors ?? {}).flat().join(' ');
            MyBasicToast.error(messages || 'No se pueden eliminar los conceptos de esta importación');
        } else {
            MyBasicToast.error('No fue posible eliminar los conceptos importados');
        }
    } finally {
        deletingId.value = null;
    }
};

const downloadResult = (archivo: ArchivoSistema) => {
    window.location.href = route('catalogos.conceptos.importaciones.resultado', archivo.id);
};

const statusClass = (status: string) => {
    if (status === 'completado') return 'bg-green-100 text-green-800';
    if (status === 'error' || status === 'fallido') return 'bg-red-100 text-red-800';
    if (status === 'eliminado') return 'bg-gray-200 text-gray-700';

    return 'bg-yellow-100 text-yellow-800';
};

const statusLabel = (status: string) => status.replaceAll('_', ' ');

const progressWidthClass = (percentage: number) => {
    if (percentage >= 100) return 'w-full';
    if (percentage >= 90) return 'w-11/12';
    if (percentage >= 75) return 'w-3/4';
    if (percentage >= 66) return 'w-2/3';
    if (percentage >= 50) return 'w-1/2';
    if (percentage >= 33) return 'w-1/3';
    if (percentage >= 25) return 'w-1/4';
    if (percentage > 0) return 'w-1/12';

    return 'w-0';
};

if (userId > 0) {
    useEcho(`importaciones.conceptos.${userId}`, '.progreso', (progress: ImportProgress) => {
        const archivo = importaciones.value.find((item) => item.id === progress.archivo_id);
        if (archivo) {
            archivo.progreso = progress;
            archivo.estatus_resultante = progress.estatus;
            archivo.conceptos = progress.importadas;
        }

        if (!activeStatuses.includes(progress.estatus)) {
            refresh();
            emit('updated');
        }
    });
}

onMounted(refresh);
onUnmounted(clearPolling);
defineExpose({ refresh });
</script>

<template>
    <section class="flex min-h-0 flex-1 flex-col rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-4 py-3">
            <div>
                <h2 class="text-lg font-semibold">Historial de archivos</h2>
                <p class="text-sm text-gray-600">Consulta el avance, resultado y conceptos generados por cada archivo.</p>
            </div>
            <button
                type="button"
                class="flex h-10 items-center gap-2 rounded-lg border border-gray-300 px-4 text-sm hover:bg-gray-50 disabled:opacity-50"
                :disabled="loading"
                @click="refresh"
            >
                <font-awesome-icon icon="fa-solid fa-rotate" />
                Actualizar
            </button>
        </div>

        <div class="min-h-0 flex-1 overflow-auto">
            <table class="w-full min-w-[58rem] text-left text-sm">
                <thead class="sticky top-0 bg-gray-100">
                    <tr>
                        <th class="px-4 py-3">Archivo</th>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Estatus</th>
                        <th class="px-4 py-3">Procesamiento</th>
                        <th class="px-4 py-3 text-center">Conceptos</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading && importaciones.length === 0">
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">Cargando importaciones...</td>
                    </tr>
                    <tr v-else-if="importaciones.length === 0">
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">No hay importaciones registradas</td>
                    </tr>
                    <template v-else>
                        <tr v-for="archivo in importaciones" :key="archivo.id" class="border-t border-gray-200">
                            <td class="max-w-72 px-4 py-3">
                                <p class="truncate font-medium" :title="archivo.nombre_archivo">{{ archivo.nombre_archivo }}</p>
                                <p class="text-xs uppercase text-gray-500">{{ archivo.tipo_archivo }}</p>
                            </td>
                            <td class="px-4 py-3">{{ archivo.usuario || 'Sin usuario' }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ archivo.fecha }}</td>
                            <td class="px-4 py-3">
                                <span :class="['rounded-full px-2 py-1 text-xs font-semibold capitalize', statusClass(archivo.estatus_resultante)]">
                                    {{ statusLabel(archivo.estatus_resultante) }}
                                </span>
                            </td>
                            <td class="min-w-52 px-4 py-3">
                                <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                                    <div
                                        :class="[
                                            'h-full rounded-full bg-blue-600 transition-all',
                                            progressWidthClass(archivo.progreso.porcentaje),
                                        ]"
                                    ></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ archivo.progreso.procesadas }}/{{ archivo.progreso.total_filas }} filas ·
                                    {{ archivo.progreso.importadas }} importadas · {{ archivo.progreso.con_error }} con error
                                </p>
                            </td>
                            <td class="px-4 py-3 text-center">{{ archivo.conceptos }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-3">
                                    <button
                                        v-if="archivo.tiene_resultado"
                                        type="button"
                                        class="whitespace-nowrap text-blue-700 hover:text-blue-900"
                                        @click="downloadResult(archivo)"
                                    >
                                        Resultado
                                    </button>
                                    <button
                                        v-if="archivo.puede_eliminar"
                                        type="button"
                                        class="whitespace-nowrap text-red-700 hover:text-red-900 disabled:opacity-50"
                                        :disabled="deletingId !== null"
                                        @click="deleteImportacion(archivo)"
                                    >
                                        {{ deletingId === archivo.id ? 'Eliminando...' : 'Eliminar conceptos' }}
                                    </button>
                                    <span v-if="!archivo.tiene_resultado && !archivo.puede_eliminar" class="text-gray-400">—</span>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </section>
</template>
