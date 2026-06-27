<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Archivos;
use App\Models\RutasArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArchivosController extends Controller
{
    public function Delete(Request $request){

        $request->validate(['id'=>['required','exists:archivos,id'],'origen'=>['required','in:ordenservicio,presupuesto']]);
        $file=Archivos::find($request->id);
        try{
            DB::beginTransaction();
            if(!$file){
                throw new \Exception('Imagen Eliminada Anteriormente');
            }
            if(($request->origen === 'ordenservicio' && (!$file->recepcion_vehicular_id || (int)$file->tipo_id !== 63) ) || ($request->origen === 'presupuesto' && !$file->presupuesto_id )){
                throw new \Exception('Imagen No Valida Para Eliminar por este medio ');
            }

            if($request->origen === 'ordenservicio'){
                $orden=$file->recepcion_vehicular?->orden_servicio;
                if(!$orden || ($orden && !($orden->recepcion_vehicular->cambiar_archivos ?? false))){
                    throw new \Exception('Orden de Servicio No Habilitada ');
                }
                
                
            }else{

            }
            $path=RutasArchivo::where('tipo_id', $file->tipo_id)->where('estatus_id', $file->estatus_id)->first();
            if($path){
                $pathstorage=$path->folder . '/' . $file->nombre;
                if (Storage::disk($path->disk)->exists($pathstorage)) {
                    Storage::disk($path->disk)->delete($pathstorage);
                } else {
                    Log::warning("No existe el archivo: " . $pathstorage);
                }
            }
            $file->delete();
            DB::commit();
            return response()->json(['message'=>'Archivo Eliminado Correctamente']);
        }catch(\Throwable $th){
            DB::rollBack();
            return response()->json(['message'=>$th->getMessage()],500);
        }
    }
}
