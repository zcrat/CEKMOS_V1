export type InspectionStatus = 23 | 24 | 25 | null

export interface LucesEspiasForm {
  codigo: InspectionStatus
  notas: string
}

export interface LiquidosForm {
  alternador_aire_acondicionado: InspectionStatus
  alternador_aire_acondicionado_ok: boolean
  alternador_aire_acondicionado_lleno: boolean
  transmision: InspectionStatus
  transmision_ok: boolean
  transmision_lleno: boolean
  diferencial_frente_trasero: InspectionStatus
  diferencial_frente_trasero_ok: boolean
  diferencial_frente_trasero_lleno: boolean
  refrigerante: InspectionStatus
  refrigerante_ok: boolean
  refrigerante_lleno: boolean
  frenos: InspectionStatus
  frenos_ok: boolean
  frenos_lleno: boolean
  direccion_hidraulica: InspectionStatus
  direccion_hidraulica_ok: boolean
  direccion_hidraulica_lleno: boolean
  limpiaparabrisas: InspectionStatus
  limpiaparabrisas_ok: boolean
  limpiaparabrisas_lleno: boolean
  notas: string
}

export interface ManguerasForm {
  refrigerante: InspectionStatus
  direccion_aire_acondicionado: InspectionStatus
  calefaccion: InspectionStatus
}

export interface BandasForm {
  accesorios: InspectionStatus
  bandas_direccion_hidraulica: InspectionStatus
  alternador_aire_acondicionado: InspectionStatus
}

export interface FiltrosForm {
  aire: InspectionStatus
  combustible: InspectionStatus
  aceite: InspectionStatus
  notas: string
}

export interface LlantasForm {
  izquierda_delantera: InspectionStatus
  izquierda_delantera_presion: number
  izquierda_trasera: InspectionStatus
  izquierda_trasera_presion: number
  derecha_delantera: InspectionStatus
  derecha_delantera_presion: number
  derecha_trasera: InspectionStatus
  derecha_trasera_presion: number
  refaccion: InspectionStatus
  refaccion_presion: number
  alineacion_balanceo: InspectionStatus
}

export interface SeguridadForm {
  frenos_emergencia: InspectionStatus
  limpiaparabrisas_izquierdo_derecho: InspectionStatus
  limpiaparabrisas_trasero: InspectionStatus
  notas: string
}

export interface SuspencionDireccionForm {
  amortiguadores_suspencion: InspectionStatus
  juntas_direccion_rotulas: InspectionStatus
  notas: string
}

export interface TrenTransmisionForm {
  filtro_transmison: InspectionStatus
  union_transmision_clutch: InspectionStatus
  eje_traccion_juntas_homocineticas: InspectionStatus
  eje_transmision_juntas_universales: InspectionStatus
  rodamientos_rueda: InspectionStatus
  tren_transmision: InspectionStatus
  clutch: InspectionStatus
  notas: string
}

export interface ElectricoForm {
  sistema_carga_bateria: InspectionStatus
  cables_conexiones_fusibles: InspectionStatus
  faro_izquierda: InspectionStatus
  faro_derecha: InspectionStatus
  cuarto_izquierda: InspectionStatus
  cuarto_derecha: InspectionStatus
  reversa_frenos: InspectionStatus
  direccionales_izquierda_delantera: InspectionStatus
  direccionales_derecha_delantera: InspectionStatus
  direccionales_izquierda_trasera: InspectionStatus
  direccionales_derecha_trasera: InspectionStatus
  intermitentes: InspectionStatus
}

export interface AfinacionMotorForm {
  tapa_distribuidor_bujias_cables: InspectionStatus
  fuel_injection: InspectionStatus
}

