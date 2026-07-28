<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminDoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ClinicalRecordController;
use App\Http\Controllers\ClinicRoomController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\LabEquipmentController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientDocumentController;
use App\Http\Controllers\PatientHistoryController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Installer Routes
|--------------------------------------------------------------------------
*/
Route::get('/install', [InstallController::class, 'welcome'])->name('install.welcome');
Route::get('/install/database', [InstallController::class, 'database'])->name('install.database');
Route::post('/install/process', [InstallController::class, 'process'])->name('install.process');
Route::post('/install/run', [InstallController::class, 'runMigrations'])->name('install.run');
Route::get('/install/complete', [InstallController::class, 'complete'])->name('install.complete');

/*
|--------------------------------------------------------------------------
| Public Website Routes
|--------------------------------------------------------------------------
*/
Route::get('/home', [PublicController::class, 'home'])->name('public.home');
Route::get('/about', [PublicController::class, 'about'])->name('public.about');
Route::get('/branches', [PublicController::class, 'branches'])->name('public.branches');
Route::get('/our-services', [PublicController::class, 'services'])->name('public.services');
Route::get('/book-appointment', [PublicController::class, 'appointments'])->name('public.appointments');
Route::post('/book-appointment', [PublicController::class, 'storeAppointment'])->name('public.appointments.store');
Route::get('/blog', [PublicController::class, 'blog'])->name('public.blog');
Route::get('/shop', [PublicController::class, 'shop'])->name('public.shop');
Route::get('/contact', [PublicController::class, 'contact'])->name('public.contact');
Route::post('/contact', [PublicController::class, 'storeContact'])->name('public.contact.store');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);

Route::get('/login', function () {
    return redirect()->route('login');
})->name('login.alt');

