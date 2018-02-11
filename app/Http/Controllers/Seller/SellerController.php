<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;
use App\Seller;

class SellerController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Aquí pasa lo mismo que con buyer
        $vendedores = Seller::has('products')->get();
        // return response()->json(['data' => $vendedores], 200);
        return $this->showAll($vendedores);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vendedor = Seller::has('products')->findOrFail($id);
        // return response()->json(['data' => $vendedor], 200);
        return $this->showOne($vendedor);
    }
}
