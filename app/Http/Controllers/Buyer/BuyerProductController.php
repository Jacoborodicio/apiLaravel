<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;

class BuyerProductController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        /**
         * Esta siguiente línea falla y es debido a que al ser una 
         * relacción uno a muchos, al momento de acceder a las transacciones, 
         * Laravel automáticamente lo convierte en una colección por lo que después
         * no podremos acceder a product ya que no está en la colección como tal.
         */
        // $products = $buyer->transactions->product;
        /**
         * Aquí estamos accediendo a través de transactions() directamente al 
         * query builder definido en la función por lo que podemos refinar la búsqueda 
         * en este caso obteniendo así una lista de transacciones y cada una de ellas con el producto.
         * Como en $products tenemos en principio una colección, podemos emplear el método pluck para 
         * indicar que sólo queremos trabajar con una parte de esa colección.
         */
        $products = $buyer->transactions()->with('product')
                            ->get()
                            ->pluck('product');
        // dd($products);
        
        return $this->showAll($products);
    }

    
}
