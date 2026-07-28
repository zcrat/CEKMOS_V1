<script setup lang="ts">
import axios from 'axios'
import { computed, reactive, ref } from 'vue'
import BaseModal from '@/components/Zcrat/modals/BaseModal.vue'
import InspectionPanel from '@/components/Zcrat/modals/partes/inspeccion/InspectionPanel.vue'
import InspectionPoint from '@/components/Zcrat/modals/partes/inspeccion/InspectionPoint.vue'
import InspectionSection from '@/components/Zcrat/modals/partes/inspeccion/InspectionSection.vue'
import MyBasicToast from '@/utils/ToastNotificationBasic'
import type { ConfirmButton } from '@/types/modals'
import {
  createInspeccionVehicularForm,
  inspectionStatuses,
  type InspeccionVehicularForm,
} from '@/types/InspeccionVehicular'

const emit = defineEmits<{
  (event: 'saved', orderId: number): void
}>()

const show = ref(false)
const loading = ref(false)
const pdfLoading = ref(false)
const pdfVersion = ref(0)
const viewMode = ref<'form' | 'pdf'>('form')
const orderId = ref<number | null>(null)
const exists = ref(false)
const validationErrors = ref<Record<string, string[]>>({})
const form = reactive<InspeccionVehicularForm>(createInspeccionVehicularForm())

const title = computed(() => exists.value
  ? 'Editar inspección vehicular'
  : 'Crear inspección vehicular')

const pdfUrl = computed(() => orderId.value === null
  ? ''
  : route('pdf.cortana.inspeccion.vehicular', {
      id: orderId.value,
      version: pdfVersion.value,
    }))

const isIncomplete = computed(() => inspectionStatuses(form).some((status) => status === null))

const confirmButton = computed<ConfirmButton | undefined>(() => viewMode.value === 'form'
  ? {
      text: exists.value ? 'Guardar cambios' : 'Crear inspección',
      className: 'bg-blue-600 text-white',
      disabled: loading.value || orderId.value === null || isIncomplete.value,
      onClick: save,
    }
  : undefined)

function resetForm() {
  Object.assign(form, createInspeccionVehicularForm())
  validationErrors.value = {}
}

async function open(id: number) {
  resetForm()
  orderId.value = id
  exists.value = false
  viewMode.value = 'form'
  pdfLoading.value = false
  show.value = true
  loading.value = true

  try {
    const response = await axios.get(route('inspeccionvehicular.read', { ordenServicio: id }))
    exists.value = response.data.exists

    if (response.data.data) {
      Object.assign(form, response.data.data as InspeccionVehicularForm)
    }
  } catch (error: any) {
    MyBasicToast.error(error.response?.data?.message ?? 'No se pudo cargar la inspección vehicular.')
    show.value = false
    orderId.value = null
  } finally {
    loading.value = false
  }
}

function close() {
  if (loading.value) {
    return
  }

  show.value = false
  orderId.value = null
  exists.value = false
  viewMode.value = 'form'
  pdfLoading.value = false
  validationErrors.value = {}
}

function changeView(mode: 'form' | 'pdf') {
  if (viewMode.value === mode) {
    return
  }

  viewMode.value = mode
  if (mode === 'pdf') {
    pdfLoading.value = true
  }
}

async function save() {
  if (orderId.value === null || isIncomplete.value || loading.value) {
    return
  }

  loading.value = true
  validationErrors.value = {}

  try {
    const response = await axios.post(route('inspeccionvehicular.save'), {
      orden_servicio_id: orderId.value,
      ...form,
    })

    MyBasicToast.success(response.data.message)
    emit('saved', orderId.value)
    exists.value = true
    pdfVersion.value++
    changeView('pdf')
  } catch (error: any) {
    if (error.response?.status === 422) {
      validationErrors.value = error.response.data.errors ?? {}
      MyBasicToast.warning('Revisa los campos marcados de la inspección.')
    } else {
      MyBasicToast.error(error.response?.data?.message ?? 'No se pudo guardar la inspección vehicular.')
    }
  } finally {
    loading.value = false
  }
}

defineExpose({ Open: open })
</script>

