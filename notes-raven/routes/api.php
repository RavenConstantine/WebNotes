<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\SettingsController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test',function(){
    $i = substr_count(Storage::get('public/main_notes.log'),PHP_EOL)+1;
    return $i;
});

Route::prefix('note')->controller(NotesController::class)->group(function () {
    Route::get('/load','Load');
    Route::get('/create','Create');
    Route::get('/open/{id}','Open');
    Route::get('/save/{id}/{title}/{text}','Save');
    Route::get('/delete/{id}','Delete');
    
    Route::prefix('story')->group(function () {
        Route::get('/getcount/{id}','GetStoryCount');
        Route::get('/load/{id}','LoadStory');
        Route::get('/open/{id}/{date}','OpenStory');
    });
});

Route::prefix('settings')->controller(SettingsController::class)->group(function () {
    Route::get('/newloglenght/{value}','NewLogLenght');
    Route::get('/newautosave/{value}','NewAutoSave');
    Route::get('/test/{key}/{value}','SetEnvValue');
});
