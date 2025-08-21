<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \Spatie\Permission\Models\Permission;
class UsersController extends Controller
{
    public  function ReadUsers(Request $request){
        $currentpage=$request->page ?? 1;
        $itemsperpage=$request->itemsperpage ?? 10;
        $elements=User::skip(($currentpage-1)*$itemsperpage)->take($itemsperpage)->get();
        $elements=$elements->map(function ($item){
            return[
                'name'=>$item->name,
                'email'=>$item->email,
                'varified'=>$item->email_verified_at != null ? true:false,
                'date'=>Carbon::parse($item->created_at)->format('Y-m-d H:i:s'),
                'id'=>$item->id,
            ];
        });
        return response()->json(compact('elements'));
    }
    public function GetPermisos(Request $request){
        $id=$request->id;
        $user=User::find($id);
        if(!$user){
            return response()->json(['message'=>'Usuario no encontrado'],404);
        }
        $permisos=$user->getAllPermissions();
        $permisos=$permisos->map(function ($item){
            return $item->namel;
        });
        $allpermisos=Permission::all();
        $allpermisos=$allpermisos->map(function ($item){
            return $item->name;
        });

        $permisosporasignar=$allpermisos->diff($permisos);
        return response()->json(compact('permisos', 'permisosporasignar'));
    }
    public function UpdatePermisos(Request $request){
        $request->validate([
            'id' => ['required','exists:users,id'],
            'permisos' => ['required','array'],
            'permisos.*' => ['string','exists:permissions,name'],
        ],
        [
            'id.required' => 'El ID del usuario es obligatorio.',
            'id.exists' => 'El usuario no existe.',
            'permisos.required' => 'Los permisos son obligatorios.',
            'permisos.array' => 'Los permisos deben ser un arreglo.',
            'permisos.*.string' => 'Cada permiso debe ser una cadena de texto.',
            'permisos.*.exists' => 'Uno o más permisos no existen.',
        ]);

        $id=$request->id;
        $permisos=$request->permisos;
        $user=User::find($id);
        $user->givePermissionTo($permisos);
        return response()->json(['message'=>'Permisos actualizados correctamente']);
    }
}
