<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    app()->setLocale('sk');
    return view('home', ['locale' => 'sk']);
})->name('home');

Route::get('/sk', function () {
    app()->setLocale('sk');
    return view('home', ['locale' => 'sk']);
})->name('home.sk');

Route::get('/cs', function () {
    app()->setLocale('cs');
    return view('home', ['locale' => 'cs']);
})->name('home.cs');

Route::get('/cz', function () {
    return redirect('/cs');
});

Route::get('/en', function () {
    app()->setLocale('en');
    return view('home', ['locale' => 'en']);
})->name('home.en');

Route::get('/hello-world', function () {
    return view('hello-world');
});

Route::get('/glb-viewer', function () {
    return view('glb-viewer-assembly');
});
