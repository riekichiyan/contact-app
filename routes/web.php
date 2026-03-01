<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactAdminController;

/*
|--------------------------------------------------------------------------
| Contact Routes（ゲストOK）
|--------------------------------------------------------------------------
*/

Route::get('/contacts/create', [ContactController::class, 'create'])->name('contacts.create');
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contacts.confirm');
Route::post('/contacts/store', [ContactController::class, 'store'])->name('contacts.store');
Route::get('/contacts/thanks', [ContactController::class, 'thanks'])->name('contacts.thanks');



/*
|--------------------------------------------------------------------------
| Auth Routes（Breeze）
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Admin Routes（ログイン必須）
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/contacts', [ContactAdminController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/export', [ContactAdminController::class, 'export'])->name('contacts.export');
    Route::get('/contacts/{contact}', [ContactAdminController::class, 'show'])->name('contacts.show');
    Route::delete('/contacts/{contact}', [ContactAdminController::class, 'destroy'])->name('contacts.destroy');
    Route::get('/dashboard', function () {
        return redirect('/admin/contacts');
    })->name('dashboard');
});
