<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;

class BuyerSellerController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        $sellers = $buyer->transactions()->with('product.seller')
                        ->get()
                        // Si ponemos solo seller nos dará vacío, ya que estamos buscando
                        // algo que está relaccionado/dentro de los productos, por la primera línea
                        ->pluck('product.seller')
                        ->unique('id')
                        // Reordenamos la colección eliminando los que puedan estar
                        // vacíos después de unique()
                        ->values();
        // dd($sellers);
        
        return $this->showAll($sellers);
    }
}
