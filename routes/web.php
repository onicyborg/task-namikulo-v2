<?php

use App\Http\Controllers\Area\CalendarController;
use App\Http\Controllers\Area\ClientController;
use App\Http\Controllers\Area\DashboardController;
use App\Http\Controllers\Area\TaskController;
use App\Http\Controllers\Area\TaskCategoryController;
use App\Http\Controllers\Area\WorkerController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Area\ProfilController;
use App\Http\Controllers\TaskDraftController;
use App\Http\Controllers\ThemePreferenceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(
    function () {
        Route::get('/', [LoginController::class, 'index'])->name('login');
        Route::post('/auth', [LoginController::class, 'auth']);
    }
);


Route::group(['middleware' => ['auth', 'preventBackHistory']], function () {

    Route::group(['middleware' => ['roleCheck:Admin,Worker']], function () {
        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::get('/client', [ClientController::class, 'index']);
        Route::get('/client/list', [ClientController::class, 'list']);
        Route::get('/client/search', [ClientController::class, 'search']);
        Route::get('/client/detail/{id}', [ClientController::class, 'detail']);
        Route::post('/client/add', [ClientController::class, 'add']);
        Route::post('/client/edit', [ClientController::class, 'edit']);
        Route::post('/client/delete', [ClientController::class, 'delete']);

        Route::get('/worker', [WorkerController::class, 'index']);
        Route::get('/worker/search', [WorkerController::class, 'search']);
        Route::get('/worker/list-active', [WorkerController::class, 'listActive']);
        Route::get('/worker/list-delete', [WorkerController::class, 'listDelete']);
        Route::get('/worker/detail/{id}', [WorkerController::class, 'detail']);
        Route::post('/worker/add', [WorkerController::class, 'add']);
        Route::post('/worker/edit', [WorkerController::class, 'edit']);
        Route::post('/worker/delete', [WorkerController::class, 'delete']);

        Route::get('/task', [TaskController::class, 'index']);
        Route::get('/task/metopen', [TaskController::class, 'metopen']);
        Route::get('/task/artikel-ilmiah', [TaskController::class, 'artikelIlmiah']);
        Route::get('/task/get-by-date', [TaskController::class, 'getByDate']);
        Route::get('/task/export', [TaskController::class, 'export']);
        Route::get('/task/config', [TaskController::class, 'config']);
        Route::get('/task/list', [TaskController::class, 'list']);
        Route::get('/task/detail/{id}', [TaskController::class, 'detail']);
        Route::post('/task/add', [TaskController::class, 'add']);
        Route::post('/task/edit', [TaskController::class, 'edit']);
        Route::post('/task/edit-pay', [TaskController::class, 'editPay']);
        Route::post('/task/edit-status', [TaskController::class, 'editStatus']);
        Route::post('/task/delete', [TaskController::class, 'delete']);
        Route::patch('/task/{id}/checklist', [TaskController::class, 'updateChecklist']);

        Route::post('/task/add-file', [TaskController::class, 'addFile']);
        Route::post('/task/edit-file', [TaskController::class, 'editFile']);
        Route::post('/task/delete-file', [TaskController::class, 'deleteFile']);

        Route::middleware('roleCheck:Admin')->group(function () {
            Route::get('/task-category', [TaskCategoryController::class, 'index']);
            Route::post('/task-category/add', [TaskCategoryController::class, 'add']);
            Route::post('/task-category/edit', [TaskCategoryController::class, 'edit']);
            Route::post('/task-category/delete', [TaskCategoryController::class, 'delete']);

            Route::get('/task-request', [TaskDraftController::class, 'adminIndex']);
            Route::get('/task-request/list', [TaskDraftController::class, 'list']);
            Route::get('/task-request/detail/{id}', [TaskDraftController::class, 'detail']);
            Route::post('/task-request/{id}/assign', [TaskDraftController::class, 'assign']);
        });

        Route::get('/calendar', [CalendarController::class, 'index']);

        Route::get('/profil', [ProfilController::class, 'index']);
        Route::post('/profil/edit', [ProfilController::class, 'edit']);

        Route::get('/theme/preferences', [ThemePreferenceController::class, 'show']);
        Route::post('/theme/preferences', [ThemePreferenceController::class, 'update']);
    });
});

Route::get('/request-task', [TaskDraftController::class, 'create']);
Route::get('/request-task/client-lookup', [TaskDraftController::class, 'lookupClient']);
Route::post('/request-task', [TaskDraftController::class, 'store']);
