import axios from 'axios';
import { responseBasic } from '@/types/generales';
import { OrdenServicioForm } from '@/types/OrdenServicio';

export const Create = async (data: OrdenServicioForm): Promise<responseBasic> => {
  try {
    const formData = new FormData();
    // 🔹 simples
    formData.append('orden_seguimiento', data.orden_seguimiento);
    formData.append('orden_opcional', data.orden_opcional);
    formData.append('ubicacion', data.ubicacion);
    formData.append('tipo_presupuesto_id', String(data.tipo_presupuesto_id));
    formData.append('modulo_orden_id', String(data.modulo_orden_id));
    formData.append('vehiculo_concepto_id', String(data.vehiculo_concepto_id ?? ''));
    formData.append('empresa_id', String(data.empresa_id ?? ''));
    formData.append('cliente_id', String(data.cliente_id ?? ''));
    formData.append('vehiculo_id', String(data.vehiculo_id ?? ''));
    formData.append('telefono', String(data.telefono));
    formData.append('estimacion', data.estimacion.toISOString());
    formData.append('kilometraje', String(data.kilometraje));
    formData.append('gasolina', String(data.gasolina));
    formData.append('administrador', data.administrador);
    formData.append('jefe', data.jefe);
    formData.append('trabajador', data.trabajador);
    formData.append('tecnico', data.tecnico);
    formData.append('indicaciones_cliente', data.indicaciones_cliente);
    formData.append('descripcion_mo', data.descripcion_mo);
    formData.append('garantia', data.garantia);
    formData.append('observaciones', data.observaciones);

    // 🔹 objetos (Laravel los entiende así)
    Object.entries(data.inventario).forEach(([k, v]) => {
      if (v != null) formData.append(`inventario[${k}]`, String(v));
    });

    Object.entries(data.pintura).forEach(([k, v]) => {
      if (v != null) formData.append(`pintura[${k}]`, String(v));
    });

    Object.entries(data.condiciones_interiores).forEach(([k, v]) => {
      if (v != null) formData.append(`condiciones_interiores[${k}]`, String(v));
    });

    Object.entries(data.condiciones_exteriores).forEach(([k, v]) => {
      if (v != null) formData.append(`condiciones_exteriores[${k}]`, String(v));
    });

    // 🔥 archivos múltiples (SIEMPRE [] sin índice)
    data.imagenes_evidencia.forEach((file, index) => {
      formData.append(
        'imagenes_evidencia[]',
        file
      );
    });
    if (data.carro) {
      formData.append(
        'carro',
        data.carro
      );
    }
    if (data.firma) {
      formData.append(
        'firma',
        data.firma,
      );
    }
    const response = await axios.post(
      route('Cortana.OrdenServicio.Create'),
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    );

    return { status: true, code: 200, data: response.data };

  } catch (error: any) {
    const status = error.response?.status ?? 0;
    const data = error.response?.data ?? 'Error inesperado';

    if (status === 422 && data.errors) {
      return {
        status: false,
        code: 422,
        data: {
          message: 'Error de validación',
        },
        validationErrors: data.errors,
      };
    }

    return { status: false, code: status, data };
  }
};