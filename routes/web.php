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

Route::group(['namespace' => 'Web', 'as' => 'web.'], function () {

    /** Página Principal */
    Route::get('/', 'WebController@home')->name('home');

    /** Página de Locação */
    Route::get('/quero-alugar', 'WebController@rent')->name('rent');

    /** Página de Locação Específica de um Imóvel*/
    Route::get('/quero-alugar/{slug}', 'WebController@rentProperty')->name('rentProperty');

    /** Página de Compra */
    Route::get('/quero-comprar', 'WebController@buy')->name('buy');

    /** Página de Compra Específica de um Imóvel*/
    Route::get('/quero-comprar/{slug}', 'WebController@buyProperty')->name('buyProperty');

    /** Página de Filtro */
    Route::get('/filtro', 'WebController@filter')->name('filter');

    /** Página de Contato */
    Route::get('/contato', 'WebController@contact')->name('contact');

});

Route::group(['prefix' => 'component', 'namespace' => 'Web', 'as' => 'component.'], function () {

    Route::post('main-filter/search', 'FilterController@search')->name('main-filter.search');

});

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.'], function () {

    /** Formulário de Login */
    Route::get('/', 'AuthController@showLoginForm')->name('login');
    Route::post('login', 'AuthController@login')->name('login.do');

    /** Rotas Protegidas */
    Route::group(['middleware' => ['auth']], function () {

        /** Dashboard Home */
        Route::get('home', 'AuthController@home')->name('home');

        /** Users */
        Route::get('users/team', 'UserController@team')->name('users.team');
        Route::resource('users', 'UserController');

        /** Company */
        Route::resource('companies', 'CompanyController');

        /** Property */
        Route::post('properties/set-cover-image', 'PropertyController@setCoverImage')->name('properties.setCoverImage');
        Route::delete('properties/remove-image', 'PropertyController@removeImage')->name('properties.removeImage');
        Route::resource('properties', 'PropertyController');

        /** Contract */
        Route::post('contracts/get-owner-info', 'ContractController@getOwnerInfo')->name('contracts.getOwnerInfo');
        Route::post('contracts/get-acquirer-info', 'ContractController@getAcquirerInfo')->name('contracts.getAcquirerInfo');
        Route::post('contracts/get-property-info', 'ContractController@getPropertyInfo')->name('contracts.getPropertyInfo');
        Route::resource('contracts', 'ContractController');
    });

    /** Logout */
    Route::get('logout', 'AuthController@logout')->name('logout');

});
