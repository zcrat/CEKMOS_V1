<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function Termwind\parse;

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
}
