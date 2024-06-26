<?php

use App\Http\Controllers\FauxController;
use App\Http\Controllers\MonController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

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
    return view('auth.login');
});

Route::get('ajoute_user', function(){
    return view('auth.register');
});

Route::post('/viens',[MonController::class, 'viens'])->name('viens');


Route::get('/tableau',[MonController::class, 'montrer'])->name('tableau');

Route::get('/faux',[MonController::class, 'faux'])->name('faux');

Route::get('/details/{CT_Num}', [MonController::class, 'details']);
Route::get('/facturesClient/{CT_Num}', [MonController::class, 'facturesClient']);


Route::get('/time', [FauxController::class, 'time']);

Route::get('/fusion', [MonController::class,'fusion']);

Route::post('/enregistrer-ligne', [MonController::class,'enregistrerLigne'])->name('enregistrer_ligne');

Route::post('/enregistrer_commentaire', [MonController::class,'enregistrer_commentaire'])->name('enregistrer_commentaire');

Route::get('/verifier_donnees', [MonController::class,'verifierDonnees']);

Route::get('/client_recouvre/{id}', [MonController::class,'client_recouvre']);

Route::get('/client_rappel/{id}', [MonController::class,'client_rappel']);
Route::delete('/supprimer-ligne/{id}', [MonController::class,'supprimer_ligne'])->name('supprimer_ligne');

Route::delete('/factures_recouvrees/{idClient}', [UserController::class, 'factures_recouvrees'])->name("factures_recouvrees");

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::namespace('')->prefix('admin')->name('admin.')->middleware('can:manage-users')->group(function () {
    Route::resource('users', UserController::class, [
        'names' => [
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'show' => 'users.show',
            'edit' => 'users.edit',
            'update' => 'users.update',
            'destroy' => 'users.destroy',
        ]
    ]);
});
