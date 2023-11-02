<?php

use App\Http\Controllers\ActiveSearchController;
use App\Http\Controllers\AtmosScoringController;
use App\Http\Controllers\MultiSearchController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReferalController;
use App\Http\Controllers\SellerAuthController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\XMLController;
use App\Http\Middleware\CheckRedirects;
use App\Http\Middleware\ForceLowercaseUrls;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use TCG\Voyager\Facades\Voyager;

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

Route::post('/multisearch/ajax', [MultiSearchController::class, 'index'])->name('multisearch');

Route::post('/card/get/info', [AtmosScoringController::class, 'getToken'])->name('getToken');
Route::get('/searchindex', [ActiveSearchController::class, 'searchindex'])->name('active.searchindex');
Route::get('/searchable', [ActiveSearchController::class, 'search'])->name('active.search');

Route::get('/xml/multisearch', [XMLController::class, 'three']);
Route::get('/myfeed', [XMLController::class, 'xmlgoogle']);

Route::post('/addmysearch', [ActiveSearchController::class, 'addmysearch'])->name('active.addmysearch');

// Voyager admin routes
Route::namespace('Voyager')->name('voyager.')->prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/status/activate', 'StatusController@activate')->name('status.activate');
    Route::get('/status/deactivate', 'StatusController@deactivate')->name('status.deactivate');

    // product attributes
    Route::get('/products/{product}/attributes/edit', 'VoyagerProductController@attributesEdit')->name('products.attributes.edit');
    Route::post('/products/{product}/attributes', 'VoyagerProductController@attributesUpdate')->name('products.attributes.update');

    // product groups
    Route::get('/product_groups/{product_group}/settings', 'VoyagerProductGroupController@settings')->name('product_groups.settings');
    Route::put('/product_groups/{product_group}/attributes/update', 'VoyagerProductGroupController@attributesUpdate')->name('product_groups.attributes.update');
    Route::put('/product_groups/{product_group}/attribute-values/update', 'VoyagerProductGroupController@attributeValuesUpdate')->name('product_groups.attribute_values.update');
    Route::get('/product_groups/{product_group}/products/create', 'VoyagerProductGroupController@productsCreate')->name('product_groups.products.create');
    Route::get('/product_groups/{product_group}/products', 'VoyagerProductGroupController@productsIndex')->name('product_groups.products.index');
    Route::post('/product_groups/{product_group}/products', 'VoyagerProductGroupController@productsStore')->name('product_groups.products.store');
    Route::post('/product_groups/{product_group}/products/{product}/detach', 'VoyagerProductGroupController@productsDetach')->name('product_groups.products.detach');


    // orders
    Route::post('/orders/{order}/delivery/store', 'VoyagerOrderController@deliveryStore')->name('orders.delivery.store');
    Route::post('/orders/{order}/refund/store', 'VoyagerOrderController@refundStore')->name('orders.refund.store');
    Route::post('/orders/{order}/status/update', 'VoyagerOrderController@statusUpdate')->name('orders.status.update');

    // import
    Route::get('/import', 'ImportController@index')->name('import.index');
    Route::post('/import/products', 'ImportController@products')->name('import.products');
    Route::post('/import/smartup/products', 'ImportController@smartupProducts')->name('import.smartup.products');

    // export
    Route::get('/export', 'ExportController@index')->name('export.index');
    Route::get('/export/products/store', 'ExportController@productsStore')->name('export.products.store');
    Route::get('/export/products/store/full', 'ExportController@productsStoreFull')->name('export.products.store.full');
    Route::post('/export/products/download', 'ExportController@productsDownload')->name('export.products.download');

    // exportorders
    Route::get('/exportorders', 'ExportOrdersController@index')->name('exportorders.index');
    Route::get('/exportorders/products/store', 'ExportOrdersController@productsStore')->name('exportorders.products.store');
    Route::get('/exportorders/products/store/full', 'ExportOrdersController@productsStoreFull')->name('exportorders.products.store.full');
    Route::post('/exportorders/products/download', 'ExportOrdersController@productsDownload')->name('exportorders.products.download');

    // download subscribers
    Route::get('/subscribers/download', 'VoyagerSubscriberController@download')->name('subscribers.download');

    // user
    Route::get('/users/{user}/api_tokens', 'VoyagerUserController@apiTokens')->name('users.api_tokens');
    Route::post('/users/{user}/api_tokens', 'VoyagerUserController@apiTokensStore')->name('users.api_tokens.store');
    Route::put('/users/{id}/edit', 'VoyagerUserController@editUser')->name('users.edit2');
    Route::get('/users/{user}/installment-info', 'VoyagerUserController@installmentInfo')->name('users.installment_info');
    Route::post('/users/{user}/installment-info/verify', 'VoyagerUserController@installmentInfoVerify')->name('users.installment_info.verify');

    // order-items
    Route::post('/order-items/{order_item}/cancel', 'VoyagerOrderItemController@cancel')->name('order-items.cancel');
});
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

