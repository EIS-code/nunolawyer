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

    Route::group(['middleware' => ['permission:article_purpose_access']], function () {
        Route::resources(['article_purpose' => 'ArticlePurposeController']);
    });

    Route::group(['middleware' => ['permission:poa_access']], function () {
        Route::resources(['poa' => 'PoaController']);
        Route::get('poa/{id}/download', 'PoaController@download')->name('poa.download');
    });

    Route::group(['middleware' => ['permission:account_access']], function () {
        Route::resources(['account' => 'AccountController']);
    });

    Route::group(['middleware' => ['permission:follow_up_access']], function () {
        Route::resources(['follow_up' => 'FollowUpController']);
    });

    Route::group(['middleware' => ['permission:translate_model_document_access']], function () {
        Route::resources(['translate_model_document' => 'TranslateModelDocumentController']);
    });

    Route::group(['middleware' => ['permission:roles_access']], function () {
        Route::resources(['roles' => 'RoleController']);
    });

    Route::group(['middleware' => ['permission:our_fee_policy_document_access']], function () {
        Route::resources(['our_fee_policy_document' => 'OurFeePolicyDocumentController']);
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

