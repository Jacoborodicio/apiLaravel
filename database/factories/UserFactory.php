<?php

use App\User;
use App\Category;
use App\Product;
use App\Transaction;
use App\Seller;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    static $password;
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'verified' => $verificado = $faker->randomElement([User::USUARIO_VERIFICADO, User::USUARIO_NO_VERIFICADO]),
        'verification_token' => $verificado == User::USUARIO_VERIFICADO ? null : User::generarVerificationToken(),
        'admin' => $faker->randomElement([User::USUARIO_ADMINISTRADOR, User::USUARIO_REGULAR]),

    ];
});

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker-> word,
        'description' => $faker->paragraph(1),
    ];
});

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker-> word,
        'description' => $faker->paragraph(1),
        'quantity' => $faker->numberBetween(1,10),
        'status' => $faker->randomElement([Product::PRODUCTO_DISPONIBLE, Product::PRODUCTO_NO_DISPONIBLE]),
        'image' => $faker->randomElement(['1.jpeg', '2.jpg', '3jpg', '4.jpeg', '5.jpg']),
        // 'seller_id' => User::inRandomOrder()->first()->id,
        // La siguiente línea es equivalente a la anterior, hacemos esto, porque el usuario aleatorio tiene
        // que existir, por eso no cualquier integer, sino uno que sabemos que existe
        'seller_id' => User::all()->random()->id,
    ];
});

$factory->define(Transaction::class, function (Faker $faker) {
    /**
     *  Aquí nos surge un nuevo problema, y es que, una de nuestras restricciones es
     * que no se puedan comprar productos "a sí mismos" por lo que, entonces, debemos hacer el seeder
     * contemplando que el id del producto es un producto vendido por un usuario diferente al id del comprador.
     */
    
    $vendedor = Seller::has('products')->get()->random();
    $comprador = User::all()->except($vendedor->id)->random();

    /**
     * Como vemos, con las dos líneas anteriores, primero escogemos aletoriamente un vendedor y 
     * a continuación escogemos cualquier usuario, puesto que cualquier usuario puede comprar, pero 
     * restringiendo a que ese usuario no sea el mismo que vende el producto.
     */

    return [
        'quantity' => $faker->numberBetween(1,3),
        'buyer_id' => $comprador->id,
        // Accedemos a la lista de productos de ese vendedor
        'product_id' => $vendedor->products->random()->id,
    ];
});