Auth::routes(['login' => false, 'register' => false]);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard')->middleware('redirect.role:admin|reception');
    Route::get('/dashboard/stats', [HomeController::class, 'stats'])->name('dashboard.stats')->middleware('redirect.role:admin|reception');

    Route::prefix('reception')->name('reception.')->middleware('redirect.role:reception|admin')->group(function () {
        Route::get('/', [ReceptionController::class, 'dashboard'])->name('dashboard');
        Route::get('/queue', [ReceptionController::class, 'queue'])->name('queue');
        Route::get('/payments', [ReceptionController::class, 'payments'])->name('payments');
        Route::get('/stats', [ReceptionController::class, 'stats'])->name('stats');
        Route::post('patients', [ReceptionController::class, 'storePatient'])->name('patients.store');
        Route::post('visits', [ReceptionController::class, 'storeVisit'])->name('visits.store');
        Route::post('visits/{visit}/assign', [ReceptionController::class, 'assignDoctor'])->name('visits.assign');
        Route::post('visits/{visit}/change-doctor', [ReceptionController::class, 'changeDoctor'])->name('visits.change-doctor');
        Route::post('visits/{visit}/pay', [ReceptionController::class, 'storePayment'])->name('visits.pay');
        Route::post('visits/{visit}/close', [ReceptionController::class, 'closeVisit'])->name('visits.close');
        Route::post('invoices/{invoice}/mark-paid', [ReceptionController::class, 'markInvoicePaid'])->name('invoices.mark-paid');
        Route::post('invoices/{invoice}/mark-unpaid', [ReceptionController::class, 'markInvoiceUnpaid'])->name('invoices.mark-unpaid');
    });

    Route::prefix('doctor')->name('doctor.')->middleware('redirect.role:doctor|admin')->group(function () {
        Route::get('/', [DoctorController::class, 'queue'])->name('queue');
        Route::get('lab-results', [DoctorController::class, 'labResults'])->name('lab-results');
        Route::post('visits/{visit}/call', [DoctorController::class, 'callNext'])->name('visits.call');
        Route::post('visits/{visit}/no-show', [DoctorController::class, 'markNoShow'])->name('visits.no-show');
        Route::post('visits/{visit}/consult', [DoctorController::class, 'saveConsultation'])->name('visits.consult');
        Route::post('visits/{visit}/lab', [DoctorController::class, 'orderLab'])->name('visits.lab');
        Route::post('visits/{visit}/lab-return', [DoctorController::class, 'returnFromLab'])->name('visits.lab-return');
        Route::post('visits/{visit}/prescribe', [DoctorController::class, 'prescribe'])->name('visits.prescribe');
        Route::post('visits/{visit}/payment', [DoctorController::class, 'sendToPayment'])->name('visits.payment');
    });

    Route::middleware('redirect.role:lab|admin')->group(function () {
        Route::resource('lab-equipment', LabEquipmentController::class);
        Route::patch('lab-equipment/{labEquipment}/status', [LabEquipmentController::class, 'updateStatus'])->name('lab-equipment.status.update');
        Route::resource('lab-tests', LabTestController::class);

        Route::prefix('lab')->name('lab.')->group(function () {
            Route::get('/', [LabController::class, 'queue'])->name('queue');
            Route::post('orders/{order}/start', [LabController::class, 'startProcessing'])->name('orders.start');
            Route::post('orders/{order}/results', [LabController::class, 'submitResults'])->name('orders.results');
        });
    });

    Route::prefix('pharmacy')->name('pharmacy.')->middleware('redirect.role:pharmacy|admin')->group(function () {
        Route::get('/', [PharmacyController::class, 'queue'])->name('queue');
        Route::post('prescriptions/{prescription}/dispense', [PharmacyController::class, 'dispense'])->name('prescriptions.dispense');
    });

    // Patient Registry, Documents & History
    Route::get('patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::get('patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
    Route::get('patients/{patient}/documents', [PatientDocumentController::class, 'index'])->name('patients.documents.index');
    Route::post('patients/{patient}/documents', [PatientDocumentController::class, 'store'])->name('patients.documents.store');
    Route::get('patient-documents/{document}/download', [PatientDocumentController::class, 'download'])->name('patients.documents.download');
    Route::delete('patient-documents/{document}', [PatientDocumentController::class, 'destroy'])->name('patients.documents.destroy');
    Route::get('patients/{patient}/history', [PatientHistoryController::class, 'show'])->name('patients.history');

    // Profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Team Chat
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('chat/{conversation}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('chat/{conversation}/poll', [ChatController::class, 'poll'])->name('chat.poll');
    Route::get('chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');

    // Clinical Records
    Route::resource('clinical-records', ClinicalRecordController::class);
    Route::get('visits/{visit}/clinical-record', [ClinicalRecordController::class, 'createFromVisit'])->name('clinical-records.from-visit');
    Route::get('appointments/{appointment}/clinical-record', [ClinicalRecordController::class, 'createFromAppointment'])->name('clinical-records.from-appointment');

    // SMS
    Route::get('sms', [SmsController::class, 'index'])->name('sms.index');
    Route::post('sms', [SmsController::class, 'store'])->name('sms.store');
    Route::get('sms/logs', [SmsController::class, 'logs'])->name('sms.logs');
    Route::get('sms/templates', [SmsController::class, 'templates'])->name('sms.templates');
    Route::post('sms/templates', [SmsController::class, 'storeTemplate'])->name('sms.templates.store');
    Route::put('sms/templates/{template}', [SmsController::class, 'updateTemplate'])->name('sms.templates.update');
    Route::delete('sms/templates/{template}', [SmsController::class, 'destroyTemplate'])->name('sms.templates.destroy');

    Route::middleware('role:admin')->group(function () {
        // Admin Queue - View all patients
        Route::get('admin/queue', [AdminDoctorController::class, 'queue'])->name('admin.queue');
        Route::post('admin/visits/{visit}/discharge', [AdminDoctorController::class, 'discharge'])->name('admin.visits.discharge');
        Route::post('admin/visits/{visit}/complete-lab', [AdminDoctorController::class, 'completeLab'])->name('admin.visits.complete-lab');
        Route::post('admin/visits/{visit}/complete-payment', [AdminDoctorController::class, 'completePayment'])->name('admin.visits.complete-payment');
        
        // Doctors Management (Admin)
        Route::get('admin/doctors', [AdminDoctorController::class, 'index'])->name('admin.doctors.index');
        Route::post('admin/doctors', [AdminDoctorController::class, 'store'])->name('admin.doctors.store');
        Route::get('admin/doctors/{doctor}', [AdminDoctorController::class, 'show'])->name('admin.doctors.show');
        Route::put('admin/doctors/{doctor}', [AdminDoctorController::class, 'update'])->name('admin.doctors.update');
        Route::delete('admin/doctors/{doctor}', [AdminDoctorController::class, 'destroy'])->name('admin.doctors.delete');
        Route::put('admin/doctors/{doctor}/reset-password', [AdminDoctorController::class, 'resetPassword'])->name('admin.doctors.reset-password');
        Route::patch('admin/doctors/{doctor}/toggle', [AdminDoctorController::class, 'toggleActive'])->name('admin.doctors.toggle');
        Route::resource('users', UserController::class);
        Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
        Route::post('users/bulk-deactivate', [UserController::class, 'bulkDeactivate'])->name('users.bulk-deactivate');
        Route::post('users/bulk-activate', [UserController::class, 'bulkActivate'])->name('users.bulk-activate');
        Route::resource('appointments', AppointmentController::class);
        Route::resource('posts', PostController::class);
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('clinic-rooms', ClinicRoomController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('shifts', ShiftController::class);
        Route::resource('services', ServiceController::class);

        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('{invoice}', [InvoiceController::class, 'show'])->name('show');
            Route::get('{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('pdf');
            Route::get('download-all', [InvoiceController::class, 'downloadAllPdf'])->name('download-all');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::put('/', [SettingsController::class, 'update'])->name('update');
            Route::get('email', [SettingsController::class, 'email'])->name('email');
            Route::get('sms', [SettingsController::class, 'sms'])->name('sms');
            Route::post('sms/test', [SettingsController::class, 'testSms'])->name('sms.test');
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
