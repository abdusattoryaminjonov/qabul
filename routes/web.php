<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormBuilderController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ResponseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PublicFormController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/s/{code}', [PublicFormController::class, 'shortRedirect'])->name('forms.short');

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.login');
});

Route::prefix('f')->name('forms.')->middleware('throttle:60,1')->group(function () {
    Route::get('/{slug}', [PublicFormController::class, 'show'])->name('show');
    Route::post('/{slug}', [PublicFormController::class, 'submit'])->middleware('throttle:10,1')->name('submit');
    Route::get('/{slug}/rahmat', [PublicFormController::class, 'thankyou'])->name('thankyou');
});

Route::name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/read-all', [NotificationController::class, 'readAll'])->name('read-all');

            Route::middleware('super_admin')->group(function () {
                Route::get('/broadcast/create', [NotificationController::class, 'createBroadcast'])->name('broadcast.create');
                Route::post('/broadcast', [NotificationController::class, 'storeBroadcast'])->name('broadcast.store');
            });
        });

        Route::middleware('super_admin')->prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::get('/responses', [ResponseController::class, 'formsList'])->name('responses.forms');
        Route::get('/statistics', [ResponseController::class, 'statisticsList'])->name('statistics.forms');

        Route::get('/manage', [FormController::class, 'index'])->name('forms.index');
        Route::get('/manage/create', [FormController::class, 'create'])->name('forms.create');
        Route::post('/manage', [FormController::class, 'store'])->name('forms.store');
        Route::get('/manage/{form}/edit', [FormController::class, 'edit'])->name('forms.edit');
        Route::put('/manage/{form}', [FormController::class, 'update'])->name('forms.update');
        Route::delete('/manage/{form}', [FormController::class, 'destroy'])->name('forms.destroy');
        Route::post('/manage/{form}/duplicate', [FormController::class, 'duplicate'])->name('forms.duplicate');
        Route::post('/manage/{form}/short-link', [FormController::class, 'generateShortLink'])->name('forms.short-link');
        Route::post('/manage/{form}/structure', [FormBuilderController::class, 'saveStructure'])->name('forms.structure');

        Route::get('/manage/{form}/responses', [ResponseController::class, 'index'])->name('responses.index');
        Route::get('/manage/{form}/responses/export', [ResponseController::class, 'export'])->name('responses.export');
        Route::get('/manage/{form}/analytics', [ResponseController::class, 'analytics'])->name('responses.analytics');
        Route::get('/manage/{form}/responses/{response}', [ResponseController::class, 'show'])->name('responses.show');
        Route::delete('/manage/{form}/responses/{response}', [ResponseController::class, 'destroy'])->name('responses.destroy');
    });
});
