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

//Заметки
Route::prefix('note')->controller(NotesController::class)->group(function () {
    Route::get('/load','Load');
    Route::get('/create','Create');
    Route::get('/open/{id}','Open');
    Route::get('/save/{id}/{title}/{text}','Save');
    Route::get('/delete/{id}','Delete');
    //История заметок
    Route::prefix('story')->group(function () {
        Route::get('/getcount/{id}','GetStoryCount');
        Route::get('/load/{id}','LoadStory');
        Route::get('/open/{id}/{date}','OpenStory');
    });
});
//Настройки
Route::prefix('settings')->controller(SettingsController::class)->group(function () {
    Route::get('/loglenght/{value}','LogLenght');
    Route::get('/autosave/{value}','AutoSave');
});
