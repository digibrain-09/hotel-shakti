<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TableController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\Table1Controller;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\NationalController;
use App\Http\Controllers\RestaurantReportController;
use App\Http\Controllers\RestaurantUserController;
use App\Http\Controllers\ZomatoController;
use App\Http\Controllers\WaiterController;
use App\Http\Controllers\AddOnController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\KOTController;
use App\Http\Controllers\LocationController;

// Customer Data
use App\Http\Controllers\RoyalPlatterResController;
use App\Http\Controllers\TakeAwayOrderController;

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

// For Front Side
Route::get('/', function () {
    return view('front.index');
});

Route::get('/', [FrontController::class, 'index'])->name('front');
Route::get('/about', [FrontController::class, 'about'])->name('about');
Route::get('/chef', [FrontController::class, 'chef'])->name('chef');
Route::get('/contact', [FrontController::class, 'contact'])->name('contact');
Route::get('/restaurants', [FrontController::class, 'restaurants'])->name('restaurants');
Route::get('/demo', [FrontController::class, 'demo'])->name('demo');
Route::post('/contact', [FrontController::class, 'mail'])->name('mail');
Route::get('/terms_and_conditions', [FrontController::class, 'terms_conditions'])->name('terms_conditions');
Route::get('/privacy_policy', [FrontController::class, 'privacy'])->name('privacy');




Route::post('/customer', [Table1Controller::class, 'customer'])->name('customer');
Route::post('/customer_check', [Table1Controller::class, 'customer_check'])->name('customer_check');


Route::get('/custom_login', [Table1Controller::class, 'custom_login'])->name('custom_login');
Route::get('/custom_logout', [Table1Controller::class, 'custom_logout'])->name('custom_logout');
Route::get('/custom_register', [Table1Controller::class, 'custom_register'])->name('custom_register');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// For Restaurant Admin Side
Route::resource('table', TableController::class);
Route::resource('restaurant', RestaurantController::class);
Route::post('/update-gst-status', [RestaurantController::class, 'updateStatus'])->name('restaurant.update-gst-status');
Route::resource('/restaurant_user', RestaurantUserController::class);
Route::resource('category', App\Http\Controllers\CategoryController::class);
Route::resource('item', ItemsController::class);
Route::resource('add_on', AddOnController::class);
Route::resource('coupons', CouponController::class);
Route::get('/admin_order', [ItemsController::class, 'admin_order'])->name('admin_order');
Route::get('/get-orders/{table_id}', [ItemsController::class, 'getOrders'])->name('getOrders');
Route::get('/get-latest-orders/{table_id}', [ItemsController::class, 'getLatestOrders'])->name('getLatestOrders');
Route::get('/check-orders', [ItemsController::class, 'checkOrders'])->name('checkOrders');
Route::get('/check-waiter-paymentmode', [ItemsController::class, 'checkWaiterPaymentmode'])->name('checkWaiterPaymentmode');

// For manager Admin Side
Route::get('/manager_order', [RestaurantUserController::class, 'manager_order'])->name('manager_order');
Route::get('/get-manager-orders/{table_id}', [RestaurantUserController::class, 'getManagerOrders'])->name('getManagerOrders');
Route::get('/get-latest-manager-orders/{table_id}', [RestaurantUserController::class, 'getLatestManagerOrders'])->name('getLatestManagerOrders');
Route::get('/check-new-orders', [ItemsController::class, 'checkNewOrders'])->name('checkNewOrders');
Route::delete('/remove-order-item/{orderId}/{itemId}', [RestaurantUserController::class, 'RemoveOrderItem'])->name('RemoveOrderItem');
Route::patch('/update-order-quantity', [RestaurantUserController::class, 'UpdateOrderQuantity'])->name('UpdateOrderQuantity');
Route::get('/view-item', [RestaurantUserController::class, 'view_item'])->name('view_item');
Route::post('/update-manager-order', [RestaurantUserController::class, 'UpdateManagerOrder'])->name('UpdateManagerOrder');

// For Kitchen Order Admin Side
Route::get('/kitchen_order', [KitchenController::class, 'index'])->name('kitchen_order');
Route::get('/get-kitchen-orders', [KitchenController::class, 'getKitchenOrders'])->name('getKitchenOrders');
Route::get('/get-latest-kitchen-orders/{table_id}', [KitchenController::class, 'getLatestKitchenOrders'])->name('getLatestKitchenOrders');
Route::get('/check-new-kitchen-orders', [KitchenController::class, 'checkNewKitchenOrders'])->name('checkNewKitchenOrders');
Route::post('/update-Kitchen-order-notified/{orderId}', [KitchenController::class, 'updateKitchenOrderNotified'])->name('updateKitchenOrderNotified');
Route::post('/item-ready-to-served/{orderId}/{itemId}', [KitchenController::class, 'ItemReadyToServed'])->name('ItemReadyToServed');
Route::post('/update-manager-kitchen-order-notified/{orderId}', [KitchenController::class, 'updateManagerKitchenOrderNotified'])->name('updateManagerKitchenOrderNotified');
Route::post('/update-waiter-kitchen-order-notified/{orderId}', [KitchenController::class, 'updateWaiterKitchenOrderNotified'])->name('updateWaiterKitchenOrderNotified');

