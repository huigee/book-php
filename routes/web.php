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

// Route::get('/', function () {
//     return view('login');
// });
Route::get('/', 'View\MemberController@toLogin');
Route::get('/login', 'View\MemberController@toLogin');
Route::get('/register', 'View\MemberController@toRegister');

Route::get('/category', 'View\BookController@toCategory');
Route::get('/product/category_id/{category_id}', 'View\BookController@toProduct');
Route::get('/product/{product_id}', 'View\BookController@toPdtContent');
Route::get('/pay', function (){
    return view('alipay');
});

//Route::post('service/register', 'Service\MemberController@register');
//Route::post('service/login', 'Service\MemberController@login');
//Route::get('service/validate_code/create', 'Service\ValidateController@create');
//Route::get('service/category/parent_id/{parent_id}', 'Service\BookController@getCategoryByParentId');
//Route::get('service/cart/add/{parent_id}', 'Service\CartController@addCart');

Route::prefix('service')->group(function () {
    Route::get('category/parent_id/{parent_id}', 'Service\BookController@getCategoryByParentId');
    Route::get('cart/add/{product_id}', 'Service\CartController@addCart');
    Route::get('cart/delete', 'Service\CartController@deleteCart');

    Route::post('register', 'Service\MemberController@register');
    Route::post('login', 'Service\MemberController@login');
    Route::get('validate_code/create', 'Service\ValidateController@create');
    Route::get('validate_phone/send', 'Service\ValidateController@sendSMS');


    Route::post('alipay', 'Service\PayController@aliPay');
    Route::post('pay/ali_notify', 'Service\PayController@aliNotify');
    Route::get('pay/ali_return', 'Service\PayController@aliReturn');


    Route::post('wxpay', 'Service\PayController@wxPay');
    Route::post('pay/wx_notify', 'Service\PayController@wxNotify');
    Route::get('openid/get', 'Service\PayController@getOpenid');

    Route::post('upload/{type}', 'Service\UploadController@uploadFile');
});


Route::middleware('check.login')->group(function () {
    Route::get('/cart', 'View\CartController@toCart');

    Route::post('/order_commit', 'View\OrderController@toOrderCommit');
    Route::get('/order_list', 'View\OrderController@toOrderList');

});

Route::prefix('admin')->group(function () {
    Route::get('index', 'Admin\IndexController@index');
    Route::get('welcome', 'Admin\IndexController@welcome');
    Route::get('login', 'Admin\IndexController@toLogin');
    Route::get('member', 'Admin\IndexController@toMember');

    Route::get('category', 'Admin\CategoryController@toCategory');
    Route::get('category_add', 'Admin\CategoryController@toCategoryAdd');
    Route::get('category_edit', 'Admin\CategoryController@toCategoryEdit');

    Route::get('product', 'Admin\ProductController@toProduct');
    Route::get('product_add', 'Admin\ProductController@toProductAdd');
    Route::get('product_info', 'Admin\ProductController@toProductInfo');

    Route::prefix('service')->group(function () {
        Route::post('login', 'Admin\IndexController@login');

        Route::post('category/add', 'Admin\CategoryController@categoryAdd');
        Route::post('category/edit', 'Admin\CategoryController@categoryEdit');
        Route::post('category/edit', 'Admin\CategoryController@categoryEdit');
        Route::post('category/delete', 'Admin\CategoryController@categoryDel');

        Route::post('product/add', 'Admin\ProductController@productAdd');
        Route::post('product/delete', 'Admin\ProductController@productDelete');
    });
});