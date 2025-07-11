<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmpleadosController extends Controller
{
    public function Create(Request $request){
       
        $request->validate([
            'nombre' => ['required', 'string'],
            'peterno' => ['required', 'string'],
            'materno' => ['nullable', 'string'],
            'curp' => ['required', 'string','unique', 'size:18'],
            'rfc' => ['required', 'string', 'unique','size:13'],
            'regimen_fiscal_id' => ['required', 'exists:regimes_fiscales,id'],
            'uso_cfdi_id' => ['required', 'exists:cfdis,id'],
            'domicilio_fiscal' => ['required', 'string', 'size:13'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',

            'peterno.required' => 'El apellido paterno es obligatorio.',
            'peterno.string' => 'El apellido paterno debe ser una cadena de texto.',

            'materno.string' => 'El apellido materno debe ser una cadena de texto si se proporciona.',

            'curp.required' => 'La CURP es obligatoria.',
            'curp.unique' => 'La CURP ya esta registrada.',
            'curp.string' => 'La CURP debe ser una cadena de texto.',
            'curp.size' => 'La CURP debe tener exactamente 18 caracteres.',

            'rfc.required' => 'El RFC es obligatorio.',
            'rfc.unique' => 'El RFC ya esta registrado.',
            'rfc.string' => 'El RFC debe ser una cadena de texto.',
            'rfc.size' => 'El RFC debe tener exactamente 13 caracteres.',

            'regimen_fiscal_id.required' => 'Debes seleccionar un régimen fiscal.',
            'regimen_fiscal_id.exists' => 'El régimen fiscal seleccionado no es válido.',

            'uso_cfdi_id.required' => 'Debes seleccionar un uso de CFDI.',
            'uso_cfdi_id.exists' => 'El uso de CFDI seleccionado no es válido.',

            'domicilio_fiscal.required' => 'El domicilio fiscal es obligatorio.',
            'domicilio_fiscal.string' => 'El domicilio fiscal debe ser una cadena de texto.',
            'domicilio_fiscal.size' => 'El domicilio fiscal debe tener exactamente 13 caracteres.',
        ]);
        try {
            EmpleadosModel::Create([
                'nombre' =>  $request->nombre,
                'peterno' => $request->peterno,
                'materno' =>  $request->materno,
                'curp' =>  $request->curp,
                'rfc' =>  $request->rfc,
                'regimen_fiscal_id' =>  $request->regimen_fiscal_id,
                'uso_cfdi_id' =>  $request->uso_cfdi_id,
                'domicilio_fiscal' =>  $request->domicilio_fiscal,
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
            'id' => ['required','exists:employes,id'],
            'nombre' => ['required', 'string'],
            'peterno' => ['required', 'string'],
            'materno' => ['nullable', 'string'],
            'curp' => ['required', 'string',Rule::unique('employes', 'curp')->ignore($request->id), 'size:18'],
            'rfc' => ['required', 'string',Rule::unique('employes', 'rfc')->ignore($request->id), 'size:13'],
            'regimen_fiscal_id' => ['required', 'exists:regimes_fiscales,id'],
            'uso_cfdi_id' => ['required', 'exists:cfdis,id'],
            'domicilio_fiscal' => ['required', 'string', 'size:13'],
        ], [
            'id.required' => 'El Empleado es obligatoria.',
            'id.exists' => 'El Empleado no existe',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',

            'peterno.required' => 'El apellido paterno es obligatorio.',
            'peterno.string' => 'El apellido paterno debe ser una cadena de texto.',

            'materno.string' => 'El apellido materno debe ser una cadena de texto si se proporciona.',

            'curp.required' => 'La CURP es obligatoria.',
            'curp.unique' => 'La CURP ya esta registrada.',
            'curp.string' => 'La CURP debe ser una cadena de texto.',
            'curp.size' => 'La CURP debe tener exactamente 18 caracteres.',

            'rfc.required' => 'El RFC es obligatorio.',
            'rfc.unique' => 'El RFC ya esta registrado.',
            'rfc.string' => 'El RFC debe ser una cadena de texto.',
            'rfc.size' => 'El RFC debe tener exactamente 13 caracteres.',

            'regimen_fiscal_id.required' => 'Debes seleccionar un régimen fiscal.',
            'regimen_fiscal_id.exists' => 'El régimen fiscal seleccionado no es válido.',

            'uso_cfdi_id.required' => 'Debes seleccionar un uso de CFDI.',
            'uso_cfdi_id.exists' => 'El uso de CFDI seleccionado no es válido.',

            'domicilio_fiscal.required' => 'El domicilio fiscal es obligatorio.',
            'domicilio_fiscal.string' => 'El domicilio fiscal debe ser una cadena de texto.',
            'domicilio_fiscal.size' => 'El domicilio fiscal debe tener exactamente 13 caracteres.',
        ]);
        try {
            EmpleadosModel::where('id', $request->id)->Update([
                'nombre' =>  $request->nombre,
                'peterno' => $request->peterno,
                'materno' =>  $request->materno,
                'curp' =>  $request->curp,
                'rfc' =>  $request->rfc,
                'regimen_fiscal_id' =>  $request->regimen_fiscal_id,
                'uso_cfdi_id' =>  $request->uso_cfdi_id,
                'domicilio_fiscal' =>  $request->domicilio_fiscal,
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
            'id' => ['required','exists:employes,id']
        ], [
            'id.required' => 'El Empleado es obligatoria.',
            'id.exists' => 'El Empleado no existe',
        ]);
        try {
            EmpleadosModel::where('id', $request->id)->delete();
            $message = 'Eliminado Exitosamente';
            return response()->json(compact('message'));
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message'=>$e->getmessage()]);
        }
    }
}