// Route::get('/kitchen/orders', [KitchenController::class, 'getOrders'])->name('getOrders');
Route::post('/kitchen/update-status', [KitchenController::class, 'updateStatus']);


// Take Away Order Side
Route::get('/take-away-order', [TakeAwayOrderController::class, 'index'])->name('TakeAwayOrder');
Route::post('/complete-order', [TakeAwayOrderController::class, 'CompleteTakeAwayOrder'])->name('CompleteTakeAwayOrder');
Route::get('/view-order', [TakeAwayOrderController::class, 'ViewOrder'])->name('ViewOrder');
Route::get('/view-latest-order', [TakeAwayOrderController::class, 'ViewLatestOrder'])->name('ViewLatestOrder');
Route::get('/view-take-away-invoice', [TakeAwayOrderController::class, 'ViewTakeAwayInvoice'])->name('ViewTakeAwayInvoice');
Route::get('/get-take-away-order-details', [TakeAwayOrderController::class, 'GetTakeAwayDetails'])->name('GetTakeAwayDetails');
Route::get('/search', [TakeAwayOrderController::class, 'SearchSuggestions'])->name('search.take-away-suggestions');
Route::prefix('take-away-order')->group(function () {

    Route::get('/{category_id}', [TakeAwayOrderController::class, 'getOrderItems'])->name('getOrderItems');
    Route::get('/view/{id}', [TakeAwayOrderController::class, 'view'])->name('view');
    Route::post('/add-to-cart', [TakeAwayOrderController::class, 'AddtoCart'])->name('AddtoCart');
    Route::post('/cart', [TakeAwayOrderController::class, 'update'])->name('update');
    Route::get('/cart_view/{id}', [TakeAwayOrderController::class, 'cart_view'])->name('cart_view');
    Route::patch('/cart', [TakeAwayOrderController::class, 'UpdateTakeAwayQuantity'])->name('UpdateTakeAwayQuantity');
    Route::get('/remove-from-cart/{item_id}', [TakeAwayOrderController::class, 'RemoveOrderfromCart'])->name('RemoveOrderfromCart');

    Route::get('/printorder/{id}', [TakeAwayOrderController::class, 'PrintOrder'])->name('PrintOrder');
    Route::post('/order', [TakeAwayOrderController::class, 'TakeAwayOrder'])
        ->name('StoreTakeAwayOrder');
});


Route::get('/check_condition', [ItemsController::class, 'check_condition'])->name('check_condition');
Route::get('/invoice', [ItemsController::class, 'Invoice'])->name('Invoice');
Route::get('/invoice/{table_id}', [ItemsController::class, 'InvoiceData'])->name('InvoiceData');
Route::get('/restaurant_report', [RestaurantReportController::class, 'index'])->name('report');
Route::post('/update-status', [ItemsController::class, 'updateStatus'])->name('items.update-status');
Route::post('/update-area-status', [ItemsController::class, 'updateAreaStatus'])->name('updateAreaStatus');
Route::get('/export', [RestaurantReportController::class, 'exportReport'])->name('exportReport');

Route::post('/update-tax-status', [ItemsController::class, 'updateTaxStatus'])->name('orders.update-tax-status');

Route::get('/kot', [KOTController::class, 'index'])->name('KOT');
Route::get('/kot/{table_id}', [KOTController::class, 'KOTData'])->name('KOTData');
Route::get('/kot/{table_id}/{order_id}', [KOTController::class, 'KOTOrderData'])->name('KOTOrderData');


// For Waiter Side
Route::get('/ordermenu', [WaiterController::class, 'index'])->name('OrderMenu');

