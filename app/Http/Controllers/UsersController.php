<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \Spatie\Permission\Models\Permission;
use \Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
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
        return $this->GetRolesAndPermission($user);
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
    public function ToggleRole(Request $request){
        $request->validate([
            'id' => ['required','exists:users,id'],
            'role' => ['required','string','exists:roles,name'],
        ],
        [
            'id.required' => 'El ID del usuario es obligatorio.',
            'id.exists' => 'El usuario no existe.',
            'role.required' => 'El rol es obligatorio.',
            'role.string' => 'El rol debe ser una cadena de texto.',
            'role.exists' => 'El rol no existe.',
        ]);
        $id=$request->id;
        $role=$request->role;
        $user=User::find($id);
        if($user->hasRole($role)){
            if($role==='Super Admin'){
                return response()->json(['message'=>'No se puede revocar el rol de Super Admin'],403);
            }
            $user->removeRole($role);
        }else{
            $user->assignRole($role);
            $roleInstance=Role::findByName($role, 'web');
            $permisosrol=$roleInstance->permissions->pluck('name');
            $permisos=$user->getDirectPermissions()->pluck('name');
            $perimosuser=$permisos->diff($permisosrol);    
            $user->syncPermissions($perimosuser);
        }
        return $this->GetRolesAndPermission($user);
    }
    public function TogglePermiso(Request $request){
        $request->validate([
            'id' => ['required','exists:users,id'],
            'permiso' => ['required','string','exists:permissions,name'],
        ],
        [
            'id.required' => 'El ID del usuario es obligatorio.',
            'id.exists' => 'El usuario no existe.',
            'permiso.required' => 'El permiso es obligatorio.',
            'permiso.string' => 'El permiso debe ser una cadena de texto.',
            'permiso.exists' => 'El permiso no existe.',
        ]);
        $id=$request->id;
        $permiso=$request->permiso;
        $user=User::find($id);
        if($user->hasPermissionTo($permiso)){
            if($user->hasRole('Super Admin')){
                return response()->json(['message'=>'No se puede revocar el permiso a un Super Admin'],403);
            }
            if($user->permissions()->where('name', $permiso)->exists()){
                $user->revokePermissionTo($permiso);
            }else{
                return response()->json(['message'=>'No se puede revocar este permiso porque está asignado a través de un rol'],404);
            }
        }else{
            $user->givePermissionTo($permiso);
        }
        if(!$user){
            return response()->json(['message'=>'Usuario no encontrado'],404);
        }
        return $this->GetRolesAndPermission($user);
    }

    private function GetRolesAndPermission($user){

        $userpermisos=$user->getAllPermissions();
        $userpermisos=$userpermisos->map(function ($item){
            return $item->name;
        });
        $userroles=$user->getRoleNames();
        $allpermisos=Permission::all();
        $allpermisos=$allpermisos->map(function ($item){
            return $item->name;
        });
        $allroles=Role::all();
        $allroles=$allroles->map(function ($item){
            return $item->name;
        });

        return response()->json(compact('userpermisos', 'allpermisos', 'userroles', 'allroles'));
    }
}
