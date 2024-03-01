<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/* ________________ RESET PASSWORD CUSTOMIZED FOR MANAGER AND PLAYER _______________ */
Route::get('/getTicket/data', 'GameController@getTicketData');
Route::get('/check/stock/{stock}', 'GameController@checkstock');
Route::post('/reset/password', 'ResetPasswordController@ForgotTokenGenrate');
// ______________ Update new verify and update new password da
Route::post('/update/password', 'ResetPasswordController@ForgotTokenCheck');
Route::post('/send/ticket', 'ResetPasswordController@sendTicket');

// Media Manager
Route::get('/media', 'MediaController@index');
Route::post('/media/upload', 'MediaController@store');
Route::get('/media/id/{id}', 'MediaController@show');
Route::get('/media/delete/{id}', 'MediaController@destroy');

// Setup Winner
Route::get('/setupwinner', 'GameWinnerController@setupWinner');
Route::get('/setupdailywinner', 'GameWinnerDailyController@setupWinner');

// get Winner
Route::get('/grandwinner/{id}', 'GameWinnerController@winners');
Route::get('/dailywinner/{id}', 'GameWinnerDailyController@daily_winners');
Route::get('/winnerget', 'GameWinnerController@index');
// update daily stocks
Route::get('/updatestocks', 'StockUpdateController@updateStocks');

// Update Stock List 
Route::get('/updatestocklist','StockController@runStockUpdate');

// User Controller
// Login Info Logout
Route::post('/user/login', 'UserController@Login');
Route::middleware('auth:api')->get('/user/info', 'UserController@UserInfo')->name('userinfo');
Route::middleware('auth:api')->post('/user/logout', 'UserController@Logout');
Route::middleware('auth:api')->post('/user/status', 'UserController@statusUpdate');

//User Controller
Route::middleware('auth:api')->put('/user/setting/update', 'UserController@setting');
Route::middleware('auth:api')->get('/user/records', 'UserController@index');
Route::middleware('auth:api')->post('/user/create', 'UserController@store');
Route::middleware('auth:api')->put('/user/update', 'UserController@store');
Route::middleware('auth:api')->get('/user/id/{id}', 'UserController@show');
Route::middleware('auth:api')->delete('/user/delete/{id}', 'UserController@destroy');


//Offer Controller 
Route::middleware('auth:api')->get('/offer/records', 'OfferController@index');
Route::middleware('auth:api')->get('/offer/active/records', 'OfferController@activeRecord');
Route::middleware('auth:api')->post('/offer/create', 'OfferController@store');
Route::middleware('auth:api')->post('/offer/status', 'OfferController@statusUpdate');
Route::middleware('auth:api')->put('/offer/update', 'OfferController@store');
Route::middleware('auth:api')->get('/offer/id/{id}', 'OfferController@show');
Route::middleware('auth:api')->delete('/offer/delete/{id}', 'OfferController@destroy');


//Organization Controller
Route::middleware('auth:api')->get('/organization/records', 'OrganizationController@index');
Route::middleware('auth:api')->get('/organization/active/records', 'OrganizationController@activeRecord');
Route::middleware('auth:api')->post('/organization/create', 'OrganizationController@store');
Route::middleware('auth:api')->post('/organization/status', 'OrganizationController@statusUpdate');
Route::middleware('auth:api')->put('/organization/update', 'OrganizationController@store');
Route::middleware('auth:api')->get('/organization/id/{id}', 'OrganizationController@show');
Route::middleware('auth:api')->delete('/organization/delete/{id}', 'OrganizationController@destroy');

//Prize Controller
Route::middleware('auth:api')->get('/prize/records', 'PrizeController@index');
Route::middleware('auth:api')->get('/prize/active/records', 'PrizeController@activeRecord');
Route::middleware('auth:api')->post('/prize/create', 'PrizeController@store');
Route::middleware('auth:api')->post('/prize/status', 'PrizeController@statusUpdate');
Route::middleware('auth:api')->put('/prize/update', 'PrizeController@store');
Route::middleware('auth:api')->get('/prize/id/{id}', 'PrizeController@show');
Route::middleware('auth:api')->delete('/prize/delete/{id}', 'PrizeController@destroy');

//Prize Meta Controller
Route::middleware('auth:api')->get('/prizemeta/records/{id}', 'PrizeMetaController@index');
Route::middleware('auth:api')->get('/prizemeta/active/records', 'PrizeMetaController@activeRecord');
Route::middleware('auth:api')->post('/prizemeta/create', 'PrizeMetaController@store');
//Route::post('/prizemeta/status', 'PrizeMetaMetaController@statusUpdate');
Route::middleware('auth:api')->put('/prizemeta/update', 'PrizeMetaController@store');
Route::middleware('auth:api')->get('/prizemeta/id/{id}', 'PrizeMetaController@show');
Route::middleware('auth:api')->delete('/prizemeta/delete/{id}', 'PrizeMetaController@destroy');

// Daily Prize Meta

Route::middleware('auth:api')->get('/prizemeta/daily/records/{id}', 'PrizeMetaController@indexDaily');
Route::middleware('auth:api')->post('/prizemeta/daily/create', 'PrizeMetaController@storeDaily');
Route::middleware('auth:api')->put('/prizemeta/daily/update', 'PrizeMetaController@storeDaily');