<template>
  <BaseModal
    :show="show"
    :loading="loading"
    loading-message="Espera a que termine la operación."
    :modal-title="title"
    modal-description="Captura los resultados de la inspección multipunto."
    :confirm-button="confirmButton"
    z-index-class="z-[999]"
    @close="close"
  >
    <div class="w-[min(1180px,92vw)] space-y-3">
      <nav
        class="flex gap-1.5 rounded-lg border border-slate-300 bg-slate-50 p-1.5"
        aria-label="Vistas de inspección"
      >
        <button
          type="button"
          :class="viewMode === 'form' ? 'bg-[#2f6887] text-white' : 'text-gray-700'"
          class="min-w-32 rounded-md px-4 py-2 font-bold hover:bg-[#2f6887] hover:text-white max-sm:flex-1"
          @click="changeView('form')"
        >
          Captura
        </button>
        <button
          type="button"
          :class="viewMode === 'pdf' ? 'bg-[#2f6887] text-white' : 'text-gray-700'"
          class="min-w-32 rounded-md px-4 py-2 font-bold hover:bg-[#2f6887] hover:text-white max-sm:flex-1"
          @click="changeView('pdf')"
        >
          Vista PDF
        </button>
      </nav>

      <template v-if="viewMode === 'form'">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 rounded-lg border border-gray-400 bg-slate-50 px-3 py-2 text-gray-800">
          <strong>Selecciona un estado para cada punto:</strong>
          <span class="inline-flex items-center gap-1.5 text-sm"><i class="size-5 border border-gray-900 bg-[#ff1111]"></i> Atención inmediata</span>
          <span class="inline-flex items-center gap-1.5 text-sm"><i class="size-5 bg-[#ffc43d] [clip-path:polygon(50%_0,100%_100%,0_100%)]"></i> Atención futura</span>
          <span class="inline-flex items-center gap-1.5 text-sm"><i class="size-5 rounded-full border border-gray-900 bg-[#08b83f]"></i> Buen estado</span>
        </div>

        <div v-if="loading" class="rounded bg-blue-100 px-3 py-2 font-semibold text-blue-900">
          Cargando inspección…
        </div>

        <div v-if="Object.keys(validationErrors).length" class="rounded bg-red-100 px-3 py-2 font-semibold text-red-800">
          Hay campos pendientes o con valores inválidos. Revisa las secciones antes de guardar.
        </div>

        <InspectionPanel title="26 puntos - Inspección de vehículo" tone="orange">
            <div class="flex min-w-0 flex-col self-start gap-3">
              <InspectionSection
                v-model:notes="form.luces_espias.notas"
                title="Revisión de luces espías"
                show-notes
                :disabled="loading"
              >
                <InspectionPoint v-model:status="form.luces_espias.codigo" label="Código(s)" :disabled="loading" />
              </InspectionSection>

              <InspectionSection
                v-model:notes="form.seguridad.notas"
                title="Seguridad y limpiaparabrisas"
                show-notes
                :disabled="loading"
              >
                <InspectionPoint v-model:status="form.seguridad.frenos_emergencia" label="Freno de emergencia" :disabled="loading" />
                <InspectionPoint v-model:status="form.seguridad.limpiaparabrisas_izquierdo_derecho" label="Limpiaparabrisas izq./der." :disabled="loading" />
                <InspectionPoint v-model:status="form.seguridad.limpiaparabrisas_trasero" label="Limpiaparabrisas trasero" :disabled="loading" />
              </InspectionSection>
            </div>

            <InspectionSection
              v-model:notes="form.liquidos.notas"
              title="Líquidos"
              show-notes
              :disabled="loading"
            >
              <InspectionPoint v-model:status="form.liquidos.alternador_aire_acondicionado" v-model:ok="form.liquidos.alternador_aire_acondicionado_ok" v-model:lleno="form.liquidos.alternador_aire_acondicionado_lleno" label="Aceite de motor" show-checks :disabled="loading" />
              <InspectionPoint v-model:status="form.liquidos.transmision" v-model:ok="form.liquidos.transmision_ok" v-model:lleno="form.liquidos.transmision_lleno" label="Transmisión" show-checks :disabled="loading" />
              <InspectionPoint v-model:status="form.liquidos.diferencial_frente_trasero" v-model:ok="form.liquidos.diferencial_frente_trasero_ok" v-model:lleno="form.liquidos.diferencial_frente_trasero_lleno" label="Diferencial frente/trasero" show-checks :disabled="loading" />
              <InspectionPoint v-model:status="form.liquidos.refrigerante" v-model:ok="form.liquidos.refrigerante_ok" v-model:lleno="form.liquidos.refrigerante_lleno" label="Refrigerante" show-checks :disabled="loading" />
              <InspectionPoint v-model:status="form.liquidos.frenos" v-model:ok="form.liquidos.frenos_ok" v-model:lleno="form.liquidos.frenos_lleno" label="Frenos" show-checks :disabled="loading" />
              <InspectionPoint v-model:status="form.liquidos.direccion_hidraulica" v-model:ok="form.liquidos.direccion_hidraulica_ok" v-model:lleno="form.liquidos.direccion_hidraulica_lleno" label="Dirección hidráulica" show-checks :disabled="loading" />
              <InspectionPoint v-model:status="form.liquidos.limpiaparabrisas" v-model:ok="form.liquidos.limpiaparabrisas_ok" v-model:lleno="form.liquidos.limpiaparabrisas_lleno" label="Limpiaparabrisas" show-checks :disabled="loading" />
            </InspectionSection>

            <InspectionSection title="Mangueras">
              <InspectionPoint v-model:status="form.mangueras.refrigerante" label="Refrigerante" :disabled="loading" />
              <InspectionPoint v-model:status="form.mangueras.direccion_aire_acondicionado" label="Dirección/Aire acondicionado" :disabled="loading" />
              <InspectionPoint v-model:status="form.mangueras.calefaccion" label="Calefacción" :disabled="loading" />
            </InspectionSection>

            <InspectionSection title="Bandas">
              <InspectionPoint v-model:status="form.bandas.accesorios" label="Accesorios" :disabled="loading" />
              <InspectionPoint v-model:status="form.bandas.bandas_direccion_hidraulica" label="Dirección hidráulica" :disabled="loading" />
              <InspectionPoint v-model:status="form.bandas.alternador_aire_acondicionado" label="Alternador/Aire acondicionado" :disabled="loading" />
            </InspectionSection>

            <InspectionSection
              v-model:notes="form.filtros.notas"
              title="Filtros"
              show-notes
              :disabled="loading"
            >
              <InspectionPoint v-model:status="form.filtros.aire" label="Aire" :disabled="loading" />
              <InspectionPoint v-model:status="form.filtros.combustible" label="Combustible" :disabled="loading" />
              <InspectionPoint v-model:status="form.filtros.aceite" label="Aceite" :disabled="loading" />
            </InspectionSection>

            <InspectionSection title="Llantas">
              <InspectionPoint v-model:status="form.llantas.izquierda_delantera" v-model:pressure="form.llantas.izquierda_delantera_presion" label="Izquierda delantera" show-pressure :disabled="loading" />
              <InspectionPoint v-model:status="form.llantas.izquierda_trasera" v-model:pressure="form.llantas.izquierda_trasera_presion" label="Izquierda trasera" show-pressure :disabled="loading" />
              <InspectionPoint v-model:status="form.llantas.derecha_delantera" v-model:pressure="form.llantas.derecha_delantera_presion" label="Derecha delantera" show-pressure :disabled="loading" />
              <InspectionPoint v-model:status="form.llantas.derecha_trasera" v-model:pressure="form.llantas.derecha_trasera_presion" label="Derecha trasera" show-pressure :disabled="loading" />
              <InspectionPoint v-model:status="form.llantas.refaccion" v-model:pressure="form.llantas.refaccion_presion" label="Refacción" show-pressure :disabled="loading" />
              <InspectionPoint v-model:status="form.llantas.alineacion_balanceo" label="Alineación y balanceo" :disabled="loading" />
            </InspectionSection>
        </InspectionPanel>

        <InspectionPanel title="57 puntos - Inspección de vehículo" tone="purple">
            <div class="flex min-w-0 flex-col self-start gap-3">
              <InspectionSection
                v-model:notes="form.suspencion_direccion.notas"
                title="Suspensión/Dirección"
                show-notes
                :disabled="loading"
              >
                <InspectionPoint v-model:status="form.suspencion_direccion.amortiguadores_suspencion" label="Amortiguadores/Suspensión" :disabled="loading" />
                <InspectionPoint v-model:status="form.suspencion_direccion.juntas_direccion_rotulas" label="Juntas de dirección/Rótulas" :disabled="loading" />
              </InspectionSection>

              <InspectionSection title="Afinación de motor">
                <InspectionPoint v-model:status="form.afinacion_motor.tapa_distribuidor_bujias_cables" label="Tapa distribuidor/Bujías/Cables" :disabled="loading" />
                <InspectionPoint v-model:status="form.afinacion_motor.fuel_injection" label="Fuel injection" :disabled="loading" />
              </InspectionSection>

              <InspectionSection
                v-model:notes="form.escape.notas"
                title="Escape"
                show-notes
                :disabled="loading"
              >
                <InspectionPoint v-model:status="form.escape.mofle_convertidor_catlitico" label="Mofle/Convertidor catalítico" :disabled="loading" />
                <InspectionPoint v-model:status="form.escape.sensores_soporte_tubos" label="Sensores/Soportes/Tubos" :disabled="loading" />
              </InspectionSection>
            </div>

            <InspectionSection
              v-model:notes="form.tren_transmision.notas"
              title="Tren de transmisión"
              show-notes
              :disabled="loading"
            >
              <InspectionPoint v-model:status="form.tren_transmision.filtro_transmison" label="Filtro de transmisión" :disabled="loading" />
              <InspectionPoint v-model:status="form.tren_transmision.union_transmision_clutch" label="Unión de transmisión/Clutch" :disabled="loading" />
              <InspectionPoint v-model:status="form.tren_transmision.eje_traccion_juntas_homocineticas" label="Eje de tracción y juntas homocinéticas" :disabled="loading" />
              <InspectionPoint v-model:status="form.tren_transmision.eje_transmision_juntas_universales" label="Eje de transmisión y juntas universales" :disabled="loading" />
              <InspectionPoint v-model:status="form.tren_transmision.rodamientos_rueda" label="Rodamientos de rueda" :disabled="loading" />
              <InspectionPoint v-model:status="form.tren_transmision.tren_transmision" label="Transmisión" :disabled="loading" />
              <InspectionPoint v-model:status="form.tren_transmision.clutch" label="Clutch" :disabled="loading" />
            </InspectionSection>

            <InspectionSection title="Eléctrico y luces">
              <InspectionPoint v-model:status="form.electrico.sistema_carga_bateria" label="Sistema de carga/Batería" :disabled="loading" />
              <InspectionPoint v-model:status="form.electrico.cables_conexiones_fusibles" label="Cables/Conexiones/Fusibles" :disabled="loading" />
              <InspectionPoint v-model:status="form.electrico.reversa_frenos" label="Luces de freno/Reversa" :disabled="loading" />
              <InspectionPoint v-model:status="form.electrico.intermitentes" label="Intermitentes" :disabled="loading" />
              <InspectionPoint v-model:status="form.electrico.faro_izquierda" label="Faro izquierdo" :disabled="loading" />
              <InspectionPoint v-model:status="form.electrico.faro_derecha" label="Faro derecho" :disabled="loading" />
              <InspectionPoint v-model:status="form.electrico.cuarto_izquierda" label="Cuarto izquierdo" :disabled="loading" />
              <InspectionPoint v-model:status="form.electrico.cuarto_derecha" label="Cuarto derecho" :disabled="loading" />
              <InspectionPoint v-model:status="form.electrico.direccionales_izquierda_delantera" label="Direccional izquierda delantera" :disabled="loading" />
              <InspectionPoint v-model:status="form.electrico.direccionales_derecha_delantera" label="Direccional derecha delantera" :disabled="loading" />
              <InspectionPoint v-model:status="form.electrico.direccionales_izquierda_trasera" label="Direccional izquierda trasera" :disabled="loading" />
              <InspectionPoint v-model:status="form.electrico.direccionales_derecha_trasera" label="Direccional derecha trasera" :disabled="loading" />
            </InspectionSection>

            <InspectionSection title="Frenos">
              <InspectionPoint v-model:status="form.frenos.pastillas_izquierda_delantera" label="Pastilla izquierda delantera" :disabled="loading" />
              <InspectionPoint v-model:status="form.frenos.pastillas_derecha_delantera" label="Pastilla derecha delantera" :disabled="loading" />
              <InspectionPoint v-model:status="form.frenos.pastillas_izquierda_trasera" label="Pastilla izquierda trasera" :disabled="loading" />
              <InspectionPoint v-model:status="form.frenos.pastillas_derecha_trasera" label="Pastilla derecha trasera" :disabled="loading" />
              <InspectionPoint v-model:status="form.frenos.rotores_izquierda_delantera" label="Rotor izquierdo delantero" :disabled="loading" />
              <InspectionPoint v-model:status="form.frenos.rotores_derecha_delantera" label="Rotor derecho delantero" :disabled="loading" />
              <InspectionPoint v-model:status="form.frenos.rotores_izquierda_trasera" label="Rotor izquierdo trasero" :disabled="loading" />
              <InspectionPoint v-model:status="form.frenos.rotores_derecha_trasera" label="Rotor derecho trasero" :disabled="loading" />
              <InspectionPoint v-model:status="form.frenos.pinzas_cilindros_rueda_izquierda_delantera" label="Pinza/cilindro izquierdo delantero" :disabled="loading" />
              <InspectionPoint v-model:status="form.frenos.pinzas_cilindros_rueda_derecha_delantera" label="Pinza/cilindro derecho delantero" :disabled="loading" />
              <InspectionPoint v-model:status="form.frenos.pinzas_cilindros_rueda_izquierda_trasera" label="Pinza/cilindro izquierdo trasero" :disabled="loading" />
              <InspectionPoint v-model:status="form.frenos.pinzas_cilindros_rueda_derecha_trasera" label="Pinza/cilindro derecho trasero" :disabled="loading" />
            </InspectionSection>
        </InspectionPanel>
      </template>

      <div v-else class="relative h-[72vh] min-h-[520px] overflow-hidden rounded-lg border border-slate-400 bg-slate-200">
        <div v-if="pdfLoading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/90 font-bold text-slate-700">
          Generando vista PDF…
        </div>
        <iframe
          v-if="orderId !== null"
          :key="pdfUrl"
          :src="pdfUrl"
          title="Vista PDF de inspección vehicular"
          class="block h-full w-full border-0 bg-white"
          @load="pdfLoading = false"
        />
      </div>
    </div>
  </BaseModal>
</template>
