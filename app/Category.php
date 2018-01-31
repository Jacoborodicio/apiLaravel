<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Atributos que pueden ser asignados de manera masiva
    protected $fillable = [
        'name',
        'description',
    ];

    // Relacción muchos a muchos implementa belong to many
    public function products(){
        return $this->belongsToMany(Product::class);
    }
}
