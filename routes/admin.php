<?php
/**
 * User: Frank
 * Date: 2018/1/4
 * Time: 16:05
 */
Route::namespace('Admin')->group(function () {
    Route::get(env('ADMIN_PREFIX', ''), 'AdminController@index');
    Route::post('upload', 'UploadController@upload');
    Route::group(['middleware' => ['api', 'cors']], function () {
        Route::post('login', 'AuthController@login');
        Route::get('logout', 'AuthController@logout');

        Route::group(['middleware' => 'auth:admin'], function () {
            Route::get('refreshToken', 'AuthController@refresh');
            Route::get('info', 'AdminController@info');
            Route::get('permissions', 'AdminController@permissions');
            Route::get('uploadParams', 'UploadController@uploadParams');
            // 角色
            Route::get('roles', 'RoleController@lists')->middleware(['permission:role-list']);
            Route::get('role/selectRoles', 'RoleController@selectRoles');
            Route::get('role/{role}', 'RoleController@detail')->middleware(['permission:role-list']);
            Route::put('role/{role}', 'RoleController@update')->middleware(['permission:role-update']);
            Route::post('role', 'RoleController@create')->middleware(['permission:role-create']);
            Route::delete('role/{role}', 'RoleController@destroy')->middleware(['permission:role-delete']);

            // 账号
            Route::get('accounts', 'AccountController@lists')->middleware(['permission:account-list']);
            Route::get('account/{account}', 'AccountController@detail')->middleware(['permission:account-list']);
            Route::put('account/{account}', 'AccountController@update')->middleware(['permission:account-update']);
            Route::post('account', 'AccountController@create')->middleware(['permission:account-create']);
            Route::delete('account/{account}', 'AccountController@destroy')->middleware(['permission:account-delete']);

            Route::post('submitContent', 'AdminController@submitContent');
        });
    });
});