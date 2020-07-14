<?php

use Illuminate\Support\Facades\Route;

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
Auth::routes(['reset' => setting('auth.forgot_password'), 'register' => setting('auth.allow_registration')]);

Route::get('/', 'DashboardController@index')->name('dashboard');

$middlewares = ['auth'];

if (setting('auth.email_verification')) {
    $middlewares[] = 'verified';
}

Route::middleware($middlewares)->group(function() {
    Route::group(['middleware' => ['permission:clients_access']], function () {
        Route::resources(['clients' => 'ClientController']);
        Route::get('clients/{id}/ban', 'ClientController@banClient')->name('clients.ban');
        Route::get('clients/{id}/activity', 'ClientController@activityLog')->name('clients.activity');
        Route::get('clients/{id}/print', 'ClientController@print')->name('clients.print');
    });

    Route::group(['middleware' => ['permission:editors_access']], function () {
        Route::get('editors/create', 'ClientController@create')->name('editors.create');
        Route::get('editors/{id}/edit', 'ClientController@edit')->name('editors.edit');
        Route::get('editors/{id}', 'ClientController@show')->name('editors.show');
        Route::get('editors/{id}/activity', 'ClientController@activityLog')->name('editors.activity');
        // Route::get('editors', 'EditorController@index')->name('editors.index');
        // Route::get('editors/{id}/show', 'ClientController@show')->name('editors.show');
        Route::resources(['editors' => 'EditorController'], ['except' => 'create']);
        Route::patch('editors/{id}/update', 'ClientController@update')->name('editors.update');
        Route::delete('editors/{id}/destroy', 'ClientController@destroy')->name('editors.destroy');
        Route::get('editors/{id}/print', 'ClientController@print')->name('editors.print');
    });

    Route::group(['middleware' => ['permission:roles_access']], function () {
        Route::resources(['roles' => 'RoleController']);
    });

    Route::group(['middleware' => ['permission:permissions_access']], function () {
        Route::resources(['permissions' => 'PermissionController']);
    });

    Route::group(['middleware' => ['permission:activitylog_access']], function () {
        Route::get('activitylog', 'ActivityLogController@index')->name('activitylog.index');
        Route::get('activitylog/{id}', 'ActivityLogController@show')->name('activitylog.show');
        Route::delete('activitylog/{id}', 'ActivityLogController@destroy')->name('activitylog.destroy');
    });
});

