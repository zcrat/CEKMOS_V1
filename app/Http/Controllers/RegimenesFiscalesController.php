<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RegimenesFiscalesController extends Controller
{
    public function Create(Request $request){
        $request->validate([
            'clave' => ['required', 'string','unique'],
            'descripcion' => ['required', 'string'],
            'regimen_fiscal' => ['nullable', 'string']
        ], [
            'clave.required' => 'La clave es obligatoria.',
            'clave.string' => 'La clave debe ser una cadena de texto.',
            'clave.unique' => 'La clave ya esta registrada.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'regimen_fiscal.string' => 'El régimen fiscal debe ser una cadena de texto si se proporciona.'
        ]);
        try {
            RegimenesFiscalesModel::Create([
                'clave' =>  $request->clave,
                'descripcion' => $request->descripcion,
                'regimen_fiscal' =>  $request->regimen_fiscal,
            ]);
            $message='Creado Exitosamente';
            return response()->json(compact('message'));
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message'=>$e->getmessage()]);
        }
    }
    public function Update(Request $request){
        $request->validate([
            'id' => ['required','exists:regimes_fiscales,id'],
            'clave' => ['required', 'string',Rule::unique('regimes_fiscales', 'clave')->ignore($request->id)],
            'descripcion' => ['required', 'string'],
            'regimen_fiscal' => ['nullable', 'string']
        ], [
            'id.required' => 'El Regimen es obligatoria.',
            'id.exists' => 'El Regimen no existe',
            'clave.required' => 'La clave es obligatoria.',
            'clave.unique' => 'La clave ya esta registrada.',
            'clave.string' => 'La clave debe ser una cadena de texto.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'regimen_fiscal.string' => 'El régimen fiscal debe ser una cadena de texto si se proporciona.'
        ]);
        try {
            RegimenesFiscalesModel::where('id', $request->id)->update([
                'clave' => $request->clave,
                'descripcion' => $request->descripcion,
                'regimen_fiscal' => $request->regimen_fiscal,
            ]);
            $message='Actualizado Exitosamente';
            return response()->json(compact('message'));
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message'=>$e->getmessage()]);
        }
    }
    public function Delete(Request $request){
        $request->validate([
            'id' => ['required','exists:regimes_fiscales,id']
        ], [
            'id.required' => 'El Regimen es obligatoria.',
            'id.exists' => 'El Regimen no existe',
        ]);
        try {
            RegimenesFiscalesModel::where('id', $request->id)->delete();
            $message = 'Eliminado Exitosamente';
            return response()->json(compact('message'));
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message'=>$e->getmessage()]);
        }
    }
    public function Read(Request $request){

        $elements=RegimenesFiscalesModel::get()->map(function($element){
            return[
                'id'=>$request->id,
                'clave'=>$request->clave,
                'descripcion'=>$request->descripcion,
                'regimen_fiscal'=>$request->id,
            ];
        });
        return response()->json(compact('elements'));
    }
}