Route::prefix('ordermenu')->group(function () {
    // Define static route FIRST
    // Route::get('/cart', [WaiterController::class, 'GotoCart'])->name('GotoCart');
    Route::get('/remove-from-cart/{item_id}', [WaiterController::class, 'RemovefromCart'])->name('RemovefromCart');
    Route::patch('/cart', [WaiterController::class, 'UpdateQuantity'])->name('UpdateQuantity');
    Route::get('/check', [WaiterController::class, 'CheckCondition'])->name('CheckCondition');
    Route::get('/printorder/{id}', [WaiterController::class, 'PrintMyOrder'])->name('PrintMyOrder');
    Route::get('/order_details', [WaiterController::class, 'OrderDetails'])->name('OrderDetails');
    Route::get('/order_details/{table_id}', [WaiterController::class, 'getOrderDetails'])->name('getOrderDetails');
    Route::post('/complete_order', [WaiterController::class, 'CompleteOrder'])->name('CompleteOrder');
    Route::post('/update-payment-method', [WaiterController::class, 'updatePaymentMethod'])->name('updatePaymentMethod');
    Route::post('/add-to-cart', [WaiterController::class, 'AddtoCart'])->name('AddtoCart');
    Route::get('/view/{id}', [WaiterController::class, 'view'])->name('view');
    Route::get('/cart_view/{id}', [WaiterController::class, 'cart_view'])->name('cart_view');
    Route::post('/cart', [WaiterController::class, 'update'])->name('update');

    Route::get('/search-suggestions', [WaiterController::class, 'suggestions'])->name('search.suggestions');

    // Then dynamic routes
    Route::get('/{table_id}', [WaiterController::class, 'getMenu'])->name('getMenu');
    Route::get('/{table_id}/{category_id}', [WaiterController::class, 'getItems'])->name('getItems');

    Route::post('/order', [WaiterController::class, 'Order'])->name('Order');
});



Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');




Route::get('/print-order/{id}', [RestaurantUserController::class, 'PrintOrder'])->name('PrintOrder');
Route::get('/print-invoice-order/{id}', [ItemsController::class, 'PrintInvoiceOrder'])->name('PrintInvoiceOrder');
Route::get('/get-invoice-tax', [ItemsController::class, 'getInvoiceTax'])->name('get.invoice.tax');
Route::post('/download-pdf', [ItemsController::class, 'downloadPdf'])->name('downloadPdf');





// Route::get('smartbi/{table}', [App\Http\Controllers\SmartbiController::class, 'index'])->name('index');

Route::post('/check-email', [RestaurantController::class, 'checkEmail'])->name('check.email');

Route::post('/update-notified/{orderId}', [ItemsController::class, 'updateNotified'])->name('updateNotified');
Route::post('/update-order-notified/{orderId}', [ItemsController::class, 'updateOrderNotified'])->name('updateOrderNotified');
Route::post('/update-print-notified/{orderId}', [ItemsController::class, 'updatePrintNotified'])->name('updatePrintNotified');
Route::post('/update-kitchen-notified/{orderId}', [KitchenController::class, 'updateKitchenNotified'])->name('updateKitchenNotified');
Route::post('/update-kitchen-status/{orderId}', [KitchenController::class, 'updateKitchenStatus'])->name('updateKitchenStatus');





// For Scantable Side
Route::prefix('hotelshakti')->group(function () {
    // Define static routes first
    Route::get('/myorder', [Table1Controller::class, 'myorder'])->name('scantable.myorder');
    Route::get('/mylatestorder', [Table1Controller::class, 'myLatestOrder'])->name('scantable.myLatestOrder');
    Route::get('/cart', [Table1Controller::class, 'cart'])->name('scantable.cart');
    Route::get('/thankyou', [Table1Controller::class, 'ThankYou'])->name('scantable.ThankYou');
    Route::post('/payment', [Table1Controller::class, 'payment'])->name('scantable.payment');
    Route::post('/payment_success', [Table1Controller::class, 'PaymentSuccess'])->name('scantable.PaymentSuccess');
    Route::get('/payment_cancel', [Table1Controller::class, 'PaymentCancel'])->name('scantable.PaymentCancel');
    Route::post('/order', [Table1Controller::class, 'order'])->name('scantable.order');
    Route::post('/case_on_delivery', [Table1Controller::class, 'CaseOnDelivery'])->name('scantable.CaseOnDelivery');
    Route::post('/repeat-order', [Table1Controller::class, 'repeatOrder'])->name('scantable.repeatorder');


    // Now define dynamic routes
    Route::get('/{id}/show-tabs', [Table1Controller::class, 'showTabs'])->name('scantable.showTabs');
    Route::get('/view/{id}', [Table1Controller::class, 'view'])->name('scantable.view');
    Route::get('/cart_view/{id}', [Table1Controller::class, 'cart_view'])->name('scantable.cart_view');
    // Route::get('/cart/{id}', [Table1Controller::class, 'add_to_cart'])->name('scantable.add_to_cart');
    Route::post('/add-to-cart', [Table1Controller::class, 'add_to_cart'])->name('scantable.add_to_cart');
    Route::post('/cart', [Table1Controller::class, 'update'])->name('scantable.update_cart');
    Route::get('/remove-from-cart/{id}', [Table1Controller::class, 'remove'])->name('scantable.remove_from_cart');
    Route::delete('/{id}', [Table1Controller::class, 'tab_close'])->name('scantable.tab_close');
    Route::get('/invoice-print/{payment_id}', [Table1Controller::class, 'InvoicePrint'])->name('scantable.InvoicePrint');

    Route::get('/scantable-table', [Table1Controller::class, 'showTable'])->name('scantable.table');

    Route::post('/verify-location', [LocationController::class, 'verify'])->name('scantable.verifyLocation');


    // The LAST route should be the dynamic `{table}`
    Route::get('/{table}', [Table1Controller::class, 'index'])->name('scantable.index');
});

