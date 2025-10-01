<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NivelesCombustible;
use Illuminate\Http\Request;

class selectcontroller extends Controller
{
    public function NivelesCombustible(Request $request){
        $options=NivelesCombustible::selectraw("id as value , descripcion as label")->get();
        return response()->json(compact('options'));
    }
}
