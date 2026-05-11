<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\WaitlistController;
use App\Http\Controllers\MonitorDisplayController;
use App\Http\Controllers\PublicCalendarController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
});
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Frontend ─────────────────────────────────────────────────────────────────
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');

Route::prefix('venues')->name('venues.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Public\VenueController::class, 'index'])->name('index');
    Route::get('/{venue}', [\App\Http\Controllers\Public\VenueController::class, 'show'])->name('show');
    
    // Bookings
    Route::middleware(['auth'])->group(function () {
        Route::get('/slots/{slot}/checkout', [\App\Http\Controllers\Public\BookingController::class, 'checkout'])->name('checkout');
        Route::post('/slots/{slot}/book', [\App\Http\Controllers\Public\BookingController::class, 'store'])->name('book');
        Route::get('/booking/{booking}/success', [\App\Http\Controllers\Public\BookingController::class, 'success'])->name('booking-success');
    });
});

// ── Customer Dashboard ───────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Public\DashboardController::class, 'index'])->name('dashboard');
});

// ── Public Calendar ───────────────────────────────────────────────────────────
Route::get('/calendar', [PublicCalendarController::class, 'index'])->name('calendar.index');

// ── Monitor Display (token-gated, no auth middleware) ─────────────────────────
Route::prefix('monitor')->name('monitor.')->group(function () {
    Route::get('/', [MonitorDisplayController::class, 'index'])->name('display');
    Route::get('/data', [MonitorDisplayController::class, 'data'])->name('data');
});

