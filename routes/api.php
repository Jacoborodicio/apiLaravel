<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

/*
 * Buyers
 */
Route::resource('buyers', 'Buyer\BuyerController', ['only' => ['index', 'show']]);
// Lista de las transacciones de un comprador
// Ruta del estilo -> http://127.0.0.1:8000/buyers/2/transactions
Route::resource('buyers.transactions', 'Buyer\BuyerTransactionController', ['only' => ['index']]);
// Lista de productos relaccionados con un comprador específico
// Ruta del estilo -> http://127.0.0.1:8000/buyers/2/products
Route::resource('buyers.products', 'Buyer\BuyerProductController', ['only' => ['index']]);

// Lista de todos los vendedores relaccionados con un comrador
// Ruta del estilo -> http://127.0.0.1:8000/buyers/89/sellers
Route::resource('buyers.sellers', 'Buyer\BuyerSellerController', ['only' => ['index']]);


// Lista de todas las categorías para las que un comprador ha realizado compras
// Ruta del estilo -> http://127.0.0.1:8000/buyers/89/categories
Route::resource('buyers.categories', 'Buyer\BuyerCategoryController', ['only' => ['index']]);
/*
 * Categories
 */
Route::resource('categories', 'Category\CategoryController', ['except' => ['create', 'edit']]);


// Lista de todos los productos asociados a una categoría dada.
// Ruta del estilo -> http://127.0.0.1:8000/categories/1/products
Route::resource('categories.products', 'Category\CategoryProductController', ['only' => ['index']]);
// Lista de todos los vendedores para una categoría específica
// Ruta del estilo -> http://127.0.0.1:8000/categories/13/sellers
Route::resource('categories.sellers', 'Category\CategorySellerController', ['only' => ['index']]);
// Lista de transacciones que se han efectuado para una categoría específica
// Ruta del estilo -> http://127.0.0.1:8000/categories/13/transactions
Route::resource('categories.transactions', 'Category\CategoryTransactionController', ['only' => ['index']]);
// Lista de compradores de una categoría específica
// Ruta del estilo -> http://127.0.0.1:8000/categories/13/buyers
Route::resource('categories.buyers', 'Category\CategoryBuyerController', ['only' => ['index']]);

/*
 * Products
 */
Route::resource('products', 'Product\ProductController', ['only' => ['index', 'show']]);
// Lista las transacciones para un determinado producto
// Ruta del estilo -> http://127.0.0.1:8000/products/99/transactions
Route::resource('products.transactions', 'Product\ProductTransactionController', ['only' => ['index']]);
// Lista los compradores de un producto específico
// Ruta del estilo -> http://127.0.0.1:8000/products/911/buyers
Route::resource('products.buyers', 'Product\ProductBuyerController', ['only' => ['index']]);
// Lista todas las categorías para un producto dado
// Ruta del estilo -> http://127.0.0.1:8000/products/11/categories
Route::resource('products.categories', 'Product\ProductCategoryController', ['only' => ['index', 'update', 'destroy']]);
/**
 * Permite crear instancias de transacciones. Es especial puesto que relaciona tres recursos (Product, Buyer, Transaction).
 * Product: saber el producto que se comprará. Buyer: Quién lo comprará. Transaction: Crear la instancia.
 * 
 */
// Ruta del estilo -> http://127.0.0.1:8000/products/150/buyers/2/transactions
Route::resource('products.buyers.transactions', 'Product\ProductBuyerTransactionController', ['only' => ['store']]);


/*
 * Transactions
 */
Route::resource('transactions', 'Transaction\TransactionController', ['only' => ['index', 'show']]);
// Lista de las categorías relativas a una transacción específica
// Ruta del estilo de -> http://127.0.0.1:8000/transactions/10/categories
Route::resource('transactions.categories', 'Transaction\TransactionCategoryController', ['only' => ['index']]);
// Lista el vendedor de una determinada transacción
// A pesar de que sabemos que retornará un único vendedor, mantenemos sellers en el resource por consistencia
// Ruta del estilo -> http://127.0.0.1:8000/transactions/10/sellers
Route::resource('transactions.sellers', 'Transaction\TransactionSellerController', ['only' => ['index']]);

/*
 * Sellers
 */
Route::resource('sellers', 'Seller\SellerController', ['only' => ['index', 'show']]);


Route::resource('sellers.transactions', 'Seller\SellerTransactionController', ['only' => ['index']]);

// Lista de categorías en las que un vendedor ha realizado alguna transacción o venta.
// Ruta del estilo -> http://127.0.0.1:8000/sellers/5/categories
Route::resource('sellers.categories', 'Seller\SellerCategoryController', ['only' => ['index']]);
// Lista de los compradores de un vendedor específico
// Ruta del estilo -> http://127.0.0.1:8000/sellers/5/buyer
Route::resource('sellers.buyers', 'Seller\SellerBuyerController', ['only' => ['index']]);

// Lista los productos de un vendedor específico
// Ruta del estilo -> http://127.0.0.1:8000/sellers/33/products/1002 (esto para update)
Route::resource('sellers.products', 'Seller\SellerProductController', ['except' => ['create', 'edit', 'show']]);
/*
 * Users
 */
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);
