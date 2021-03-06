<?php

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

Route::get('/', function () {
    dd(phpinfo());
    return view('welcome');
});

Route::get('/dropzone', function () {
    return view('example/dropzone', [
        'code' => file_get_contents(resource_path('assets/js/dropzone.js'))
    ]);
});

Route::middleware(['auth.apikey'])->post('upload', 'DependencyUploadController@uploadFile');