export interface FrenosForm {
  pastillas_izquierda_delantera: InspectionStatus
  pastillas_izquierda_trasera: InspectionStatus
  pastillas_derecha_delantera: InspectionStatus
  pastillas_derecha_trasera: InspectionStatus
  rotores_izquierda_delantera: InspectionStatus
  rotores_izquierda_trasera: InspectionStatus
  rotores_derecha_delantera: InspectionStatus
  rotores_derecha_trasera: InspectionStatus
  pinzas_cilindros_rueda_izquierda_delantera: InspectionStatus
  pinzas_cilindros_rueda_izquierda_trasera: InspectionStatus
  pinzas_cilindros_rueda_derecha_delantera: InspectionStatus
  pinzas_cilindros_rueda_derecha_trasera: InspectionStatus
}

export interface EscapeForm {
  mofle_convertidor_catlitico: InspectionStatus
  sensores_soporte_tubos: InspectionStatus
  notas: string
}

export interface InspeccionVehicularForm {
  luces_espias: LucesEspiasForm
  liquidos: LiquidosForm
  mangueras: ManguerasForm
  bandas: BandasForm
  filtros: FiltrosForm
  llantas: LlantasForm
  seguridad: SeguridadForm
  suspencion_direccion: SuspencionDireccionForm
  tren_transmision: TrenTransmisionForm
  electrico: ElectricoForm
  afinacion_motor: AfinacionMotorForm
  frenos: FrenosForm
  escape: EscapeForm
}

export function createInspeccionVehicularForm(): InspeccionVehicularForm {
  return {
    luces_espias: {
      codigo: null,
      notas: '',
    },
    liquidos: {
      alternador_aire_acondicionado: null,
      alternador_aire_acondicionado_ok: false,
      alternador_aire_acondicionado_lleno: false,
      transmision: null,
      transmision_ok: false,
      transmision_lleno: false,
      diferencial_frente_trasero: null,
      diferencial_frente_trasero_ok: false,
      diferencial_frente_trasero_lleno: false,
      refrigerante: null,
      refrigerante_ok: false,
      refrigerante_lleno: false,
      frenos: null,
      frenos_ok: false,
      frenos_lleno: false,
      direccion_hidraulica: null,
      direccion_hidraulica_ok: false,
      direccion_hidraulica_lleno: false,
      limpiaparabrisas: null,
      limpiaparabrisas_ok: false,
      limpiaparabrisas_lleno: false,
      notas: '',
    },
    mangueras: {
      refrigerante: null,
      direccion_aire_acondicionado: null,
      calefaccion: null,
    },
    bandas: {
      accesorios: null,
      bandas_direccion_hidraulica: null,
      alternador_aire_acondicionado: null,
    },
    filtros: {
      aire: null,
      combustible: null,
      aceite: null,
      notas: '',
    },
    llantas: {
      izquierda_delantera: null,
      izquierda_delantera_presion: 0,
      izquierda_trasera: null,
      izquierda_trasera_presion: 0,
      derecha_delantera: null,
      derecha_delantera_presion: 0,
      derecha_trasera: null,
      derecha_trasera_presion: 0,
      refaccion: null,
      refaccion_presion: 0,
      alineacion_balanceo: null,
    },
    seguridad: {
      frenos_emergencia: null,
      limpiaparabrisas_izquierdo_derecho: null,
      limpiaparabrisas_trasero: null,
      notas: '',
    },
    suspencion_direccion: {
      amortiguadores_suspencion: null,
      juntas_direccion_rotulas: null,
      notas: '',
    },
    tren_transmision: {
      filtro_transmison: null,
      union_transmision_clutch: null,
      eje_traccion_juntas_homocineticas: null,
      eje_transmision_juntas_universales: null,
      rodamientos_rueda: null,
      tren_transmision: null,
      clutch: null,
      notas: '',
    },
    electrico: {
      sistema_carga_bateria: null,
      cables_conexiones_fusibles: null,
      faro_izquierda: null,
      faro_derecha: null,
      cuarto_izquierda: null,
      cuarto_derecha: null,
      reversa_frenos: null,
      direccionales_izquierda_delantera: null,
      direccionales_derecha_delantera: null,
      direccionales_izquierda_trasera: null,
      direccionales_derecha_trasera: null,
      intermitentes: null,
    },
    afinacion_motor: {
      tapa_distribuidor_bujias_cables: null,
      fuel_injection: null,
    },
    frenos: {
      pastillas_izquierda_delantera: null,
      pastillas_izquierda_trasera: null,
      pastillas_derecha_delantera: null,
      pastillas_derecha_trasera: null,
      rotores_izquierda_delantera: null,
      rotores_izquierda_trasera: null,
      rotores_derecha_delantera: null,
      rotores_derecha_trasera: null,
      pinzas_cilindros_rueda_izquierda_delantera: null,
      pinzas_cilindros_rueda_izquierda_trasera: null,
      pinzas_cilindros_rueda_derecha_delantera: null,
      pinzas_cilindros_rueda_derecha_trasera: null,
    },
    escape: {
      mofle_convertidor_catlitico: null,
      sensores_soporte_tubos: null,
      notas: '',
    },
  }
}

