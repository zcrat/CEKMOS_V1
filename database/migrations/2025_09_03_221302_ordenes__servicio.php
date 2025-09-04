<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('categorias', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
        $table->timestamps();
    });
    Schema::create('colores', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
        $table->timestamps();
    });
    Schema::create('marcas', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
        $table->timestamps();
    });
    Schema::create('modelos', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
        $table->foreignId('marca_id')->constrained('marcas');
        $table->timestamps();
    });
    Schema::create('tipos', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
        $table->foreignId('categoria_id')->constrained('categorias');
        $table->timestamps();
    });
    Schema::create('estatus', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
        $table->foreignId('categoria_id')->constrained('categorias');
        $table->timestamps();
    });
    Schema::create('vehiculos', function (Blueprint $table) {
        $table->id();
        $table->string('placas'); 
        $table->year('año'); 
        $table->string('economomico'); 
        $table->string('vin'); 
        $table->foreignId('tipo_id')->constrained('tipos');
        $table->foreignId('color_id')->constrained('colores');
        $table->foreignId('modelo_id')->constrained('modelos');
        $table->timestamps();
    });
    Schema::create('niveles_combustible', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion'); 
        $table->timestamps();
    });
    Schema::create('vehiculos_conceptos', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion'); 
        $table->timestamps();
    });

    Schema::create('tipos_imagenes', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion'); 
        $table->text('ruta'); 
        $table->timestamps();
    });
   
    Schema::create('emisor', function (Blueprint $table) {
        $table->id();
        $table->string('n_certificado');
        $table->string('archivo_cer');
        $table->string('archivo_key');
        $table->string('clave_key');
        $table->string('rfc_emisor');
        $table->string('nombre_emisor');
        $table->string('logotipo_emisor');
        $table->string('regimen_emisor');
        $table->string('codigo_emisor');
        $table->string('serie_factura');
        $table->string('serie_p_factura');
        $table->timestamps();
    });
    Schema::create('modulos', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
        $table->foreignId('emisor_id')->constrained('emisor');
        $table->timestamps();
    });
    Schema::create('contratos', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
        $table->integer('numero');
        $table->decimal('monto',10,2);
        $table->timestamps();
    });
    Schema::create('zonas', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
        $table->timestamps();
    });
    Schema::create('modulos_ordenes_servicio', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
        $table->string('clave');
        $table->foreignId('modulo_id')->constrained('modulos');
        $table->foreignId('contrato_id')->constrained('contratos');
        $table->foreignId('zona_id')->constrained('zonas');
        $table->year('anio');
        $table->timestamps();
    });
   
    Schema::create('ordenes_servicio', function (Blueprint $table) {
        $table->id();
        $table->string('orden_servicio')->unique();
        $table->string('orden_seguimiento')->unique();
        $table->foreignId('modulo_orden')->constrained('modulos_ordenes_servicio');
        $table->foreignId('vehiculo_id')->constrained('vehiculos');
        $table->foreignId('tipo_vehiculo_concepto_id')->constrained('vehiculos_conceptos');
        $table->foreignId('user_id')->constrained('users');  
        $table->foreignId('empresa_id')->constrained('empresas');
        $table->foreignId('cliente_id')->constrained('clientes');
        $table->boolean('update_fotos')->default(false);
        $table->dateTime('diagnostico')->nullable();
        $table->text('indicaciones_cliente')->nullable();
        $table->text('notas_mecanico')->nullable();
        $table->text('notas_retraso')->nullable();
        $table->string('telefono')->nullable();
        $table->string('ubicacion')->nullable();
        $table->timestamps();
    });
    Schema::create('archivos_orden_servicio', function (Blueprint $table) {
        $table->id();
        $table->estring('nombre');
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
        $table->foreignId('tipo_id')->constrained('tipos_imagenes');
        $table->timestamps();
    });
    Schema::create('vehiculos_conceptos_disponobles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tipo_id')->constrained('vehiculos_conceptos');
        $table->foreignId('modulo_orden')->constrained('modulos_ordenes_servicio');
        $table->timestamps();
    });

    Schema::create('datos_entrada', function (Blueprint $table) {
        $table->id();
        $table->dateTime('fecha');
        $table->dateTime('estimacion');
        $table->integer('kilomentraje');
        $table->foreignId('gasolina')->constrained('niveles_combustible');
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
    });
    Schema::create('datos_salida', function (Blueprint $table) {
        $table->id();
        $table->dateTime('fecha');
        $table->integer('kilomentraje');
        $table->foreignId('gasolina')->constrained('niveles_combustible');
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
    });
    Schema::create('estados', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
    });
    Schema::create('ciudades', function (Blueprint $table) {
        $table->id();
        $table->string('descripcion');
        $table->foreignId('estado_id')->constrained('estados');
    });
