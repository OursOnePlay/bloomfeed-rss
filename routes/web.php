<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\NotifierController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/self-hosting', [PageController::class, 'selfHosting'])->name('self-hosting');
Route::post('/locale', [SettingsController::class, 'updateLocale'])->name('locale.update');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/registrations', [SettingsController::class, 'toggleRegistrations'])->name('settings.toggleRegistrations');
    Route::post('/settings/users', [SettingsController::class, 'createUser'])->name('settings.createUser');
    Route::post('/settings/sources', [SettingsController::class, 'addCatalogSource'])->name('settings.addSource');
    Route::delete('/settings/sources', [SettingsController::class, 'removeCatalogSource'])->name('settings.removeSource');

    Route::get('/notifiers', [NotifierController::class, 'index'])->name('notifiers');
    Route::post('/notifiers', [NotifierController::class, 'store'])->name('notifiers.store');
    Route::put('/notifiers/{notifier}', [NotifierController::class, 'update'])->name('notifiers.update');
    Route::post('/notifiers/{notifier}/toggle', [NotifierController::class, 'toggle'])->name('notifiers.toggle');
    Route::post('/notifiers/{notifier}/test', [NotifierController::class, 'test'])->name('notifiers.test');
    Route::delete('/notifiers/{notifier}', [NotifierController::class, 'destroy'])->name('notifiers.destroy');

    Route::get('/feeds', [FeedController::class, 'index'])->name('feeds.index');
    Route::post('/feeds', [FeedController::class, 'store'])->name('feeds.store');
    Route::delete('/feeds/{feed}', [FeedController::class, 'destroy'])->name('feeds.destroy');
    Route::post('/feeds/{feed}/refresh', [FeedController::class, 'refresh'])->name('feeds.refresh');
    Route::post('/feeds/refresh-all', [FeedController::class, 'refreshAll'])->name('feeds.refreshAll');

    Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
    Route::post('/articles/{article}/read', [ArticleController::class, 'markRead'])->name('articles.markRead');
    Route::post('/articles/{article}/favorite', [ArticleController::class, 'toggleFavorite'])->name('articles.toggleFavorite');
    Route::post('/articles/{article}/retry', [ArticleController::class, 'retry'])->name('articles.retry');
});
