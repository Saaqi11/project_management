<?php

use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\DocumentController;
use App\Http\Controllers\Tenant\InformationController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\TicketController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'tenant', 'as' => 'tenant.', 'middleware' => ['auth', 'tenant']], function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::group(['prefix' => 'invoice', 'as' => 'invoice.'], function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('print/{id}', [InvoiceController::class, 'details'])->name('print');
        Route::get('pay/{id}', [InvoiceController::class, 'pay'])->name('pay');
        Route::get('get-currency-by-gateway', [InvoiceController::class, 'getCurrencyByGateway'])->name('get.currency');
    });

    Route::group(['prefix' => 'information', 'as' => 'information.'], function () {
        Route::get('/', [InformationController::class, 'index'])->name('index');
        Route::get('get-info', [InformationController::class, 'getInfo'])->name('get.info'); // ajax
    });

    Route::group(['prefix' => 'document', 'as' => 'document.'], function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::post('store', [DocumentController::class, 'store'])->name('store');
        Route::get('get-info', [DocumentController::class, 'getInfo'])->name('get.info'); // ajax
        Route::get('get-config-info', [DocumentController::class, 'getConfigInfo'])->name('get.config.info'); // ajax
        Route::delete('delete/{id}', [DocumentController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('get-info', [TicketController::class, 'getInfo'])->name('get.info'); // ajax
        Route::get('details/{id}', [TicketController::class, 'details'])->name('details');
        Route::post('store', [TicketController::class, 'store'])->name('store');
        Route::post('reply', [TicketController::class, 'reply'])->name('reply');
        Route::get('status-change', [TicketController::class, 'statusChange'])->name('status.change');
        Route::delete('delete/{id}', [TicketController::class, 'delete'])->name('delete');
        Route::get('search', [TicketController::class, 'search'])->name('search'); // ajax
    });
});
