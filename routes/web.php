<?php

use App\Http\Controllers\CheckLoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [CheckLoginController::class , 'CheckLogin' ]);

Auth::routes(['register' => false]);

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


///////////////////////////////////////////Start User Login And Exit///////////////////////////////////////////
Route::get('/CheckLogin' , [CheckLoginController::class , 'CheckLogin'])->name('users.CheckLogin') ;

Route::get('/log-out' , [CheckLoginController::class , 'logOut'])->name('users.log_out') ;
///////////////////////////////////////////End User Login And Exit///////////////////////////////////////////