// telegram bot
Route::post('telegram-bot-nWq723bZP7x5cfF', "TelegramBotController@index")->name('telegram-bot');
Route::get('telegram-bot/sethook-nWq723bZP7x5cfF', "TelegramBotController@sethook")->name('telegram-bot.sethook');
Route::get('telegram-bot/deletehook-nWq723bZP7x5cfF', "TelegramBotController@deletehook")->name('telegram-bot.deletehook');

// Payment
Route::post('paycom-xBrGbjU2RyaNwBY', 'PaymentGatewayController@paycom')->name('payment-gateway.paycom');
Route::any('click-SVNfd45qbr5dW9b/prepare', 'PaymentGatewayController@click')->name('payment-gateway.click.prepare');
Route::any('click-SVNfd45qbr5dW9b/complete', 'PaymentGatewayController@click')->name('payment-gateway.click.complete');
Route::any('zoodpay-fzYHsjSXMJ5U2G6q', 'PaymentGatewayController@zoodpay')->name('payment-gateway.zoodpay');
Route::any('atmos-mps42w88sxw6san', 'PaymentGatewayController@atmos')->name('payment-gateway.atmos');

// testing
Route::get('testing-29szTThmfP35dFx', 'TestingController@index')->name('testing.index');

// alifshop azo
Route::post('alifshop/azo/clients/check', "AlifshopAzoController@clientsCheck")->name('alifshop.azo.clients.check');

// synchronization
// Route::post('synchro/torgsoft-LYtkVn6MhH2TqdhK', 'SynchroController@torgsoft')->name('synchro.torgsoft');
// Route::get('synchro/torgsoft', 'SynchroController@torgsoft')->name('synchro.torgsoft.get');

