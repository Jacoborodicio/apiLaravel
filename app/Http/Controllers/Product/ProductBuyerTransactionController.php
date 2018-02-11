<?php

namespace App\Http\Controllers\Product;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Transaction;

class ProductBuyerTransactionController extends Apicontroller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        /**
         * Nota: Estamos recibiendo el buyer de USER. Esto es así igual que en seller hacíamos lo mismo,
         * y es porque el comprador puede no ser un comprador aún pero lo será después, así que de cogerlo
         * de BUYER, sólo permitiríamos comprar a los que ya han comprado algo y son compradores.
         */

        // Primero creamos las reglas de validación para asegurar los parámetros recibidos
        $rules = [
            'quantity' => 'required|integer|min:1',
        ];

        $this->validate($request, $rules);
        // Comprobamos que el comprador y el vendedor sean personas diferentes
        if($buyer->id == $product->seller_id){
            return $this->errorResponse('El comprador debe ser diferente al vendedor.', 409);
        }
        // Tanto comprador como vendedor son usuarios verificados
        if(!$buyer->esVerificado()){
            return $this->errorResponse('El comprador debe ser un usuario verificado.', 409);
        }
        if(!$product->seller->esVerificado()){
            return $this->errorResponse('El vendedor debe ser un usuario verificado.', 409);
        }
        // El producto ha de estar disponible
        if(!$product->estaDisponible()){
            return $this->errorResponse('El producto elegido para esta transacción no está disponible', 409);
        }
        // La cantidad que se desea comprar no es superior a la cantidad disponible del producto.
        if($product->quantity < $request->quantity){
            return $this->errorResponse('El producto no tiene disponible la cantidad necesaria para la realización de esta transacción', 409);
        }

        // Puede ser que se realicen diversas transacciones sobre un mismo producto al mismo tiempo
        // Para asegurar su correcta disponibilidad usaremos las transacciones de la base de datos:
        /**
         * Lo interesante del uso de las transacciones de la base de datos es que si algo falla en el medio,
         * lo realizado dentro de la función no tendrá efecto puesto que la propia base de datos revertirá 
         * todos los cambios para volver al estado en el que se encontraba.
         */
        return DB::transaction(function () use ($request, $product, $buyer){
            // Primero reducimos la cantidad disponible del producto
            $product->quantity -= $request->quantity;
            $product->save();

            // Ahora creamos la transacción
            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);

            return $this->showOne($transaction, 201);
        });
    }
 }
