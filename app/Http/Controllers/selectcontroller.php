<?php

namespace App\Http\Controllers;

use App\Models\Estatus;
use App\Models\ModuloOrdenesServicio;
use App\Models\NivelesCombustible;
use App\Models\Tipos;
use App\Services\AlcanceOrdenesServicio;
use App\Services\AlcanceRecepcionesVehiculares;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class selectcontroller extends Controller
{
    public function TiposVehiculosGeneral(Request $request)
    {
        $options = Tipos::selectraw('id as value , descripcion as label')->where('categoria_id', 3)->get();

        return response()->json(compact('options'));
    }

    public function TiposIdsPerCategory(Request $request)
    {
        $category = $request->input('categoria_id');
        $options = [];
        if (! empty($category)) {
            $options = Tipos::where('categoria_id', $category)->selectraw('id as value , descripcion as label')->get();
        }

        return response()->json(compact('options'));
    }

    public function NivelesCombustible(Request $request)
    {
        $options = NivelesCombustible::selectraw('id as value , descripcion as label')->get();

        return response()->json(compact('options'));
    }

    public function ModulosOrden(Request $request)
    {
        $user = Auth::user()->load('modulos_orden');
        $options = ModuloOrdenesServicio::query();
        if (! $request->user()->hasRole('Super Admin')) {
            $modulosvisibles = $user->modulos_orden ? $user->modulos_orden->pluck('modulo_orden_id')->toarray() : [];
            $options->whereIn('id', $modulosvisibles);
        }
        $options = $options->selectraw('id as value , descripcion as label')->orderByDesc('año')->get();

        return response()->json(compact('options'));
    }

    public function ModulosPresupuestos(Request $request)
    {
        $user = $request->user();
        $options = ModuloOrdenesServicio::query();

        match (AlcanceOrdenesServicio::nivel($user)) {
            AlcanceOrdenesServicio::TODOS => null,
            AlcanceOrdenesServicio::MODULOS => $options->whereIn(
                'id',
                $user->modulos_orden()->select('modulo_orden_id')
            ),
            AlcanceOrdenesServicio::BASE => $options->whereHas(
                'ordenes_servicio',
                fn ($orders) => $orders
                    ->where('user_id', $user->id)
                    ->whereHas('presupuestos')
            ),
            default => $options->whereRaw('1 = 0'),
        };

        $options = $options
            ->selectRaw('id as value, descripcion as label')
            ->orderByDesc('año')
            ->get();

        return response()->json(compact('options'));
    }

    public function ModulosRecepcionesVehiculares(Request $request)
    {
        $user = $request->user();
        $options = ModuloOrdenesServicio::query();

        match (AlcanceRecepcionesVehiculares::nivel($user)) {
            AlcanceRecepcionesVehiculares::TODOS => null,
            AlcanceRecepcionesVehiculares::MODULOS => $options->whereIn(
                'id',
                $user->modulos_orden()->select('modulo_orden_id')
            ),
            AlcanceRecepcionesVehiculares::BASE => $options->whereHas(
                'ordenes_servicio',
                fn ($orders) => $orders
                    ->where('user_id', $user->id)
                    ->whereHas('recepcion_vehicular')
            ),
            default => $options->whereRaw('1 = 0'),
        };

        $options = $options
            ->selectRaw('id as value, descripcion as label')
            ->orderByDesc('año')
            ->get();

        return response()->json(compact('options'));
    }

    public function EstatusIdsPerCategory(Request $request)
    {
        $category = $request->input('categoria_id');
        $options = [];
        if (! empty($category)) {
            $options = Estatus::where('categoria_id', $category)->selectraw('id as value , descripcion as label')->get();
        }

        return response()->json(compact('options'));
    }
}
