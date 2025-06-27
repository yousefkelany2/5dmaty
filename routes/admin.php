<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\UserController;

Route::get('/dasbord', function () {
    return view("dashbord.layout.main");
});

Route::resource("user",UserController::class);
