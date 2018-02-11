<?php

use App\User;
use App\Product;
use App\Category;
use App\Transaction;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Primero borramos "truncamos", i.e. borramos toda la información
         * de las tablas, NO las tablas, para evitar sobrecarga e inconsistencia.
         * Para evitar "quejas" de inconsistencia, anularemos por ahora la verificación
         * de las claves foráneas.
         */

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::truncate();
        Product::truncate();
        Transaction::truncate();
        Category::truncate();
        // Como no tenemos modelo para la tabla pivote, lo hacemos accediendo mediante DB
        DB::table('category_product')->truncate();

        /**
         * Algo importante dentro del seeder es: Como en nuestro caso tenemos diversos disparadores en la base de datos,
         * como ocurre al momento de crear un usuario, que se le envía automáticamente un email, entonces, si ejecutáramos el seeder
         * se envíarian 1000 emails en este caso, uno por usuario creado. Para EVITAR este "peligro", podemos desactivar todos los disparadores
         * de eventos para nuestros modelos mediante flush:
         */

        User::flushEventListeners();
        Category::flushEventListeners();
        Product::flushEventListeners();
        Transaction::flushEventListeners();

        // Cantidades a generar
        $cantidadUsuarios = 1000;
        $cantidadCategorias = 30;
        $cantidadProductos = 1000;
        $cantidadTransacciones = 1000;


        // Procedemos a llamar a los factories
        factory(User::class, $cantidadUsuarios)->create();
        factory(Category::class, $cantidadCategorias)->create();
        /**
         * Con los productos es diferente, puesto que al momento de crear un producto,
         * vamos a asociarlo directamente con las categorías a las que este pertenecerá.
         * Para la implementación de la asociación de los elementos que son de muchos a muchos
         * laravel utiliza el método attach, que recibe un array con la lista de todos los ids, en este caso,
         * de todas las categorías que asignaremos a ese producto.
         * El método pluk nos permite obtener únicamente el id de las colecciones, por lo que $categorias contendrá
         * así solamente los ids de las categorías. Finalmente lo asociamos.
         */
        factory(Product::class, $cantidadProductos)->create()->each(
            function ($producto) {
                $categorias = Category::all()->random(mt_rand(1, 5))->pluck('id');
                $producto->categories()->attach($categorias);
            }
        );
        factory(Transaction::class, $cantidadTransacciones)->create();

    }
}
