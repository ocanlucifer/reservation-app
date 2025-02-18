<?php

// routes/web.php
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\RoleMiddleware;
use App\Helpers\RouteHelper;
use App\Http\Controllers\AssignTourGuideController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\BuildingScheduleController;
use App\Http\Controllers\ReportBuildingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportKoordinatorController;
use App\Http\Controllers\TourGuideController;
use App\Http\Controllers\VisitReservationController;
use App\Http\Controllers\VisitScheduleController;

//auth Route
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');


//Route foll all user
Route::middleware('auth')->group(function () {
    // Add Route to Change Password
    Route::post('/password/change', [UserController::class, 'changePassword'])->name('password.change');

    //Dashboard Route
    Route::get('/', function () {
        $user = Auth::user();
        $accessibleRoutes = RouteHelper::getAccessibleRoutes($user->role);
        return view('dashboard', compact('accessibleRoutes'));
    })->name('dashboard');

    Route::get('/unauthorized', function () {
        return view('unauthorized');
    })->name('unauthorized');

    //Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

//route for humas
Route::middleware(['auth', RoleMiddleware::class . ':admin,humas,visitor'])->group(function () {
    //Visit Schedule Route
    Route::resource('visitSchedules', VisitScheduleController::class);
        //Add a route to toggle booking Jadwal
        Route::post('/visitSchedules/{id}/cancelBooking', [VisitScheduleController::class, 'cancel_booking'])->name('visitSchedules.cancelBooking');

    //Visit Reservation Route
    Route::resource('visitReservations', VisitReservationController::class);


    Route::get('/api/reservation-schedules', [VisitReservationController::class, 'getSchedules']);

    Route::get('/reservation-schedules/calendar', [VisitReservationController::class, 'calendar'])->name('reservation-schedules.calendar');
});

Route::middleware(['auth', RoleMiddleware::class . ':admin,humas'])->group(function () {
    //Add a route to toggle item active status
    Route::post('/visitSchedules/{id}/toggleStatus', [VisitScheduleController::class, 'toggleStatus'])->name('visitSchedules.toggleStatus');
    //Add a route to toggle pengajuan Tour Guide
    Route::post('/visitSchedules/{id}/ReqTourGuide', [VisitScheduleController::class, 'RequestTourGuide'])->name('visitSchedules.ReqTourGuide');
    //Add a route to toggle konfirmasi kunjungan
    Route::post('/visitSchedules/{id}/ConfirmVisit', [VisitScheduleController::class, 'confirmVisit'])->name('visitSchedules.ConfirmVisit');
});

//route for koordinator
Route::middleware(['auth', RoleMiddleware::class . ':admin,koordinator'])->group(function () {
    //Assign Tour Guide Route
    Route::resource('assignTourGuides', AssignTourGuideController::class);
    //Report Koordinator
    Route::get('reportkoordinators', [ReportKoordinatorController::class, 'index'])->name('reportkoordinator.index');
    Route::get('/reportkoordinators/export-excel', [ReportKoordinatorController::class, 'exportExcel'])->name('reportkoordinator.export.excel');
    Route::get('/reportkoordinators/export-pdf', [ReportKoordinatorController::class, 'exportPDF'])->name('reportkoordinator.export.pdf');
});

//route for admin building humas
Route::middleware(['auth', RoleMiddleware::class . ':admin,building,humas'])->group(function () {
    //Report Kunjungan
    Route::get('reports', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/export-excel', [ReportController::class, 'exportExcel'])->name('report.export.excel');
    Route::get('/report/export-pdf', [ReportController::class, 'exportPDF'])->name('report.export.pdf');


    //Report Building
    Route::get('reportbuildings', [ReportBuildingController::class, 'index'])->name('reportbuilding.index');
    Route::get('/reportbuildings/export-excel', [ReportBuildingController::class, 'exportExcel'])->name('reportbuilding.export.excel');
    Route::get('/reportbuildings/export-pdf', [ReportBuildingController::class, 'exportPDF'])->name('reportbuilding.export.pdf');
// });

// //route for building
// Route::middleware(['auth', RoleMiddleware::class . ':admin,building,humas'])->group(function () {
    //Building Schedule Route
    Route::resource('buildingSchedules', BuildingScheduleController::class);
        //Add a route to toggle item active status
        Route::post('/buildingSchedules/{id}/toggleStatus', [BuildingScheduleController::class, 'toggleStatus'])->name('buildingSchedules.toggleStatus');
        //Add a route to toggle booking gedung
        Route::post('/buildingSchedules/{id}/bookingGedung', [BuildingScheduleController::class, 'booking'])->name('buildingSchedules.bookingGedung');

        Route::get('/api/building-schedules', [BuildingScheduleController::class, 'getSchedules']);

        Route::get('/building-schedules/calendar', [BuildingScheduleController::class, 'calendar'])->name('building-schedules.calendar');


});

//route for admin
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    //User Route
    Route::resource('users', UserController::class);
            //Add a route to toggle item active status
            Route::post('/users/{id}/toggleStatus', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
            // Add Route to reset Password
            Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');

    //Building Route
    Route::resource('buildings', BuildingController::class);
        //Add a route to toggle item active status
        Route::post('/buildings/{id}/toggleStatus', [BuildingController::class, 'toggleStatus'])->name('buildings.toggleStatus');

    //Tour Guide Route
    Route::resource('tourGuides', TourGuideController::class);
        //Add a route to toggle item active status
        Route::post('/tourGuides/{id}/toggleStatus', [TourGuideController::class, 'toggleStatus'])->name('tourGuides.toggleStatus');


});