Route::middleware('within.restaurant')->group(function () {
    Route::post('/add-to-cart', [Table1Controller::class, 'add_to_cart'])->name('scantable.add_to_cart');
    Route::post('/cart', [Table1Controller::class, 'update'])->name('scantable.update_cart');
    Route::post('/order', [Table1Controller::class, 'order'])->name('scantable.order');
    Route::post('/payment', [Table1Controller::class, 'payment'])->name('scantable.payment');
    Route::post('/case_on_delivery', [Table1Controller::class, 'CaseOnDelivery'])->name('scantable.CaseOnDelivery');
    Route::post('/repeat-order', [Table1Controller::class, 'repeatOrder'])->name('scantable.repeatorder');
});


// For Royalplatter Side
Route::prefix('royalplatter')->group(function () {
    // Define static routes first
    Route::get('/myorder', [RoyalPlatterResController::class, 'myorder'])->name('royalplatter.myorder');
    Route::get('/mylatestorder', [RoyalPlatterResController::class, 'myLatestOrder'])->name('royalplatter.myLatestOrder');
    Route::get('/cart', [RoyalPlatterResController::class, 'cart'])->name('royalplatter.cart');
    Route::get('/thankyou', [RoyalPlatterResController::class, 'ThankYou'])->name('royalplatter.ThankYou');
    Route::post('/payment', [RoyalPlatterResController::class, 'payment'])->name('royalplatter.payment');
    Route::post('/payment_success', [RoyalPlatterResController::class, 'PaymentSuccess'])->name('royalplatter.PaymentSuccess');
    Route::get('/payment_cancel', [RoyalPlatterResController::class, 'PaymentCancel'])->name('royalplatter.PaymentCancel');
    Route::post('/order', [RoyalPlatterResController::class, 'order'])->name('royalplatter.order');
    Route::post('/case_on_delivery', [RoyalPlatterResController::class, 'CaseOnDelivery'])->name('royalplatter.CaseOnDelivery');

    // Now define dynamic routes
    Route::get('/{id}/show-tabs', [RoyalPlatterResController::class, 'showTabs'])->name('royalplatter.showTabs');
    Route::get('/view/{id}', [RoyalPlatterResController::class, 'view'])->name('royalplatter.view');
    Route::get('/cart/{id}', [RoyalPlatterResController::class, 'add_to_cart'])->name('royalplatter.add_to_cart');
    Route::patch('/cart', [RoyalPlatterResController::class, 'update'])->name('royalplatter.update_cart');
    Route::get('/remove-from-cart/{id}', [RoyalPlatterResController::class, 'remove'])->name('royalplatter.remove_from_cart');
    Route::delete('/{id}', [RoyalPlatterResController::class, 'tab_close'])->name('royalplatter.tab_close');
    Route::get('/invoice-print/{payment_id}', [RoyalPlatterResController::class, 'InvoicePrint'])->name('royalplatter.InvoicePrint');

    Route::get('/royalplatter-table', [RoyalPlatterResController::class, 'showTable'])->name('royalplatter.table');

    // The LAST route should be the dynamic `{table}`
    Route::get('/{table}', [RoyalPlatterResController::class, 'index'])->name('royalplatter.index');
});




Route::get('/data', [Table1Controller::class, 'data'])->name('scantable.data');

Route::get('/categories', [ZomatoController::class, 'getCategories']);

Route::get('/report-chart', [RestaurantReportController::class, 'salesChart'])->name('sales.chart');
Route::get('/report-chart/export', [RestaurantReportController::class, 'exportSalesChart'])->name('sales.chart.export');

Route::post('/apply-coupon', [CouponController::class, 'apply'])->name('coupon.apply');
Route::post('/waiter-apply-coupon', [WaiterController::class, 'apply'])->name('waiter.coupon_apply');
Route::post('/remove-coupon', [CouponController::class, 'removeCoupon'])->name('coupon.remove');
Route::post('/coupons/check-code', [CouponController::class, 'checkCode'])->name('coupons.checkCode');


Route::post('/save-subscription', [\App\Http\Controllers\PushSubscriptionController::class, 'saveSubscription'])
    ->middleware('auth');

Route::post(
    '/verify-order-location',
    [LocationVerificationController::class, 'verify']
)->name('verify.order.location');
