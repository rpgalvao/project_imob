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

    /** Página de Destaque */
    Route::get('/destaque', 'WebController@spotlight')->name('spotlight');

    /** Página de Locação */
    Route::get('/quero-alugar', 'WebController@rent')->name('rent');

    /** Página de Locação Específica de um Imóvel*/
    Route::get('/quero-alugar/{slug}', 'WebController@rentProperty')->name('rentProperty');

    /** Página de Compra */
    Route::get('/quero-comprar', 'WebController@buy')->name('buy');

    /** Página de Compra Específica de um Imóvel*/
    Route::get('/quero-comprar/{slug}', 'WebController@buyProperty')->name('buyProperty');

    /** Página de Experiência */
    Route::get('/experiencias', 'WebController@experiences')->name('experiences');

    /** Página de Experiência Individual por Categoria*/
    Route::get('/experiencias/{slug}', 'WebController@experienceCategory')->name('experienceCategory');

    /** Página de Filtro */
    Route::match(['post', 'get'], '/filtro', 'WebController@filter')->name('filter');

    /** Página de Contato */
    Route::get('/contato', 'WebController@contact')->name('contact');
    Route::post('/contato/sendEmail', 'WebController@sendEmail')->name('sendEmail');
    Route::get('/contato/sucesso', 'WebController@sendEmailSuccess')->name('sendEmailSuccess');

});

Route::group(['prefix' => 'component', 'namespace' => 'Web', 'as' => 'component.'], function () {

    Route::post('main-filter/search', 'FilterController@search')->name('main-filter.search');
    Route::post('main-filter/category', 'FilterController@category')->name('main-filter.category');
    Route::post('main-filter/type', 'FilterController@type')->name('main-filter.type');
    Route::post('main-filter/neighborhood', 'FilterController@neighborhood')->name('main-filter.neighborhood');
    Route::post('main-filter/bedrooms', 'FilterController@bedrooms')->name('main-filter.bedrooms');
    Route::post('main-filter/suites', 'FilterController@suites')->name('main-filter.suites');
    Route::post('main-filter/bathrooms', 'FilterController@bathrooms')->name('main-filter.bathrooms');
    Route::post('main-filter/garage', 'FilterController@garage')->name('main-filter.garage');
    Route::post('main-filter/base-price', 'FilterController@basePrice')->name('main-filter.basePrice');
    Route::post('main-filter/limit-price', 'FilterController@limitPrice')->name('main-filter.limitPrice');

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

        /** Perfis (Roles) */
        Route::get('/role/{role}/permissions', 'ACL\\RoleController@permissions')->name('role.permissions');
        Route::put('/role/{role}/permissions/sync', 'ACL\\RoleController@permissionsSync')->name('role.permissionsSync');
        Route::resource('/role', 'ACL\\RoleController');

        /** Permissions */
        Route::resource('/permission', 'ACL\\PermissionController');

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
