<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;

class BuyerCategoryController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        /**
         * De esta forma, lo que obtenemos son varias listas de categorías, ya que 
         * Laravel al juntar una con otra forma colecciones y nosotros lo que queremos
         * es una sola lista con todas las categorías en ella.
         */
        // $categories = $buyer->transactions()->with('product.categories')
        //                 ->get()
        //                 ->pluck('product.categories')
        //                 ->unique('id')
        //                 ->values();

        // Entonces: Utilizamos el método collapse()
        $categories = $buyer->transactions()->with('product.categories')
                        ->get()
                        ->pluck('product.categories')
                        ->collapse()
                        ->unique('id')
                        ->values();



        return $this->showAll($categories);
    }

}
