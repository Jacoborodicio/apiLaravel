<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;

class CategoryBuyerController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        /**
         * Esta parte tiene un detalle importante, y es que es necesario
         * manejar colecciones dos veces. El caso es que el pluck de transactions
         * nos devuelve DIVERSAS colecciones, por ello no es posible para Laravel acceder
         * a cada una de ella para ver los compradores mediante pluck('transactions.buyer')
         * como ocurrió en ejemplos anteriores. Así que primero tenemos que sacar todas esas colecciones,
         * juntarlas en una mediante collapse y ahora que ya están en una sola collection, ahora sí 
         * podemos sacar los compradores con pluck('buyer') y lo que sigue.
         */
        $buyers = $category->products()
                    ->whereHas('transactions')
                    ->with('transactions.buyer')
                    ->get()
                    ->pluck('transactions')
                    ->collapse()
                    ->pluck('buyer')
                    ->unique()
                    ->values();
        return $this->showAll($buyers);
    }
}