//Banner Controller
Route::middleware('auth:api')->get('/banner/records', 'BannerController@index');
Route::get('/banner/active/records', 'BannerController@activBanner');
Route::get('/banner/active/mini/records', 'BannerController@activMiniBanner');
Route::middleware('auth:api')->get('/banner/id/{id}', 'BannerController@show');
Route::middleware('auth:api')->post('/banner/create', 'BannerController@store');
Route::middleware('auth:api')->post('/banner/status', 'BannerController@statusUpdate');
Route::middleware('auth:api')->put('/banner/update', 'BannerController@store');
Route::middleware('auth:api')->delete('/banner/delete/{id}', 'BannerController@destroy');



//Game Controller
Route::middleware('auth:api')->get('/game/records', 'GameController@index');
Route::middleware('auth:api')->get('/game/active/records', 'GameController@activeRecord');
Route::middleware('auth:api')->post('/game/create', 'GameController@store');
Route::middleware('auth:api')->post('/game/status', 'GameController@statusUpdate');
Route::middleware('auth:api')->put('/game/update', 'GameController@store');
Route::middleware('auth:api')->get('/game/id/{id}', 'GameController@show');
Route::middleware('auth:api')->get('/game/billings', 'GameController@bill');
Route::middleware('auth:api')->get('/game/invoice/{id}', 'GameController@invoice');
Route::middleware('auth:api')->delete('/game/delete/{id}', 'GameController@destroy');
Route::get('/game/export/{id}', 'GameController@exportTicket');
Route::post('/email/invoice', 'GameController@sendInvoice')->name('invoicesend');
Route::get('/email/invoice/{game}', 'GameController@sendIt')->name('sendit');


Route::get('/fullgame/{id}', 'GameController@fullGameDetails');
Route::get('/game/info/{id}', 'GameController@gameInfo');

Route::get('/stock/data/{id}', 'ClaimController@fetchSymbole');


//Player  Controller
Route::post('/player/login', 'PlayerController@Login');
Route::middleware('auth:api')->get('/player/records', 'PlayerController@index');
Route::middleware('auth:api')->get('/player/active', 'PlayerController@activePlayer');
Route::middleware('auth:api')->get('/player/info', 'PlayerController@playerInfo')->name('playerinfo');
Route::post('/player/create', 'PlayerController@store');
Route::middleware('auth:api')->post('/player/status', 'PlayerController@statusUpdate');
Route::middleware('auth:api')->put('/player/update', 'PlayerController@store');
Route::middleware('auth:api')->get('/player/id/{id}', 'PlayerController@show');
Route::middleware('auth:api')->get('/player/paymentmethod', 'PlayerController@paymentMethods');
Route::middleware('auth:api')->get('/player/invoce', 'PlayerController@Invoces');
Route::middleware('auth:api')->delete('/player/delete/{id}', 'PlayerController@destroy');
Route::middleware('auth:api')->post('/player/logout', 'PlayerController@Logout');

/* ______________ Fetch Gameplay by Player _________________________ */

Route::middleware('auth:api')->get('/player/gameplay/active/{id}', 'GameplayController@selectActiveByPlayer');
Route::middleware('auth:api')->get('/player/gameplay/inactive/{id}', 'GameplayController@selectInactiveByPlayer');




// Dashboard Data
Route::middleware('auth:api')->get('/dashboard', 'DashboardController@index');
Route::get('/position/{id}', 'DashboardController@positionH');



//Ticket Controller
Route::middleware('auth:api')->get('/ticket/records', 'TicketController@index');
Route::middleware('auth:api')->get('/ticket/active/records', 'TicketController@activeRecord');
Route::middleware('auth:api')->post('/ticket/create', 'TicketController@store');
Route::middleware('auth:api')->post('/ticket/status', 'TicketController@statusUpdate');
Route::middleware('auth:api')->put('/ticket/update', 'TicketController@store');
Route::middleware('auth:api')->get('/ticket/id/{id}', 'TicketController@show');
Route::middleware('auth:api')->delete('/ticket/delete/{id}', 'TicketController@destroy');
Route::middleware('auth:api')->get('/ticket/filter/', 'TicketController@filterTicket');

Route::get('/stocks', 'StockController@index');
Route::middleware('auth:api')->post('/stocks/list', 'StockController@list');

//Route::post('/strip/customer', 'StripeController@createAccount');


// FrontEnd Side Requests
Route::middleware('auth:api')->get('/gameplay/ticket/{access_code}', 'GameplayController@TicketData');
Route::middleware('auth:api')->post('/gameplay/claim', 'ClaimController@Claim');
Route::middleware('auth:api')->post('/gameplay/start', 'GameplayController@start');
Route::middleware('auth:api')->get('/gameplay/mygame', 'GameplayController@myGame');
Route::get('/gameplay/continue/{id}', 'GameplayController@continue_game');
Route::middleware('auth:api')->post('/gameplay/autopick', 'GameplayController@autopick');
Route::middleware('auth:api')->post('/gameplay/manualpick', 'GameplayController@manualpick');

// Game Getter
Route::get('player/game', 'GameController@activGame');
Route::get('/leaderboard/{id}', 'GameController@leaderBoard');

// Checkout session giver

Route::middleware('auth:api')->post('/checkout/session', 'CheckoutController@CreateCheckoutSession');
Route::middleware('auth:api')->post('/checkout/assign/tickets', 'CheckoutController@assignTickets');

//Ticket Getter
Route::middleware('auth:api')->get('/mytickets', 'TicketController@myTickets');

// Player Profile Fetch
Route::middleware('auth:api')->get('/myprofile', 'PlayerController@myProfile');
Route::middleware('auth:api')->put('/myprofile/update', 'PlayerController@myProfileUpdate');
