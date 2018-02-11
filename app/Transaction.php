<?php

namespace App;

use App\Product;
use App\Buyer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'quantity',
        'buyer_id',
        'product_id',
    ];

    // Tiene claves foráneas hacia el comprador y hacia el producto así que:
    // una transacción pertenece a un comprador y a un producto.
    public function product(){
        return $this->belongsTo(Product::class);
    }
    
    public function buyer(){
        return $this->belongsTo(Buyer::class);
    }
}
