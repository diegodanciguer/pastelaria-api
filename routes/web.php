<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar as rotas web para sua aplicação. Essas
| rotas são carregadas pelo RouteServiceProvider dentro de um grupo que
| contém o middleware "web". Aproveite para criar algo incrível!
|
*/

Route::get('/', function () {
    return view('welcome');
});
