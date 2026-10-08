<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// GLB Models API
Route::get('/models', function () {
    $modelsPath = public_path('3d_objects');
    $models = [];
    
    if (is_dir($modelsPath)) {
        $files = array_diff(scandir($modelsPath), ['.', '..']);
        foreach ($files as $file) {
            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'glb') {
                $models[] = [
                    'name' => pathinfo($file, PATHINFO_FILENAME),
                    'path' => '/3d_objects/' . rawurlencode($file),
                    'file' => $file
                ];
            }
        }
    }
    
    sort($models);
    return response()->json($models);
});
