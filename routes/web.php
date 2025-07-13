<?php

use App\Http\Controllers\AuthCallbackController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// routes/web.php (ชั่วคราวเพื่อ debug)
Route::get('/env-check', function () {
    return [
        'DB_CONNECTION' => env('DB_CONNECTION'),
        'DB_DATABASE' => env('DB_DATABASE'),
        'DB_HOST' => env('DB_HOST'),
    ];
});

Route::get('/debug-db', function () {
    return [
        'env_DB_HOST' => env('DB_HOST'),
        'config_DB_HOST' => config('database.connections.mysql.host'),
    ];
});

Route::view('/', 'auth.login');

Route::prefix('login')->group(function () {
    Route::prefix('thaid')->group(function () {
        Route::get('/', 'Auth\ThaIDController@thaid')->name('login.thaid');
        Route::get('/check', 'Auth\ThaIDController@check_login_thaid')->name('login.thaid.check');
    });
});

Route::prefix('dashboard')->group(function () {
    Route::get('/hospital-21-variables', 'DashboardController@hospital_21_variables')->name('dashboard.hospital_21_variables');
    Route::post('/hospital-21-variables', 'DashboardController@hospital_21_variables')->name('dashboard.hospital_21_variables');
    Route::get('/hospital-overview', 'DashboardController@hospital_overview')->name('dashboard.hospital_overview');
    Route::post('/hospital-overview', 'DashboardController@hospital_overview')->name('dashboard.hospital_overview');
    Route::get('/get-province-from-health-zone', 'DashboardController@get_province_from_health_zone')->name('dashboard.get_province_from_health_zone');
    Route::get('/get-hospital-from-province', 'DashboardController@get_hospital_from_province')->name('dashboard.get_hospital_from_province');
    Route::get('/get-hospital-asm1-from-province', 'DashboardController@get_hospital_asm1_from_province')->name('dashboard.get_hospital_asm1_from_province');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('thaid')->group(function () {
        Route::get('/register_step_2', 'Auth\ThaIDController@index_register_step_2')->name('thaid.index_register_step_2');
        Route::post('/register_step_2/update', 'Auth\ThaIDController@update_register_step_2')->name('thaid.update_register_step_2');
    });

    Route::middleware(['check.username_and_type'])->group(function () {
        Route::get('/', 'PresentReportController@index')->name('home');
        Route::get('/present/report', 'PresentReportController@index')->name('present_report');
        Route::post('/present/report', 'PresentReportController@index')->name('present_report');
        // Route::get('/search/report/present', 'PresentReportController@search')->name('search_present_report');

        Route::get('/retrospective/report', 'RetrospectiveReport@index')->name('retrospective_report');
        Route::get('/download/report/{id}', 'RetrospectiveReport@download')->name('download_report');
        Route::get('/retrospective/get-all-file', 'RetrospectiveReport@GetReportPerPage')->name('retrospective_get_report');

        Route::get('/search/report', 'RetrospectiveReport@search')->name('search_report');

        Route::get('/reorder', 'ReOrderController@index')->name('reorder');
        Route::post('/reorder/add', 'ReOrderController@addReport')->name('addReport');
        Route::get('/reorder/sort/hosp', 'ReOrderController@sortHosp')->name('reorder-sort-hosp');
        Route::get('/reorder/sort/area_code', 'ReOrderController@sortAreaCode')->name('reorder-sort-area_code');

        Route::get('/reorder/monthly', 'ReOrderController@monthlyCreateJobs')->name('reorder_monthly');

        Route::get('/manage/users', 'ManageUsers@index')->name('manage_users');
        Route::get('/search/user', 'ManageUsers@search')->name('search_user');

        Route::get('/manage/cases', 'ManageCases@index')->name('manage_cases');

        Route::get('/manage/hospitals', 'ManageHospitals@index')->name('manage_hospitals');
        Route::get('/search/hospital', 'ManageHospitals@search')->name('manage_hospitals_search');
        Route::get('/edit/hospital', 'ManageHospitals@form')->name('edit_hospital_form');
        Route::get('/edit/hospital/{id}', 'ManageHospitals@edit')->name('edit_hospital');
        Route::post('/update/hospital', 'ManageHospitals@update')->name('update_hospital');
        Route::post('/create/hospital', 'ManageHospitals@create')->name('create_hospital');
        Route::get('/delete/hospital/{id}', 'ManageHospitals@delete')->name('delete_hospital');

        Route::get('/update/case/{id}', 'UpdateCaseController@index')->name('update_case_controller');
        Route::post('/update/case', 'UpdateCaseController@submit')->name('submit_new_case');


        // Route::get('/update/password/{id}', 'UpdatePasswordController@index')->name('update_password_controller');
        // Route::post('/update/password', 'UpdatePasswordController@submit')->name('submit_new_password');

        Route::get('/checking', 'CheckingController@checking')->name('check'); //checking api
        Route::get('/check/job/{id}', 'CheckingController@selectedCheck')->name('selected_check');

        Route::get('/history', 'HistoryController@index')->name('history');
    });
});

Route::get('/case_1_test/{icdcause}/{injby}', 'CheckingController@case_1_test')->name('case_1_test'); //ccase_1_test

Route::view('/welcome', 'welcome');

// รับ token จากระบบหลักผ่าน URL เช่น /auth_callback?token=xxxxx
Route::get('/auth_callback', [AuthCallbackController::class, 'handle'])->name('auth.callback');
//Route::get('/build','BuildUsersController@index')->name('build_user');

Auth::routes();
