<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    // Atributos que pueden ser asignados de manera masiva
    protected $fillable = [
        'name',
        'description',
    ];

    // Para ocultar el atributo pivote que laravel crea automáticamente
    protected $hidden = [
        'pivot'
    ];
    // Relacción muchos a muchos implementa belong to many
    public function products(){
        return $this->belongsToMany(Product::class);
    }
}