Schema::create('regimenes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('emisor_id')->constrained('emisor');
        $table->foreignId('user_id')->constrained('users');
        $table->text('xml');
        $table->text('pdf');
        $table->text('acuse')->nullable();
        $table->foreignId('estatus_id')->constrained('estatus');
        $table->decimal('monto',10,2);
    });
    Schema::create('empresas', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('rfc');
        $table->string('email');
        $table->string('logo');
        $table->string('calle');
        $table->integer('cp');
        $table->foreignId('ciudad_id')->constrained('ciudades');
        $table->foreignId('emisor_id')->constrained('emisor');
        $table->foreignId('user_id')->constrained('users');
        $table->foreignId('regimen_id')->constrained('regimenes');
        $table->integer('telefono');
        $table->integer('tel_celular');
        $table->integer('tel_negocio');
    });
    Schema::create('clietes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('empresa_id')->constrained('empresas');
        $table->string('nombre');
        $table->string('rfc');
        $table->string('email');
        $table->string('logo');
        $table->string('calle');
        $table->integer('cp');
        $table->foreignId('ciudad_id')->constrained('ciudades');
        $table->foreignId('emisor_id')->constrained('emisor');
        $table->integer('telefono');
        $table->integer('tel_celular');
        $table->integer('tel_negocio');
    });
    Schema::create('facturas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('empresa_id')->constrained('empresas');
        $table->foreignId('emisor_id')->constrained('emisor');
        $table->foreignId('user_id')->constrained('users');
        $table->text('xml');
        $table->text('pdf');
        $table->text('acuse')->nullable();
        $table->foreignId('estatus_id')->constrained('estatus');
        $table->decimal('monto',10,2);
    });
    
    Schema::create('presupuestos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
        $table->text('observaciones');
        $table->text('descripcion_mo');
        $table->text('garantia');
        $table->string('folio');
        $table->dateTime('vigencia');
        $table->foreignId('factura_id')->constrained('facturas')->nullable();
        $table->foreignId('tipo_id')->constrained('tipos');
        $table->foreignId('estatus_id')->constrained('estatus');
        $table->integer('kilomentraje');
    });
    Schema::create('pagos_presupuestos', function (Blueprint $table) {
        $table->id();
        $table->dateTime('fecha_pagado');
        $table->decimal('importe',10,2);
        $table->foreignId('presupuestos_id')->constrained('presupuestos');
    });
    Schema::create('usuarios_taller', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->foreignId('tipo_id')->constrained('tipos');
        $table->timestamps();
    });
    Schema::create('responsables_orden_servicio', function (Blueprint $table) {
        $table->id();
        $table->foreignId('administrador_transporte_id')->constrained('usuarios_taller');
        $table->foreignId('jefe_de_proceso_id')->constrained('usuarios_taller');
        $table->foreignId('trabajador_id')->constrained('usuarios_taller');
        $table->foreignId('tecnico_id')->constrained('usuarios_taller');
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
    });
    Schema::create('pedidos_ordenes_almacen', function (Blueprint $table) {
        $table->id();
        $table->dateTime('pedido_hecho');
        $table->dateTime('pedido_entregado')->nullable();
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
    });
    Schema::create('exteriores_order_servicio', function (Blueprint $table) {
        $table->id();
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
        $table->foreignId('antena_radio')->constrained('estatus');
        $table->foreignId('antena_telefono')->constrained('estatus');
        $table->foreignId('antena_cb')->constrained('estatus');
        $table->foreignId('estribos')->constrained('estatus');
        $table->foreignId('espejos_laterales')->constrained('estatus');
        $table->foreignId('guardafangos')->constrained('estatus');
        $table->foreignId('parabrisas')->constrained('estatus');
        $table->foreignId('sistema_alarma')->constrained('estatus');
        $table->foreignId('limpia_parabrisas')->constrained('estatus');
        $table->foreignId('luces_exteriores')->constrained('estatus');
    });
    Schema::create('interiores_order_servicio', function (Blueprint $table) {
        $table->id();
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
        $table->foreignId('puerta_interior_frontal')->constrained('estatus');
        $table->foreignId('puerta_interior_trasera')->constrained('estatus');
        $table->foreignId('puerta_delantera_frontal')->constrained('estatus');
        $table->foreignId('puerta_delantera_trasera')->constrained('estatus');
        $table->foreignId('asiento_interior_frontal')->constrained('estatus');
        $table->foreignId('asiento_interior_trasera')->constrained('estatus');
        $table->foreignId('asiento_delantera_frontal')->constrained('estatus');
        $table->foreignId('asiento_delantera_trasera')->constrained('estatus');
        $table->foreignId('consola_central')->constrained('estatus');
        $table->foreignId('claxon')->constrained('estatus');
        $table->foreignId('tablero')->constrained('estatus');
        $table->foreignId('quemacocos')->constrained('estatus');
        $table->foreignId('toldo')->constrained('estatus');
        $table->foreignId('elevadores_eletricos')->constrained('estatus');
        $table->foreignId('luces_interiores')->constrained('estatus');
        $table->foreignId('seguros_eletricos')->constrained('estatus');
        $table->foreignId('tapetes')->constrained('estatus');
        $table->foreignId('climatizador')->constrained('estatus');
        $table->foreignId('radio')->constrained('estatus');
        $table->foreignId('espejos_retrovizor')->constrained('estatus');
    });
    Schema::create('inventario_order_servicio', function (Blueprint $table) {
        $table->id();
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
        $table->boolean('llanta')->default(false);
        $table->boolean('cubreruedas')->default(false);
        $table->boolean('cables_corriente')->default(false);
        $table->boolean('candado_ruedas')->default(false);
        $table->boolean('estuche_herramientas')->default(false);
        $table->boolean('gato')->default(false);
        $table->boolean('llave_tuercas')->default(false);
        $table->boolean('trajeta_circulacion')->default(false);
        $table->boolean('triangulo_seguridad')->default(false);
        $table->boolean('extinguidor')->default(false);
        $table->boolean('placas')->default(false);
    });
    Schema::create('condiciones_pintura_order_servicio', function (Blueprint $table) {
        $table->id();
        $table->foreignId('orden_servicio_id')->constrained('ordenes_servicio');
        $table->boolean('decolorada')->default(false);
        $table->boolean('emblemas_completos')->default(false);
        $table->boolean('color_no_igual')->default(false);
        $table->boolean('logos')->default(false);
        $table->boolean('exeso_rayones')->default(false);
        $table->boolean('exeso_rociado')->default(false);
        $table->boolean('pequenias_grietas')->default(false);
        $table->boolean('danios_granizado')->default(false);
        $table->boolean('carroceria_golpes')->default(false);
        $table->boolean('lluvia_acido')->default(false);
    });

    }
    public function down(): void
    {
         Schema::dropIfExists('fondos_sustituidos');
    }
};
