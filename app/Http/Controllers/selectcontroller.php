<?php

namespace App\Http\Controllers;

use App\Models\Estatus;
use App\Models\ModuloOrdenesServicio;
use App\Models\NivelesCombustible;
use App\Models\Tipos;
use Illuminate\Http\Request;

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

    public function ModulosDisponibles(Request $request)
    {
        $user = $request->user();
        $options = ModuloOrdenesServicio::query();
        if (! $user->hasRole('Super Admin')) {
            $options->whereIn(
                'id',
                $user->modulos_orden()->select('modulo_orden_id')
            );
        }

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
