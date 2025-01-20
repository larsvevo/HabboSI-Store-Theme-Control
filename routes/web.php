<?php

use Atom\Theme\Http\Controllers\AccountSettingsController;
use Atom\Theme\Http\Controllers\ArticleController;
use Atom\Theme\Http\Controllers\ClientController;
use Atom\Theme\Http\Controllers\CommentController;
use Atom\Theme\Http\Controllers\HelpCentreController;
use Atom\Theme\Http\Controllers\HomeController;
use Atom\Theme\Http\Controllers\IndexController;
use Atom\Theme\Http\Controllers\LeaderboardController;
use Atom\Theme\Http\Controllers\PasswordController;
use Atom\Theme\Http\Controllers\PayPalController;
use Atom\Theme\Http\Controllers\PhotoController;
use Atom\Theme\Http\Controllers\ProfileController;
use Atom\Theme\Http\Controllers\PurchaseController;
use Atom\Theme\Http\Controllers\RareValueController;
use Atom\Theme\Http\Controllers\RedeemVoucherController;
use Atom\Theme\Http\Controllers\RuleController;
use Atom\Theme\Http\Controllers\ShopController;
use Atom\Theme\Http\Controllers\SitemapController;
use Atom\Theme\Http\Controllers\StaffApplicationController;
use Atom\Theme\Http\Controllers\StaffController;
use Atom\Theme\Http\Controllers\AssistentenController;
use Atom\Theme\Http\Controllers\TeamController;
use Atom\Theme\Http\Controllers\TicketController;
use Atom\Theme\Http\Controllers\TicketReplyController;
use Atom\Theme\Http\Controllers\TopUpController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('sitemap.xml', SitemapController::class)
        ->name('sitemap');

    Route::get('/', IndexController::class)
        ->name('index');

    Route::name('shop.')->group(function () {
        Route::get('shop', ShopController::class)
            ->middleware(Authenticate::using('sanctum'), 'voting.check')
            ->name('index');

        Route::post('shop/{article}/purchase', PurchaseController::class)
            ->middleware(Authenticate::using('sanctum'), 'voting.check')
            ->name('purchase');

        Route::post('shop/voucher/redeem', RedeemVoucherController::class)
            ->middleware(Authenticate::using('sanctum'), 'voting.check')
            ->name('voucher.redeem');

        Route::post('shop/top-up', TopUpController::class)
            ->middleware(Authenticate::using('sanctum'))
            ->name('top-up');

        Route::get('successful-transaction', [PayPalController::class, 'success'])
            ->middleware(Authenticate::using('sanctum'))
            ->name('successful-transaction');

        Route::get('cancelled-transaction', [PayPalController::class, 'cancelled'])
            ->middleware(Authenticate::using('sanctum'))
            ->name('cancelled-transaction');
    });
});