// ── Admin Panel ───────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Bookings ──────────────────────────────────────────────────────────────
    Route::prefix('bookings')->name('bookings.')->middleware(['permission:view bookings'])->group(function () {
        Route::get('/',            [BookingController::class, 'index'])->name('index');
        Route::get('/create',      [BookingController::class, 'create'])->name('create')->middleware('permission:create bookings');
        Route::post('/',           [BookingController::class, 'store'])->name('store')->middleware('permission:create bookings');
        Route::get('/{booking}',   [BookingController::class, 'show'])->name('show');
        Route::get('/{booking}/edit',   [BookingController::class, 'edit'])->name('edit')->middleware('permission:edit bookings');
        Route::put('/{booking}',        [BookingController::class, 'update'])->name('update')->middleware('permission:edit bookings');

        // Lifecycle actions
        Route::post('/{booking}/confirm',   [BookingController::class, 'confirm'])->name('confirm')->middleware('permission:approve bookings');
        Route::post('/{booking}/cancel',    [BookingController::class, 'cancel'])->name('cancel')->middleware('permission:cancel bookings');
        Route::post('/{booking}/check-in',  [BookingController::class, 'checkIn'])->name('check-in')->middleware('permission:approve bookings');
        Route::post('/{booking}/check-out', [BookingController::class, 'checkOut'])->name('check-out')->middleware('permission:approve bookings');
        Route::get('/{booking}/invoice',    [BookingController::class, 'downloadInvoice'])->name('invoice');
    });

    // ── Payments ──────────────────────────────────────────────────────────────
    Route::prefix('payments')->name('payments.')->middleware(['permission:view payments'])->group(function () {
        Route::post('/bookings/{booking}',  [PaymentController::class, 'store'])->name('store')->middleware('permission:create payments');
        Route::delete('/{payment}',         [PaymentController::class, 'destroy'])->name('destroy')->middleware('permission:delete payments');
    });

    // ── Customers ─────────────────────────────────────────────────────────────
    Route::prefix('customers')->name('customers.')->middleware(['permission:view customers'])->group(function () {
        Route::get('/',                  [CustomerController::class, 'index'])->name('index');
        Route::get('/search',            [CustomerController::class, 'search'])->name('search');
        Route::get('/create',            [CustomerController::class, 'create'])->name('create')->middleware('permission:create customers');
        Route::post('/',                 [CustomerController::class, 'store'])->name('store')->middleware('permission:create customers');
        Route::get('/{customer}',        [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit',   [CustomerController::class, 'edit'])->name('edit')->middleware('permission:edit customers');
        Route::put('/{customer}',        [CustomerController::class, 'update'])->name('update')->middleware('permission:edit customers');
        Route::delete('/{customer}',     [CustomerController::class, 'destroy'])->name('destroy')->middleware('permission:delete customers');
        Route::post('/{customer}/adjust-credit', [CustomerController::class, 'adjustCredit'])->name('adjust-credit')->middleware('permission:manage credits');
    });

    // ── Venues ────────────────────────────────────────────────────────────────
    Route::prefix('venues')->name('venues.')->middleware(['permission:view venues'])->group(function () {
        Route::get('/',                [VenueController::class, 'index'])->name('index');
        Route::get('/create',          [VenueController::class, 'create'])->name('create')->middleware('permission:create venues');
        Route::post('/',               [VenueController::class, 'store'])->name('store')->middleware('permission:create venues');
        Route::get('/{venue}/edit',    [VenueController::class, 'edit'])->name('edit')->middleware('permission:edit venues');
        Route::put('/{venue}',         [VenueController::class, 'update'])->name('update')->middleware('permission:edit venues');
        Route::delete('/{venue}',      [VenueController::class, 'destroy'])->name('destroy')->middleware('permission:delete venues');

        // Image management
        Route::post('/{venue}/images',              [VenueController::class, 'uploadImages'])->name('images.upload')->middleware('permission:edit venues');
        Route::post('/{venue}/images/{image}/primary', [VenueController::class, 'setPrimaryImage'])->name('images.primary')->middleware('permission:edit venues');
        Route::delete('/{venue}/images/{image}',   [VenueController::class, 'deleteImage'])->name('images.delete')->middleware('permission:edit venues');
    });

    // ── Schedules ─────────────────────────────────────────────────────────────
    Route::prefix('schedules')->name('schedules.')->middleware(['permission:manage slots'])->group(function () {
        Route::get('/',                  [ScheduleController::class, 'index'])->name('index');
        Route::get('/create',            [ScheduleController::class, 'create'])->name('create');
        Route::post('/',                 [ScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}/edit',   [ScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}',        [ScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}',     [ScheduleController::class, 'destroy'])->name('destroy');
    });

    // ── Slots ─────────────────────────────────────────────────────────────────
    Route::prefix('slots')->name('slots.')->middleware(['permission:manage slots'])->group(function () {
        Route::get('/',              [SlotController::class, 'index'])->name('index');
        Route::post('/generate',     [SlotController::class, 'generate'])->name('generate');
        Route::post('/{slot}/block',   [SlotController::class, 'blockSlot'])->name('block');
        Route::post('/{slot}/unblock', [SlotController::class, 'unblockSlot'])->name('unblock');
    });

    // ── Reviews ───────────────────────────────────────────────────────────────
    Route::get('reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/{review}/toggle', [\App\Http\Controllers\Admin\ReviewController::class, 'togglePublish'])->name('reviews.toggle');
    Route::delete('reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // ── Reports ───────────────────────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->middleware(['permission:view reports'])->group(function () {
        Route::get('/',              [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/utilization',   [App\Http\Controllers\Admin\ReportController::class, 'utilization'])->name('utilization');
        Route::get('/revenue',       [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/export',        [ReportController::class, 'exportExcel'])->name('export')->middleware('permission:export reports');
    });

    // ── Waitlist ──────────────────────────────────────────────────────────────
    Route::prefix('waitlist')->name('waitlist.')->middleware(['permission:manage waitlist'])->group(function () {
        Route::get('/',                      [WaitlistController::class, 'index'])->name('index');
        Route::post('/',                     [WaitlistController::class, 'store'])->name('store');
        Route::delete('/{waitlistEntry}',    [WaitlistController::class, 'destroy'])->name('destroy');
    });

    // ── Settings ──────────────────────────────────────────────────────────────
    Route::prefix('settings')->name('settings.')->middleware(['permission:manage settings'])->group(function () {
        Route::get('/',   [SettingsController::class, 'index'])->name('index');
        Route::post('/',  [SettingsController::class, 'update'])->name('update');
    });

    // ── Impersonation ─────────────────────────────────────────────────────────
    Route::post('/impersonate/{user}', [UserController::class, 'impersonate'])->name('impersonate')->middleware('role:super_admin|admin');
    Route::get('/stop-impersonating', [UserController::class, 'stopImpersonating'])->name('impersonate.stop');

    // ── User Management ───────────────────────────────────────────────────────
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('users', UserController::class)->names('users');
        Route::resource('roles', RoleController::class)->names('roles');
    });

    // ── Marketing ────────────────────────────────────────────────────────────
    Route::prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/',            [MarketingController::class, 'index'])->name('index');
        Route::get('/create',      [MarketingController::class, 'create'])->name('create');
        Route::post('/',           [MarketingController::class, 'store'])->name('store');
        Route::get('/settings',    [MarketingController::class, 'settings'])->name('settings');
        Route::post('/settings',   [MarketingController::class, 'updateSettings'])->name('settings.update');
    });
});
