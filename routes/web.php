<?php

use App\Http\Controllers\AuthCallbackController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear_cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    return redirect()->route('home')->with('success', 'Cache cleared successfully');
});

Route::get('/', [AuthCallbackController::class, 'handle'])->name('check_auth_callback');
Route::get('/logout', [AuthCallbackController::class, 'logout'])->name('logout');
Route::view('/auth-callback', 'auth_callback')->name('auth_callback');

Route::get('/case_1_test/{icdcause}/{injby}', 'CheckingController@case_1_test')->name('case_1_test'); //ccase_1_test

Route::prefix('dashboard')->group(function () {
    Route::get('/hospital-21-variables', 'DashboardController@hospital_21_variables')->name('dashboard.hospital_21_variables');
    Route::post('/hospital-21-variables', 'DashboardController@hospital_21_variables')->name('dashboard.hospital_21_variables');
    Route::get('/hospital-overview', 'DashboardController@hospital_overview')->name('dashboard.hospital_overview');
    Route::post('/hospital-overview', 'DashboardController@hospital_overview')->name('dashboard.hospital_overview');
    Route::get('/get-province-from-health-zone', 'DashboardController@get_province_from_health_zone')->name('dashboard.get_province_from_health_zone');
    Route::get('/get-hospital-from-province', 'DashboardController@get_hospital_from_province')->name('dashboard.get_hospital_from_province');
    Route::get('/get-hospital-asm1-from-province', 'DashboardController@get_hospital_asm1_from_province')->name('dashboard.get_hospital_asm1_from_province');
});

Route::middleware(['check.session'])->group(function () {
    Route::get('/present', 'PresentReportController@index')->name('home');
    Route::get('/present/report', 'PresentReportController@index')->name('present_report');
    Route::post('/present/report', 'PresentReportController@index')->name('present_report');
    // Route::get('/search/report/present', 'PresentReportController@search')->name('search_present_report');

    Route::get('/retrospective/report', 'RetrospectiveReport@index')->name('retrospective_report');
    Route::get('/download/report/{id}', 'RetrospectiveReport@download')->name('download_report');
    Route::get('/retrospective/get-all-file', 'RetrospectiveReport@GetReportPerPage')->name('retrospective_get_report');

    Route::get('/search/report', 'RetrospectiveReport@search')->name('search_report');

    Route::prefix('reorder')->group(function () {
        Route::get('/', 'ReOrderController@index')->name('reorder');
        Route::post('/add', 'ReOrderController@addReport')->name('addReport');
        Route::get('/sort/hosp', 'ReOrderController@sortHosp')->name('reorder-sort-hosp');
        Route::get('/sort/area_code', 'ReOrderController@sortAreaCode')->name('reorder-sort-area_code');
        Route::get('/monthly', 'ReOrderController@monthlyCreateJobs')->name('reorder_monthly');
    });

    // Route::get('/manage/users', 'ManageUsers@index')->name('manage_users');
    // Route::get('/search/user', 'ManageUsers@search')->name('search_user');

    Route::get('/manage/cases', 'ManageCases@index')->name('manage_cases');

    // Route::get('/manage/hospitals', 'ManageHospitals@index')->name('manage_hospitals');
    // Route::get('/search/hospital', 'ManageHospitals@search')->name('manage_hospitals_search');
    // Route::get('/edit/hospital', 'ManageHospitals@form')->name('edit_hospital_form');
    // Route::get('/edit/hospital/{id}', 'ManageHospitals@edit')->name('edit_hospital');
    // Route::post('/update/hospital', 'ManageHospitals@update')->name('update_hospital');
    // Route::post('/create/hospital', 'ManageHospitals@create')->name('create_hospital');
    // Route::get('/delete/hospital/{id}', 'ManageHospitals@delete')->name('delete_hospital');

    Route::get('/update/case/{id}', 'UpdateCaseController@index')->name('update_case_controller');
    Route::post('/update/case', 'UpdateCaseController@submit')->name('submit_new_case');

    // Route::get('/update/password/{id}', 'UpdatePasswordController@index')->name('update_password_controller');
    // Route::post('/update/password', 'UpdatePasswordController@submit')->name('submit_new_password');

    Route::get('/checking', 'CheckingController@checking')->name('check'); //checking api
    Route::get('/check/job/{id}', 'CheckingController@selectedCheck')->name('selected_check');

    Route::get('/history', 'HistoryController@index')->name('history');
});