// Localized site routes
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ /*'localeSessionRedirect', */'localizationRedirect', 'localeViewPath', 'localize', ForceLowercaseUrls::class, CheckRedirects::class  ]
    ],  function() {

    /** ADD ALL LOCALIZED ROUTES INSIDE THIS GROUP **/
    // Route::group(['middleware' => ['auth']], function() {

    // home page
    Route::get('/', "HomeController@index")->name('home');
    Route::get('/home/latest-products/{category}', "HomeController@latestProducts")->name('home.latest-products');

    // zoodpay create transaction
    Route::post('zoodpay/transaction/store', 'ZoodpayController@transactionStore')->name('zoodpay.transaction.store');

    // sitemap
    Route::get('/sitemap/index', "SitemapController@index")->name('sitemap.index');

    // search
    Route::get('search', "SearchController@index")->name('search');
    Route::get('search/ajax', "SearchController@ajax")->name('search.ajax');

    // contacts
    Route::get('contacts', "ContactController@index")->name('contacts');
    Route::post('contacts/send', "ContactController@send")->name('contacts.send');
    Route::post('contacts/send/installment-payment', "ContactController@sendInstallmentPayment")->name('contacts.send.installment-payment');

    // subscriber
    Route::post('subscriber/subscribe', "SubscriberController@subscribe")->name('subscriber.subscribe');
    Route::get('subscriber/unsubscribe', "SubscriberController@unsubscribe")->name('subscriber.unsubscribe');

    // category view
    Route::get('categories', "CategoryController@index")->name('categories');
    Route::get('category/{category}-{slug}', "CategoryController@show")->name('category');

    // brand view
    Route::get('brands', "BrandController@index")->name('brands.index');
    Route::get('brand/{brand}-{slug}', "BrandController@show")->name('brands.show');

    Route::get('brand_category/{brand_id}-{category_id}', "CategoryController@individual")->name('category.individual');

    Route::get('sale_category/{category_id}', "ProductController@individualForSale")->name('product.individualForSale');

    // product view
    Route::get('product/{product}-{slug}', "ProductController@view")->name('product');
    Route::get('product/{product}-{slug}/print', "ProductController@print")->name('product.print');
    Route::get('sale', "ProductController@sale")->name('sale');
    Route::get('featured', "ProductController@featured")->name('featured');
    Route::get('new-products', "ProductController@newProducts")->name('new-products');
    Route::get('bestsellers', "ProductController@bestsellers")->name('bestsellers');
    Route::get('latest-viewed', "ProductController@latestViewed")->name('latest-viewed');
    Route::get('promotional-products', "ProductController@promotionalProducts")->name('promotional-products');

    // products routes
    Route::get('products/create', "ProductController@create")->name('products.create');
    Route::post('products', "ProductController@store")->name('products.store');
    Route::get('products/{product}/edit', "ProductController@edit")->name('products.edit');
    Route::put('products/{product}/', "ProductController@update")->name('products.update');
    Route::delete('products/{product}/', "ProductController@destroy")->name('products.destroy');

    // product attributes
    Route::get('products/{product}/attributes/edit', "ProductController@attributesEdit")->name('products.attributes.edit');
    Route::post('products/{product}/attributes', 'ProductController@attributesUpdate')->name('products.attributes.update');

    // reviews
    Route::post('reviews/store', "ReviewController@store")->name('reviews.store');

    // polls
    Route::get('polls', "PollController@index")->name('polls');
    Route::get('polls/{poll}', "PollController@show")->name('polls.show');
    Route::post('polls/{poll}/vote', "PollController@vote")->name('polls.vote');

    // publications pages
    Route::get('news', "PublicationController@news")->name('news');
    Route::get('articles', "PublicationController@articles")->name('articles');
    Route::get('promotions', "PublicationController@promotions")->name('promotions');
    // Route::get('allgood-video', "PublicationController@videos")->name('allgood-video');
    // Route::get('events', "PublicationController@events")->name('events');
    // Route::get('faq', "PublicationController@faq")->name('faq');
    // Route::get('competitions', "PublicationController@competitions")->name('competitions');
    // Route::get('projects', "PublicationController@projects")->name('projects');
    // Route::get('ads', "PublicationController@ads")->name('ads');
    // Route::get('mass-media', "PublicationController@massMedia")->name('mass-media');
    // Route::get('useful-links', "PublicationController@usefulLinks")->name('useful-links');
    Route::get('publications/{publication}-{slug}', "PublicationController@show")->name('publications.show');
    Route::get('publications/{publication}/increment/views', "PublicationController@incrementViews")->name('publications.increment.views');
    Route::get('publications/{publication}-{slug}/print', "PublicationController@print")->name('publications.print');

    // banner statistics routes
    Route::get('banner/{banner}/increment/clicks', "BannerController@incrementClicks")->name('banner.increment.clicks');
    Route::get('banner/{banner}/increment/views', "BannerController@incrementViews")->name('banner.increment.views');
    // });

    // cart routes
    Route::get('cart','CartController@index')->name('cart.index');
    Route::get('cart/checkout','CartController@checkout')->name('cart.checkout');
    Route::post('cart','CartController@add')->name('cart.add');
    Route::post('cart/update','CartController@update')->name('cart.update');
    Route::delete('cart/{id}','CartController@delete')->name('cart.delete');
    Route::post('cart/conditions','CartController@addCondition')->name('cart.addCondition');
    Route::delete('cart/conditions','CartController@clearCartConditions')->name('cart.clearCartConditions');
    Route::get('cart/debug','CartController@debug')->name('cart.debug');

    // wishlist routes
    Route::get('wishlist','WishlistController@index')->name('wishlist.index');
    Route::post('wishlist','WishlistController@add')->name('wishlist.add');
    Route::delete('wishlist/{id}','WishlistController@delete')->name('wishlist.delete');

    // compare routes
    Route::get('compare','CompareController@index')->name('compare.index');
    Route::post('compare','CompareController@add')->name('compare.add');
    Route::delete('compare/{id}','CompareController@delete')->name('compare.delete');

    // order routes
    Route::get('order/{order}-{check}','OrderController@show')->name('order.show');
    Route::get('order/{order}-{check}/print','OrderController@print')->name('order.print');
    Route::post('order','OrderController@add')->name('order.add');
    Route::post('order-attempt','OrderController@attempt')->name('order.attempt');

    // profile routes
    Route::get('profile', "ProfileController@show")->name('profile.show');
    Route::get('profile/edit', "ProfileController@edit")->name('profile.edit');
    Route::put('profile', "ProfileController@update")->name('profile.update');
    Route::post('profile/password', "ProfileController@password")->name('profile.password');
    Route::get('profile/orders', "ProfileController@orders")->name('profile.orders');
    Route::get('profile/products', "ProfileController@products")->name('profile.products');
    Route::get('profile/notifications', "ProfileController@notifications")->name('profile.notifications.index');
    Route::get('profile/documents', "ProfileController@documents")->name('profile.documents.index');
    Route::put('profile/documents/update/passport', "ProfileController@documentsUpdatePassport")->name('profile.documents.documentsUpdatePassport');
    Route::put('profile/documents/update/card', "ProfileController@documentsUpdateCard")->name('profile.documents.documentsUpdateCard');
    Route::get('profile/notifications/{notification}', "ProfileController@notificationsShow")->name('profile.notifications.show');
    Route::get('profile/shop/edit', "ProfileController@shopEdit")->name('profile.shop.edit');
    Route::put('profile/shop', "ProfileController@shopUpdate")->name('profile.shop.update');
    Route::get('profile/request-seller-status', "ProfileController@requestSellerStatus")->name('profile.request-seller-status');

    Route::group(['middleware' => ['auth']], function() {
        Route::get('addresses/{address}/status/{status}', 'AddressController@statusUpdate')->name('addresses.status.update');
        Route::resource('addresses', 'AddressController');
    });

    // shop routes
    Route::get('shops', "ShopController@index")->name('shop.index');
    Route::get('shop/{shop}', "ShopController@show")->name('shop.show');

    // auth routes
    // Auth::routes(['verify' => true]);
    Auth::routes(['verify' => false]);

    // custom auth routes (phone registration)
    Route::get('register/verify', 'Auth\RegisterController@showRegistrationVerifyForm')->name('register.verify');
    Route::post('register/verify', 'Auth\RegisterController@registerVerify')->middleware('throttle:10,60');

    Route::get('password/phone', 'Auth\ForgotPasswordController@showLinkRequestPhoneForm')->name('password.phone');
    Route::post('password/phone', 'Auth\ForgotPasswordController@passwordPhone');
    Route::get('password/phone/verify', 'Auth\ForgotPasswordController@showPasswordPhoneVerifyForm')->name('password.phone.verify');
    Route::post('password/phone/verify', 'Auth\ForgotPasswordController@passwordPhoneVerify')->middleware('throttle:10,60');

    // regular pages
    // Route::get('page/{page}-{slug}', "PageController@index")->name('page');
    Route::get('guestbook', "PageController@guestbook")->name('guestbook');
    Route::get('{slug}', "PageController@index")->name('page');
    Route::get('page/{page}-{slug}/print', "PageController@print")->name('page.print');

    // ALLGOOD LEADS FOR SCORING PAGE

    Route::get('/scoring-kundalik/apply', [PageController::class, 'calculatorKundalik'])->name('my.calculator.index.kundalik');
    Route::post('/scoring-kundalik/apply', [PageController::class, 'sendToTelegram'])->name('my.calculator.store.kundalik');
    
    Route::get('/scoring/apply', [PageController::class, 'calculator'])->name('my.calculator.index');
    Route::get('/scoring/apply/finish', [PageController::class, 'calculatorFinish'])->name('my.calculator.finish');
    Route::post('/scoring/apply', [PageController::class, 'sendToTelegram'])->name('my.calculator.store');
});

