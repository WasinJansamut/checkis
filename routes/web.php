<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::view('/', 'auth.login');

Route::group(['middleware'=>'auth'] , function(){
    Route::get('/', 'PresentReportController@index');
    Route::get('/present/report', 'PresentReportController@index')->name('present_report');
    Route::get('/search/report/present', 'PresentReportController@search')->name('search_present_report');

    Route::get('/retrospective/report','RetrospectiveReport@index')->name('retrospective_report');
    Route::get('/download/report/{id}','RetrospectiveReport@download')->name('download_report');
    Route::get('/retrospective/get-all-file', 'RetrospectiveReport@GetReportPerPage')->name('retrospective_get_report');

    Route::get('/search/report', 'RetrospectiveReport@search')->name('search_report');

    Route::get('/reorder','ReOrderController@index')->name('reorder');
    Route::post('/reorder/add','ReOrderController@addReport')->name('addReport');
    Route::get('/reorder/sort/hosp','ReOrderController@sortHosp')->name('reorder-sort-hosp');
    Route::get('/reorder/sort/area_code','ReOrderController@sortAreaCode')->name('reorder-sort-area_code');

    Route::get('/reorder/monthly','ReOrderController@monthlyCreateJobs')->name('reorder_monthly');

    Route::get('/manage/users','ManageUsers@index')->name('manage_users');
    Route::get('/search/user', 'ManageUsers@search')->name('search_user');

    Route::get('/manage/cases','ManageCases@index')->name('manage_cases');

    Route::get('/manage/hospitals','ManageHospitals@index')->name('manage_hospitals');
    Route::get('/search/hospital', 'ManageHospitals@search')->name('manage_hospitals_search');
    Route::get('/edit/hospital', 'ManageHospitals@form')->name('edit_hospital_form');
    Route::get('/edit/hospital/{id}', 'ManageHospitals@edit')->name('edit_hospital');
    Route::post('/update/hospital', 'ManageHospitals@update')->name('update_hospital');
    Route::post('/create/hospital', 'ManageHospitals@create')->name('create_hospital');
    Route::get('/delete/hospital/{id}', 'ManageHospitals@delete')->name('delete_hospital');

    Route::get('/update/case/{id}', 'UpdateCaseController@index')->name('update_case_controller');
    Route::post('/update/case', 'UpdateCaseController@submit')->name('submit_new_case');


    Route::get('/update/password/{id}', 'UpdatePasswordController@index')->name('update_password_controller');
    Route::post('/update/password', 'UpdatePasswordController@submit')->name('submit_new_password');

    Route::get('/checking','CheckingController@checking')->name('check'); //checking api
    Route::get('/check/job/{id}', 'CheckingController@selectedCheck')->name('selected_check');

    Route::get('/history', 'HistoryController@index')->name('history');

});




Route::view('/welcome', 'welcome');




//Route::get('/build','BuildUsersController@index')->name('build_user');

Auth::routes();