export function inspectionStatuses(form: InspeccionVehicularForm): InspectionStatus[] {
  return [
    form.luces_espias.codigo,
    form.liquidos.alternador_aire_acondicionado,
    form.liquidos.transmision,
    form.liquidos.diferencial_frente_trasero,
    form.liquidos.refrigerante,
    form.liquidos.frenos,
    form.liquidos.direccion_hidraulica,
    form.liquidos.limpiaparabrisas,
    form.mangueras.refrigerante,
    form.mangueras.direccion_aire_acondicionado,
    form.mangueras.calefaccion,
    form.bandas.accesorios,
    form.bandas.bandas_direccion_hidraulica,
    form.bandas.alternador_aire_acondicionado,
    form.filtros.aire,
    form.filtros.combustible,
    form.filtros.aceite,
    form.llantas.izquierda_delantera,
    form.llantas.izquierda_trasera,
    form.llantas.derecha_delantera,
    form.llantas.derecha_trasera,
    form.llantas.refaccion,
    form.llantas.alineacion_balanceo,
    form.seguridad.frenos_emergencia,
    form.seguridad.limpiaparabrisas_izquierdo_derecho,
    form.seguridad.limpiaparabrisas_trasero,
    form.suspencion_direccion.amortiguadores_suspencion,
    form.suspencion_direccion.juntas_direccion_rotulas,
    form.tren_transmision.filtro_transmison,
    form.tren_transmision.union_transmision_clutch,
    form.tren_transmision.eje_traccion_juntas_homocineticas,
    form.tren_transmision.eje_transmision_juntas_universales,
    form.tren_transmision.rodamientos_rueda,
    form.tren_transmision.tren_transmision,
    form.tren_transmision.clutch,
    form.electrico.sistema_carga_bateria,
    form.electrico.cables_conexiones_fusibles,
    form.electrico.faro_izquierda,
    form.electrico.faro_derecha,
    form.electrico.cuarto_izquierda,
    form.electrico.cuarto_derecha,
    form.electrico.reversa_frenos,
    form.electrico.direccionales_izquierda_delantera,
    form.electrico.direccionales_derecha_delantera,
    form.electrico.direccionales_izquierda_trasera,
    form.electrico.direccionales_derecha_trasera,
    form.electrico.intermitentes,
    form.afinacion_motor.tapa_distribuidor_bujias_cables,
    form.afinacion_motor.fuel_injection,
    form.frenos.pastillas_izquierda_delantera,
    form.frenos.pastillas_izquierda_trasera,
    form.frenos.pastillas_derecha_delantera,
    form.frenos.pastillas_derecha_trasera,
    form.frenos.rotores_izquierda_delantera,
    form.frenos.rotores_izquierda_trasera,
    form.frenos.rotores_derecha_delantera,
    form.frenos.rotores_derecha_trasera,
    form.frenos.pinzas_cilindros_rueda_izquierda_delantera,
    form.frenos.pinzas_cilindros_rueda_izquierda_trasera,
    form.frenos.pinzas_cilindros_rueda_derecha_delantera,
    form.frenos.pinzas_cilindros_rueda_derecha_trasera,
    form.escape.mofle_convertidor_catlitico,
    form.escape.sensores_soporte_tubos,
  ]
}
