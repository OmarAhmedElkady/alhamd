<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\Customer\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CheckLoginController;
use Illuminate\Support\Facades\Route;



///////////////////////////////////Start PAGINATE Route/////////////////////////////////////
define('PAGINATE_USER' , 10) ;
define('PAGINATE_CATEGORIES' , 10) ;
define('PAGINATE_PRODUCTS' , 10) ;
define('PAGINATE_LANGUAGE' , 10) ;
define('PAGINATE_CUSTOMERS' , 10) ;
define('PAGINATE_ORDERS' , 10) ;

define('PAGINATE_PHOTO_USER' , base_path() . '\assets\admin\img\user\\') ;
define('PAGINATE_IMAGE_PRODUCT' , base_path() . '\assets\admin\img\products\\') ;
///////////////////////////////////End PAGINATE Route/////////////////////////////////////




Route::group(['middleware' => 'auth'] , function (){



    ///////////////////////////////////Start DashboarD Route/////////////////////////////////////
    Route::group(['prefix' => 'Dashboard' , 'middleware' => 'role:super_admin'] , function() {

        Route::get('/' , [DashboardController::class , 'index'])->name('admin.Dashboard.index') ;
    })  ;

    ///////////////////////////////////End Language Route/////////////////////////////////////



    ///////////////////////////////////Start User Route/////////////////////////////////////
    Route::group(['prefix' => 'users'] , function() {

        Route::group(['middleware' => 'permission:users-read'] , function(){
            Route::get('/' , [UserController::class , 'index'])->name('admin.users.index') ;
        }) ;


        Route::group(['middleware' => 'permission:users-create'] , function(){
            Route::get('/create' , [UserController::class , 'create'])->name('admin.users.create') ;
            Route::post('/store' , [UserController::class , 'store'])->name('admin.users.store') ;
        }) ;

        // Route::group(['middleware' => 'permission:users-update'] , function(){
            Route::get('/edit/{id}' , [UserController::class , 'edit'])->name('admin.users.edit') ;
            Route::post('/update/{id}' , [UserController::class , 'update'])->name('admin.users.update') ;
        // }) ;


        Route::post('/delete' , [UserController::class , 'delete'])->name('admin.users.delete') ;
    })  ;
    ///////////////////////////////////End User Route/////////////////////////////////////



    ///////////////////////////////////Start Categories Route/////////////////////////////////////
    Route::group(['prefix' => 'categories'] , function() {

        Route::group(['middleware' => 'permission:categories-read'] , function(){
            Route::get('/' , [CategoryController::class , 'index'])->name('admin.category.index') ;
        }) ;


        Route::group(['middleware' => 'permission:categories-create'] , function(){
            Route::get('/create' , [CategoryController::class , 'create'])->name('admin.category.create') ;
            Route::post('/store' , [CategoryController::class , 'store'])->name('admin.category.store') ;
        }) ;

        Route::group(['middleware' => 'permission:categories-update'] , function(){
            Route::get('/edit/{id}' , [CategoryController::class , 'edit'])->name('admin.category.edit') ;
            Route::post('/update/{id}' , [CategoryController::class , 'update'])->name('admin.category.update') ;
        }) ;


        Route::group(['middleware' => 'permission:categories-delete'] , function(){
            Route::post('/delete' , [CategoryController::class , 'delete'])->name('admin.category.delete') ;
        }) ;
    })  ;
    ///////////////////////////////////End Categories Route/////////////////////////////////////




    ///////////////////////////////////Start products Route/////////////////////////////////////
    Route::group(['prefix' => 'products'] , function() {

        Route::group(['middleware' => 'permission:products-read'] , function(){
            Route::get('/' , [ProductController::class , 'index'])->name('admin.product.index') ;
            Route::get('/show/{category_id}' , [ProductController::class , 'show'])->name('admin.product.show') ;

        }) ;
        Route::get('/search' , [ProductController::class , 'search'])->name('admin.product.search') ;


        Route::group(['middleware' => 'permission:products-create'] , function(){
            Route::get('/create' , [ProductController::class , 'create'])->name('admin.product.create') ;
            Route::post('/store' , [ProductController::class , 'store'])->name('admin.product.store') ;
        }) ;

        Route::group(['middleware' => 'permission:products-update'] , function(){
            Route::get('/edit/{id}' , [ProductController::class , 'edit'])->name('admin.product.edit') ;
            Route::post('/update' , [ProductController::class , 'update'])->name('admin.product.update') ;
        }) ;


        Route::group(['middleware' => 'permission:products-delete'] , function(){
            Route::post('/delete' , [ProductController::class , 'delete'])->name('admin.product.delete') ;
        }) ;
    })  ;
    ///////////////////////////////////End products Route/////////////////////////////////////







    ///////////////////////////////////Start Customers Route/////////////////////////////////////
    Route::group(['prefix' => 'customers'] , function() {

        Route::group(['middleware' => 'permission:customers-read'] , function(){
            Route::get('/' , [CustomerController::class , 'index'])->name('admin.customer.index') ;

        }) ;
        Route::get('/search' , [CustomerController::class , 'search'])->name('admin.customer.search') ;


        Route::group(['middleware' => 'permission:customers-create'] , function(){
            Route::get('/create' , [CustomerController::class , 'create'])->name('admin.customer.create') ;
            Route::post('/store' , [CustomerController::class , 'store'])->name('admin.customer.store') ;
        }) ;

        Route::group(['middleware' => 'permission:customers-update'] , function(){
            Route::get('/edit/{id}' , [CustomerController::class , 'edit'])->name('admin.customer.edit') ;
            Route::post('/update' , [CustomerController::class , 'update'])->name('admin.customer.update') ;
        }) ;


        Route::group(['middleware' => 'permission:customers-delete'] , function(){
            Route::post('/delete' , [CustomerController::class , 'delete'])->name('admin.customer.delete') ;
        }) ;
    })  ;
    ///////////////////////////////////End Customers Route/////////////////////////////////////





        ///////////////////////////////////Start Customers Order Route/////////////////////////////////////
        Route::group(['prefix' => 'orders'] , function() {

            Route::group(['middleware' => 'permission:orders-read'] , function(){
                Route::get('/' , [OrderController::class , 'index'])->name('admin.order.index') ;

            }) ;
            Route::get('/search' , [OrderController::class , 'search'])->name('admin.order.search') ;


            Route::group(['middleware' => 'permission:orders-create'] , function(){
                Route::get('/create/{TranslationOf}' , [OrderController::class , 'create'])->name('admin.order.create') ;
                Route::post('/store' , [OrderController::class , 'store'])->name('admin.order.store') ;
            }) ;

            Route::group(['middleware' => 'permission:orders-update'] , function(){
                Route::get('/edit/{id}' , [OrderController::class , 'edit'])->name('admin.order.edit') ;
                Route::post('/update' , [OrderController::class , 'update'])->name('admin.order.update') ;
            }) ;


            Route::group(['middleware' => 'permission:orders-delete'] , function(){
                Route::post('/delete' , [OrderController::class , 'delete'])->name('admin.order.delete') ;
            }) ;
        })  ;
        ///////////////////////////////////End Customers Order Route/////////////////////////////////////


        ///////////////////////////////////Start Customers Order Route/////////////////////////////////////
        Route::group(['prefix' => 'all-orders'] , function() {

            Route::group(['middleware' => 'permission:orders-read'] , function(){
                Route::get('/' , [AdminOrderController::class , 'index'])->name('admin.all_order.index') ;

                Route::get('/show/{id}' , [AdminOrderController::class , 'show'])->name('admin.all_order.show') ;
                Route::post('/status' , [AdminOrderController::class , 'status'])->name('admin.all_order.status') ;
            }) ;

            Route::get('/search' , [AdminOrderController::class , 'search'])->name('admin.all_order.search') ;

            Route::group(['middleware' => 'permission:orders-delete'] , function(){
                Route::post('/delete' , [AdminOrderController::class , 'delete'])->name('admin.all_order.delete') ;
            }) ;
        })  ;
        ///////////////////////////////////End Customers Order Route/////////////////////////////////////





    ///////////////////////////////////Start Languages Route/////////////////////////////////////
    Route::group(['prefix' => 'language' , 'middleware' => ['role:super_admin']] , function() {

        Route::get('/' , [LanguageController::class , 'index'])->name('admin.language.index') ;


        Route::get('/create' , [LanguageController::class , 'create'])->name('admin.language.create') ;
        Route::post('/store' , [LanguageController::class , 'store'])->name('admin.language.store') ;

        Route::get('/edit/{id}' , [LanguageController::class , 'edit'])->name('admin.language.edit') ;
        Route::post('/update/{id}' , [LanguageController::class , 'update'])->name('admin.language.update') ;


        Route::post('/delete' , [LanguageController::class , 'delete'])->name('admin.language.delete') ;


    })  ;
    Route::get('language/{locale}' , [LanguageController::class , 'selectLanguage'])->name('admin.language.selectLanguage') ;
    ///////////////////////////////////End Languages Route/////////////////////////////////////


}) ;

