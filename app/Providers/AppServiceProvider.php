<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Para arreglar el problema de la longitud de los string en ciertas bases de datos
        // como MariaDB.
        Schema::defaultStringLength(191);

        \App\Product::updated(function($product){
            if ($product->quantity == 0 && $product->estaDisponible()) {
                $product->status = \App\Product::PRODUCTO_NO_DISPONIBLE;
                $product->save();
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
