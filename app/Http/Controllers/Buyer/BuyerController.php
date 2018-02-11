<?php

namespace App\Http\Controllers\Buyer;

use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;
use App\Buyer;
class BuyerController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * Tener en cuenta que Buyer así como seller heredan de User, por lo que
         * no podemos hacerlo "normal", únicamente con Buyer::all() ya que lo que 
         * estaríamos haciendo sería obtener todos los usuarios, cuando realmente sólo
         * queremos aquellos que posean compras por lo que miramos que tengan transacciones.
         */

        $compradores = Buyer::has('transactions')->get();
        // Pasamos de esto a lo siguiente por la implementación del controlador personalizado
        // return response()->json(['data' => $compradores], 200);
        return $this->showAll($compradores);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comprador = Buyer::has('transactions')->findOrFail($id);
        // return response()->json(['data' => $comprador], 200);
        return $this->showOne($comprador);
    }
}
