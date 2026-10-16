<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::middleware([
    'cache.headers:no_store;no_cache;must_revalidate;max_age=0',
])->group(static function (): void {
   
    Route::controller(LoginController::class)
        ->prefix('/auth')
        ->group(static function (): void {
            
            Route::get('/login', 'showForm')->name('login');
            Route::post('/login', 'authenticate')->name('authentication');
            Route::post('/logout', 'logout')->name('logout');
        });

    
    Route::middleware(['auth'])->group(static function (): void {
        Route::controller(ProductController::class)
            ->prefix('/products')
            ->name('products.')
            ->group(static function (Router $router): void {
                $prefixName = \array_last($router->getGroupStack())['as'];

                Route::any('', static fn () => redirect()->route("{$prefixName}list"))->name('index');
                Route::get('', 'list')->name('list');
                Route::post('', 'create')->name('create');
                Route::get('/create', 'showCreateForm')->name('create-form');

                Route::prefix('/{product}')
                    ->group(static function (): void {
                        Route::get('', 'view')->name('view');
                        Route::post('', 'update')->name('update');
                        Route::get('/update', 'showUpdateForm')->name('update-form');
                        Route::post('/delete', 'delete')->name('delete');

                        Route::prefix('/shops')
                            ->group(static function (): void {
                                Route::get('', 'viewShops')->name('view-shops');
                                Route::post('', 'addShop')->name('add-shop');
                                Route::get('/add', 'showAddShopsForm')->name('add-shops-form');
                                Route::post('/remove', 'removeShop')->name('remove-shop');
                            });
                    });
            });

        Route::controller(ShopController::class)
            ->prefix('/shops')
            ->name('shops.')
            ->group(static function (Router $router): void {
                $prefixName = \array_last($router->getGroupStack())['as'];

                Route::any('', static fn () => redirect()->route("{$prefixName}list"))->name('index');
                Route::get('', 'list')->name('list');
                Route::post('', 'create')->name('create');
                Route::get('/create', 'showCreateForm')->name('create-form');

                Route::prefix('/{shop}')
                    ->group(static function (): void {
                        Route::get('', 'view')->name('view');
                        Route::post('', 'update')->name('update');
                        Route::get('/update', 'showUpdateForm')->name('update-form');
                        Route::post('/delete', 'delete')->name('delete');

                        Route::prefix('/products')
                            ->group(static function (): void {
                                Route::get('', 'viewProducts')->name('view-products');
                                Route::post('', 'addProduct')->name('add-product');
                                Route::get('/add', 'showAddProductsForm')->name('add-products-form');
                                Route::post('/remove', 'removeProduct')->name('remove-product');
                            });
                    });
            });

        Route::controller(CategoryController::class)
            ->prefix('/categories')
            ->name('categories.')
            ->group(static function (Router $router): void {
                $prefixName = \array_last($router->getGroupStack())['as'];

                Route::any('', static fn () => redirect()->route("{$prefixName}list"))->name('index');
                Route::get('', 'list')->name('list');
                Route::post('', 'create')->name('create');
                Route::get('/create', 'showCreateForm')->name('create-form');

                Route::prefix('/{category}')
                    ->group(static function (): void {
                        Route::get('', 'view')->name('view');
                        Route::post('', 'update')->name('update');
                        Route::get('/update', 'showUpdateForm')->name('update-form');
                        Route::post('/delete', 'delete')->name('delete');

                        Route::prefix('/products')
                            ->group(static function (): void {
                                Route::get('', 'viewProducts')->name('view-products');
                                Route::post('', 'addProduct')->name('add-product');
                                Route::get('/add', 'showAddProductsForm')->name('add-products-form');
                            });
                    });
            });

        Route::controller(UserController::class)
            ->prefix('/users')
            ->name('users.')
            ->group(static function (Router $router): void {
                $prefixName = \array_last($router->getGroupStack())['as'];

                Route::any('', static fn () => redirect()->route("{$prefixName}list"))->name('index');
                Route::get('', 'list')->name('list');
                Route::post('', 'create')->name('create');
                Route::get('/create', 'showCreateForm')->name('create-form');

               
                Route::prefix('/self')
                    ->group(static function (): void {
                        Route::get('', 'viewSelf')->name('selves.view');
                        Route::post('', 'updateSelf')->name('selves.update');
                        Route::get('/update', 'showUpdateSelfForm')->name('selves.update-form');
                    });

                Route::prefix('/{user}')
                    ->group(static function (): void {
                        Route::get('', 'view')->name('view');
                        Route::post('', 'update')->name('update');
                        Route::get('/update', 'showUpdateForm')->name('update-form');
                        Route::post('/delete', 'delete')->name('delete');
                    });
            });
    });
});
