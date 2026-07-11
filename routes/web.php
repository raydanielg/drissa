<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClinicRoomController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\LabEquipmentController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);

Auth::routes(['login' => false, 'register' => false]);

Route::get('/home', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::prefix('reception')->name('reception.')->middleware('role:reception,admin')->group(function () {
        Route::get('/', [ReceptionController::class, 'dashboard'])->name('dashboard');
        Route::post('patients', [ReceptionController::class, 'storePatient'])->name('patients.store');
        Route::post('visits', [ReceptionController::class, 'storeVisit'])->name('visits.store');
        Route::post('visits/{visit}/assign', [ReceptionController::class, 'assignDoctor'])->name('visits.assign');
        Route::post('visits/{visit}/pay', [ReceptionController::class, 'storePayment'])->name('visits.pay');
    });

    Route::prefix('doctor')->name('doctor.')->middleware('role:doctor,admin')->group(function () {
        Route::get('/', [DoctorController::class, 'queue'])->name('queue');
        Route::post('visits/{visit}/call', [DoctorController::class, 'callNext'])->name('visits.call');
        Route::post('visits/{visit}/consult', [DoctorController::class, 'saveConsultation'])->name('visits.consult');
        Route::post('visits/{visit}/lab', [DoctorController::class, 'orderLab'])->name('visits.lab');
        Route::post('visits/{visit}/prescribe', [DoctorController::class, 'prescribe'])->name('visits.prescribe');
        Route::post('visits/{visit}/payment', [DoctorController::class, 'sendToPayment'])->name('visits.payment');
    });

    Route::middleware('role:lab,admin')->group(function () {
        Route::resource('lab-equipment', LabEquipmentController::class);
        Route::resource('lab-tests', LabTestController::class);

        Route::prefix('lab')->name('lab.')->group(function () {
            Route::get('/', [LabController::class, 'queue'])->name('queue');
            Route::post('orders/{order}/start', [LabController::class, 'startProcessing'])->name('orders.start');
            Route::post('orders/{order}/results', [LabController::class, 'submitResults'])->name('orders.results');
        });
    });

    Route::prefix('pharmacy')->name('pharmacy.')->middleware('role:pharmacy,admin')->group(function () {
        Route::get('/', [PharmacyController::class, 'queue'])->name('queue');
        Route::post('prescriptions/{prescription}/dispense', [PharmacyController::class, 'dispense'])->name('prescriptions.dispense');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('appointments', AppointmentController::class);
        Route::resource('posts', PostController::class);
        Route::resource('products', ProductController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('clinic-rooms', ClinicRoomController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('shifts', ShiftController::class);

        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('{invoice}', [InvoiceController::class, 'show'])->name('show');
            Route::get('{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('pdf');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::put('/', [SettingsController::class, 'update'])->name('update');
            Route::get('email', [SettingsController::class, 'email'])->name('email');
            Route::get('sms', [SettingsController::class, 'sms'])->name('sms');
            Route::get('payment', [SettingsController::class, 'payment'])->name('payment');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('patients', [ReportController::class, 'patients'])->name('patients');
            Route::get('doctors', [ReportController::class, 'doctorPerformance'])->name('doctors');
            Route::get('stock', [ReportController::class, 'stock'])->name('stock');
            Route::get('revenue', [ReportController::class, 'revenue'])->name('revenue');
            Route::get('health', [ReportController::class, 'systemHealth'])->name('health');
        });

        Route::prefix('activity-logs')->name('activity_logs.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        });

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('send', [NotificationController::class, 'send'])->name('send');
            Route::get('templates', [NotificationController::class, 'templates'])->name('templates');
            Route::post('templates', [NotificationController::class, 'storeTemplate'])->name('templates.store');
        });
    });
});
