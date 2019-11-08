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

            // 店铺
            Route::get('shops', 'ShopController@lists')->middleware(['permission:shop-list']);
            Route::get('shop/options', 'ShopController@options');
            Route::get('shop/{shop}', 'ShopController@detail')->middleware(['permission:shop-list']);
            Route::put('shop/{shop}', 'ShopController@update')->middleware(['permission:shop-update']);
            Route::post('shop', 'ShopController@create')->middleware(['permission:shop-create']);
            Route::delete('shop/{shop}', 'ShopController@destroy')->middleware(['permission:shop-delete']);

            // 订单
            Route::get('orders', 'OrderController@lists')->middleware(['permission:order-list']);
            Route::get('order/export', 'OrderController@export')->middleware(['permission:order-list']);
            Route::get('order/{order}', 'OrderController@detail');
            Route::put('order/{order}', 'OrderController@update')->middleware(['permission:order-update']);
            Route::post('order', 'OrderController@create')->middleware(['permission:order-create']);
            Route::delete('order/{order}', 'OrderController@destroy')->middleware(['permission:order-delete']);
            Route::post('order/import', 'OrderController@import')->middleware(['permission:order-import']);

            // 订单商品
            Route::get('orderGoodses', 'OrderGoodsController@lists')->middleware(['permission:order-goods-list']);
            Route::get('orderGoods/export', 'OrderGoodsController@export')->middleware(['permission:order-goods-export']);
            Route::get('orderGoods/{orderGoods}', 'OrderGoodsController@detail');
            Route::put('orderGoods/match', 'OrderGoodsController@match')->middleware(['permission:order-goods-match']);
            Route::put('orderGoods/{orderGoods}', 'OrderGoodsController@update')->middleware(['permission:order-goods-update']);
            Route::delete('orderGoods/{orderGoods}', 'OrderGoodsController@destroy')->middleware(['permission:order-goods-delete']);

            // 订单物流
            Route::get('orderLogisticses', 'OrderLogisticsController@lists')->middleware(['permission:order-logistics-list']);
            Route::get('orderLogistics/export', 'OrderLogisticsController@export')->middleware(['permission:order-logistics-export']);
            Route::get('orderLogistics/{orderLogistics}', 'OrderLogisticsController@detail');
            Route::put('orderLogistics/match', 'OrderLogisticsController@match')->middleware(['permission:order-logistics-match']);
            Route::put('orderLogistics/{orderLogistics}', 'OrderLogisticsController@update')->middleware(['permission:order-logistics-update']);
            Route::delete('orderLogistics/{orderLogistics}', 'OrderLogisticsController@destroy')->middleware(['permission:order-logistics-delete']);

            // 订单退款
            Route::get('orderRefunds', 'OrderRefundController@lists')->middleware(['permission:order-refund-list']);
            Route::get('orderRefund/export', 'OrderRefundController@export')->middleware(['permission:order-refund-export']);
            Route::get('orderRefund/{orderRefund}', 'OrderRefundController@detail');
            Route::put('orderRefund/{orderRefund}', 'OrderRefundController@update')->middleware(['permission:order-refund-update']);
            Route::delete('orderRefund/{orderRefund}', 'OrderRefundController@destroy')->middleware(['permission:order-refund-delete']);

            // 物流
            Route::get('logisticses', 'LogisticsController@lists')->middleware(['permission:logistics-list']);
            Route::get('logistics/export', 'LogisticsController@export')->middleware(['permission:order-logistics-export']);
            Route::put('logistics/{logistics}', 'LogisticsController@update')->middleware(['permission:logistics-update']);
            Route::post('logistics', 'LogisticsController@create')->middleware(['permission:logistics-create']);
            Route::delete('logistics/{logistics}', 'LogisticsController@destroy')->middleware(['permission:logistics-delete']);
            Route::post('logistics/import', 'LogisticsController@import')->middleware(['permission:logistics-import']);

            // 采购
            Route::get('purchases', 'PurchasesController@lists')->middleware(['permission:purchases-list']);
            Route::get('purchase/export', 'PurchasesController@export')->middleware(['permission:purchases-export']);
            Route::put('purchase/{purchase}', 'PurchasesController@update')->middleware(['permission:purchases-update']);
            Route::post('purchase', 'PurchasesController@create')->middleware(['permission:purchases-create']);
            Route::delete('purchase/{purchase}', 'PurchasesController@destroy')->middleware(['permission:purchases-delete']);
            Route::post('purchase/import', 'PurchasesController@import')->middleware(['permission:purchases-import']);

            // 其他费用
            Route::get('otherFees', 'OtherFeeController@lists')->middleware(['permission:other-fee-list']);
            Route::get('otherFee/{otherFee}', 'OtherFeeController@detail')->middleware(['permission:other-fee-list']);
            Route::put('otherFee/{otherFee}', 'OtherFeeController@update')->middleware(['permission:other-fee-update']);
            Route::post('otherFee', 'OtherFeeController@create')->middleware(['permission:other-fee-create']);
            Route::delete('otherFee/{otherFee}', 'OtherFeeController@destroy')->middleware(['permission:other-fee-delete']);

            // 结算
            Route::get('settlements', 'SettlementController@lists')->middleware(['permission:settlement-list']);
            Route::get('settlement/items', 'SettlementController@items')->middleware(['permission:settlement-items']);
            Route::post('settlement/confirm', 'SettlementController@confirm')->middleware(['permission:settlement-confirm']);
            Route::post('settlement/return/{settlement}', 'SettlementController@settlementReturn')->middleware(['permission:settlement-return']);

            // 导入
            Route::get('imports', 'ImportController@lists')->middleware(['permission:import-list']);
            Route::delete('import/{import}', 'ImportController@destroy')->middleware(['permission:import-delete']);

            // 导出
            Route::get('exports', 'ExportController@lists')->middleware(['permission:export-list']);
            Route::delete('export/{export}', 'ExportController@destroy')->middleware(['permission:export-delete']);
        });
    });
});