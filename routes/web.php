<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/self-hosting', [PageController::class, 'selfHosting'])->name('self-hosting');

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
