<?php

namespace App\Http\Controllers\Product;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;

class ProductCategoryController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $categories = $product->categories;
        return $this->showAll($categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product, \App\Category $category)
    {
        /**
         * Se trata no de actualizar en sí sino de añadir una categoría a un producto.
         * Lo realizaremos de tres formas explicando cuál es la correcta para este caso
         * concreto y porqué
         */

         // Mediante sync
        //  $product->categories()->sync([$category->id]);
         // Mostramos las categorías para ver si se ha añadido o que ha pasado.
         /**
          * Una vez realizada la prueba con sync, vemos que no es que se añada una nueva categoría,
          * sino que se borran las que había dejando únicamente la añadida, así que no nos vale.
          */
        //   $product->categories()->attach([$category->id]);
        /**
         * Mediante atach parece funcionar, sin embargo, cuando queremos añadir la misma categoría dos veces
         * sí nos lo permite, y ese es un comportamiento erróneo en este caso concreto. Así que no nos vale.
         */
        $product->categories()->syncWithoutDetaching([$category->id]);
        // Esta última sí funciona perfectamente, agrega sin borrar, y si ya existe no hace nada.
         return $this->showAll($product->categories);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, \App\Category $category)
    {
        // Estamos borrando la relacción entre el producto y categoría, no ninguno de ellos
        // Comprobamos que efectivamente existe dicha relacción
        if(!$product->categories()->find($category->id)){
            return $this->errorResponse('La categoría especificada no es una categoría de este producto.', 404);
        }
        $product->categories()->detach([$category->id]);
        return $this->showAll($product->categories);
    }
}
