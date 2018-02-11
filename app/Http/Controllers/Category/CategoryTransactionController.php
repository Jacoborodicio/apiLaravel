<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;

class CategoryTransactionController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        /**
         * Aquí nos encontramos con un problema, puede ser que nos encontremos con 
         * transacciones vacías ya que puede ocurrir que un cierto producto aún no tenga
         * asociada ninguna transacción. Para solventar esto hacemos uso del método 
         * whereHas('') consiguiendo aquellos únicamente que ya tienen una transacción asociada.
         */
        $transactions = $category->products()
                        ->whereHas('transactions')
                        ->with('transactions')
                        ->get()
                        ->pluck('transactions')
                        ->collapse();


        return $this->showAll($transactions);
    }
}
