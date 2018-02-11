<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;
use App\Product;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Storage;

class SellerProductController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;
        return $this->showAll($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, \App\User $seller)
    {
        /**
         * Estamos haciendo inyección directa desde el modelo User y no Seller
         * para posibilitar que un usuario que aún no es vendedor pero quiere poner
         * algún artículo a la venta y así convertirse en vendedor pueda hacerlo
         */
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image', // mimes:jpg,jpeg,png,bmp
        ];
        $this->validate($request, $rules);

        $data = $request->all();
        $data['status'] = \App\Product::PRODUCTO_NO_DISPONIBLE;
        /**
         * El método store recibe dos parámetros, en sí la ruta de almacenamiento que
         * parte de la raíz base definida en el sistema de archivos y el sistema de archivos en sí.
         * Como hemos definido como default nuestro propio sistema de archivos no tenemos que especificar
         * ningún segundo parámetro(es opcional), y el primero lo dejamos en blanco haciendo que sea
         * Laravel el que asigne un nombre personalizado y único para cada imagen.
         */
        $data['image'] = $request->image->store('');
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        /**
         * PROBLEMAS al utilizar imágenes medianamente grandes.
         * Checkear de donde puede venir dicha limitación, en php.ini 
         * todo está OK, con tamaños de 128MB.
         */
        return $this->showOne($product, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        /**
         * Como vemos en esta ocasión no necesitamos "tirar" de User ya que
         * ahora sí requerimos que exista alguna instancia del producto (ya que 
         * eso es lo que queremos actualizar), por lo tanto ya se trata de un seller
         */

         $rules = [
             'quantity' => 'integer|min:1',
             'status' => 'in: ' . Product::PRODUCTO_DISPONIBLE . ',' . Product::PRODUCTO_NO_DISPONIBLE,
             'image' => 'image',
         ];

         $this->validate($request, $rules);

        
         // Controlamos si el ID del vendedor que recibimos en la petición es el 
         // mismo ID que el vendedor asociado a ese producto.
         $this->verificarVendedor($seller, $product);

         $product->fill($request->only([
            'name',
            'description',
            'quantity',
         ]));

         // Vamos a permitir cambiar el estado únicamente si ese producto tiene ya asignada al menos una categoría
         if($request->has('status')){
             // Lo cambiamos primero, ya que igual es a no disponible y no nos importa
             $product->status = $request->status;
             // Ahora sí comprobamos
             if($product->estaDisponible() && $product->categories()->count() == 0) {
                 return $this->errorResponse('Un producto activo debe tener al menos una categoría', 409);
             }
         }
         /**
          * Para la actualización de imagen procederemos a eliminar la anterior y subir la nueva independientemente
          * de si esta es realmente nueva o es la misma, puesto que es complejo y "no worth it" comprobarlo.
          * DETALLE: En cuanto a la petición a realizar a esta parte del servicio y debido a que HTTP no nos permite
          * incluir archivos persé en un put (x-url-...) entonces FALSEAMOS una petición POST a PUT incluyendo en uno
          * de los campos, como clave: _method y como valor: put. De esta forma, aún estando realmente realizando una 
          * petición POST, Laravel la interpreta como una petición PUT.
          */
          if($request->hasFile('image')) {
            Storage::delete($product->image);
            $product->image = $request->image->store('');
          }

         if($product->isClean()) {
             return $this->errorResponse('Se debe modificar alguno de los campos para actualizar', 422);
         }

         $product->save();
         return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->verificarVendedor($seller, $product);
        Storage::delete($product->image);
        $product->delete();
        return $this->showOne($product);
    }

    protected function verificarVendedor(Seller $seller, Product $product){
        if($seller->id != $product->seller_id){
            /**
             * Aquí en vez de retornar el mensaje de error mediante nuestra Apicontroller disparamos una excepción
             * así nos evitamos agregar condicionales comprobando el valor de la respuesta de esta función
             */
            throw new HttpException(422, 'El vendedor especificado no es el vendedor real del producto');
        }
    }
}
