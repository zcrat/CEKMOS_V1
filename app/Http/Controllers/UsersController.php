<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ModuloOrdenesServicio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \Spatie\Permission\Models\Permission;
use \Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Models\Notificaciones;
use App\Models\ModulosPerUser;
use \App\Notifications\SeendNotification;
use Illuminate\Support\Facades\Crypt;

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
                'id'=>Crypt::encrypt($item->id),
            ];
        });
        return response()->json(compact('elements'));
    }
    public function GetPermisos(Request $request){
        $id=$request->id;
        $id=Crypt::decrypt($id);
        if(!$id){
            return response()->json(['message'=>'ID de usuario no válido'],400);
        }
        $user=User::find($id);
        if(!$user){
            return response()->json(['message'=>'Usuario no encontrado'],404);
        }
        return $this->GetRolesAndPermission($user);
    }
    public function GetModulos(Request $request){
        $id=$request->id;
        $id=Crypt::decrypt($id);
        if(!$id){
            return response()->json(['message'=>'ID de usuario no válido'],400);
        }
        $user=User::with('modulos_orden')->find($id);
        if(!$user){
            return response()->json(['message'=>'Usuario no encontrado'],404);
        }
        return $this->GetModulosPerUser($user);
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
        $id=Crypt::decrypt($id);
        if(!$id){
            return response()->json(['message'=>'ID de usuario no válido'],400);
        }
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
        $id=Crypt::decrypt($id);
        if(!$id){
            return response()->json(['message'=>'ID de usuario no válido'],400);
        }
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
        $id=Crypt::decrypt($id);
        if(!$id){
            return response()->json(['message'=>'ID de usuario no válido'],400);
        }
        $permiso=$request->permiso;
        $user=User::find($id);
        if($user->hasPermissionTo($permiso)){
            if($user->hasRole('Super Admin')){
                return response()->json(['message'=>'No se puede revocar el permiso a un Super Admin'],403);
            }
            if($user->permissions()->where('name', $permiso)->exists()){
                $user->revokePermissionTo($permiso);
                $this->Notificate($user->id,'Permiso Eliminado', 'El permiso '.$permiso.' ha sido eliminado de tu cuenta','warning',2);
            }else{
                return response()->json(['message'=>'No se puede revocar este permiso porque está asignado a través de un rol'],404);
            }
        }else{
            $user->givePermissionTo($permiso);
            $this->Notificate($user->id,'Permiso Agregado', 'El permiso '.$permiso.' ha sido Agregado de tu cuenta','info',2);
        }
        if(!$user){
            return response()->json(['message'=>'Usuario no encontrado'],404);
        }
        return $this->GetRolesAndPermission($user);
    }
    public function ToggleModulo(Request $request){
        $request->validate([
            'user' => ['required','exists:users,id'],
            'modulo' => ['required','exists:modulo_ordenes_servicios,id'],
        ],
        [
            'user.required' => 'El ID del usuario es obligatorio.',
            'user.exists' => 'El usuario no existe.',
            'modulo.required' => 'El Modulo De Orden De Servicio es obligatorio.',
            'modulo.exists' => 'El Modulo De Orden De Servicio no existe.',
        ]);
        $user=$request->user;
        $modulo=$request->modulo;
        $user=Crypt::decrypt($user);
        if(!$user){
            return response()->json(['message'=>'ID de usuario no válido'],400);
        }
        $exist=ModulosPerUser::withTrashed()->where('user_id',$user)->where('modulo_orden_id',$modulo)->first();
        if ($exist) {
            if (isset($exist->deleted_at)) {
                $exist->restore();
            } else {
                $exist->delete();
            }
        }else{
            ModulosPerUser::create([
                'modulo_orden_id'=>$modulo,
                'user_id'=>$user,
            ]);
        }
        
        $user=User::find($user);
        return $this->GetModulosPerUser($user);
    }
    public function DeleteUser(Request $request){
        $request->validate([
            'id' => ['required','string'],
        ],
        [
            'id.required' => 'El ID del usuario es obligatorio.',
            'id.exists' => 'El usuario no existe.',
        ]);
        $id=$request->id;
        $id=Crypt::decrypt($id);
        if(!$id){
            return response()->json(['message'=>'ID de usuario no válido'],400);
        }
        if($id === $request->user()->id){
            return response()->json(['message'=>'No Puedes Eliminar Tu Propio Perfil'],500);
        }
        
        $user=User::find($id);
        $user->tokens()->delete();
        $user->delete();
        event(new \App\Events\DataUserEvents(Crypt::encrypt($id),'delete'));
         return response()->json(['message' => 'Usuario eliminado correctamente.'], 200);
    }
    private function GetModulosPerUser($user){
        $usermodulos = $user->load('modulos_orden')->modulos_orden->isNotEmpty()
        ? $user->modulos_orden->pluck('modulo_orden_id')->toArray()
        : [];

        $allmodulos=ModuloOrdenesServicio::all()->map(function($item){
            return [
                'id'=>$item->id,
                'descripcion'=>$item->descripcion,
            ];
        });

        return response()->json(compact('usermodulos', 'allmodulos'));
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
    public function GetNotificaciones(Request $request){
        $user=$request->user();
        $shownotifications=$request->shownotifications ?? 5; 
        $notificaciones=Notificaciones::where('user_id',$user->id)->orderBy('prioridad','asc')->orderBy('created_at','desc')->orderBy('read_at','desc')->take($shownotifications)->get()->map(function($item){
            return[
                'id'=>$item->id,
                'title'=>$item->title,
                'body'=>$item->message,
                'type'=>$item->tipo,
                'created_at'=>Carbon::parse($item->created_at)->format('Y-m-d H:i:s'),
                'read'=>$item->read_at ? true:false
            ];
        });
        $countnotificaciones=Notificaciones::where('user_id',$user->id)->whereNull('read_at')->count();
        $hasmore=Notificaciones::where('user_id',$user->id)->count() > $shownotifications;
        return response()->json(compact('notificaciones','countnotificaciones','hasmore'));
    }
    public function ReadNotification(Request $request){
        $user=$request->user();
        $id=$request->id;
        $notificacion=Notificaciones::where('user_id',$user->id)->where('id',$id)->first();
        if(!$notificacion){
            return response()->json(['message'=>'Notificación no encontrada'],404);
        }
        if(!$notificacion->read_at){
            $notificacion->read_at=Carbon::now();
            $notificacion->save();
        }
        return response()->json(['message'=>'Notificación marcada como leída']);
    }
    private function Notificate($user_id, $title, $message, $tipo=1, $prioridad=1){
        try{
            $notificacion=new Notificaciones();
            $notificacion->user_id=$user_id;
            $notificacion->title=$title;
            $notificacion->message=$message;
            $notificacion->tipo_id=$tipo;
            $notificacion->prioridad=$prioridad;
            $notificacion->save();
            $user=User::find($user_id);

            if($user){
                $noti=[
                'id'=>$notificacion->id,
                'title'=>$notificacion->title,
                'body'=>$notificacion->message,
                'type'=>$notificacion->tipo,
                'created_at'=>Carbon::parse($notificacion->created_at)->format('Y-m-d H:i:s'),
                'read'=>$notificacion->read_at ? true:false
            ];
                $user->notify(new SeendNotification($user_id,$noti) );
            }
        }catch(\Exception $e){
            Log::error('Error al enviar notificación: '.$e->getMessage());
        }
    }
}
