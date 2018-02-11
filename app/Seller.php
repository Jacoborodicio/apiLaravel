<?php

namespace App;

use App\Product;

class Seller extends User
{
    protected static function boot(){
        parent::boot();

        static::addGlobalScope(new \App\Scopes\SellerScope);
    }
    // Está extendiendo (heredando) de user, por lo que no tiene 
    // atributos aquí.


    // Un vendedor tiene muchos productos
    public function products(){
        return $this->hasMany(Product::class);
    }
}
