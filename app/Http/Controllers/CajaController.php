<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CajaMovimientos;
use Illuminate\Http\Request;
Use Inertia\Inertia;
class CajaController extends Controller
{
    public function View(Request $request){
        return Inertia::render('Admin/CajaMovimientos');
    }
    public function Read(Request $request){
        $currentPage=$request->currentPage;
        $itemsPerPage=$request->itemsPerPage;
        $offset=($currentPage-1)*$itemsPerPage;
        $search='%'.$request->search.'%';
        $query=CajaMovimientos::with('user')->where('descripcion','LIKE',$search);
        $totalItems=(clone $query)->count();
        $items=$query->skip($offset)->take($itemsPerPage);
        $totalPages=ceil($totalItems/$itemsPerPage);

        return response()->json(
            compact('totalPages', 'totalItems', 'items')
        );
    }
}