// non localized routes

// REFERAL SYSYTEM
Route::group(['prefix' => 'ref'], function () {
    Route::get('/from/{referal}', [ReferalController::class, 'referal'])->name('referal');
    Route::get('/voucher', [ReferalController::class, 'voucher'])->name('voucher');
});

// captcha
Route::get('/refereshcaptcha', 'HelperController@refereshCaptcha');

// update sale price
Route::post('updatesaledate','ProductController@updatesaledate')->name('updatesaledate');

// cache clear and optimize
Route::get('/cache/optimize/{check}', "CacheController@optimize")->name('cache.optimize');
Route::get('/cache/view/clear/{check}', "CacheController@viewClear")->name('cache.view.clear');

// region
Route::post('/region/set', "RegionController@set")->name('region.set');

// SELLER
Route::group(['prefix' => 'seller'], function () {

    Route::get('/register_formxyz', [SellerAuthController::class, 'register_form_index'])->name('seller.register_form_index');
    Route::post('/register_formxyz', [SellerAuthController::class, 'register_form_store'])->name('seller.register_form_store');
    
    Route::get('/visit/{id}', [SellerController::class, 'visit'])->name('seller.visit');

    // Route::get('/company/{id}/edit', [SellerAuthController::class, 'update_form_index'])->name('seller.update_form_index');
    // Route::put('/company/{id}/update', [SellerAuthController::class, 'update_form_store'])->name('seller.update_form_store');

    // Route::get('/login', [SellerAuthController::class, 'login_form_index'])->name('seller.login_form_index');
    // Route::post('/login', [SellerAuthController::class, 'login_form_store'])->name('seller.login_form_store');
    // Route::post('/logout', [SellerAuthController::class, 'logout'])->name('seller.logout');

    // Route::get('/account/{owner?}', [SellerController::class, 'account'])->name('seller.account');
    // Route::get('/account/pages/products', [SellerController::class, 'products'])->name('seller.account.products');
    // Route::get('/account/pages/products/{id}/edit', [SellerController::class, 'productsEdit'])->name('seller.account.products.edit');
    // Route::put('/account/pages/products/{id}/update', [SellerController::class, 'productsUpdate'])->name('seller.account.products.update');


});
