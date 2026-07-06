<?php

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', 'App\Http\Controllers\Web\AuthController@showLogin')->name('login');
    Route::post('/login', 'App\Http\Controllers\Web\AuthController@login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', 'App\Http\Controllers\Web\AuthController@logout')->name('logout');

    Route::get('/', 'App\Http\Controllers\Web\DashboardController@index')->name('dashboard');
    Route::get('/dashboard', 'App\Http\Controllers\Web\DashboardController@index');

    // Surveys
    Route::get('/surveys/{survey}', 'App\Http\Controllers\Web\SurveyController@show')->name('surveys.show');
    Route::put('/surveys/{survey}', 'App\Http\Controllers\Web\SurveyController@update')->name('surveys.update');
    Route::post('/surveys/{survey}/duplicate', 'App\Http\Controllers\Web\SurveyController@duplicate')->name('surveys.duplicate');

    // Questions
    Route::post('/surveys/{survey}/questions', 'App\Http\Controllers\Web\QuestionController@store')->name('questions.store');
    Route::get('/questions/{question}/edit', 'App\Http\Controllers\Web\QuestionController@edit')->name('questions.edit');
    Route::put('/questions/{question}', 'App\Http\Controllers\Web\QuestionController@update')->name('questions.update');
    Route::delete('/questions/{question}', 'App\Http\Controllers\Web\QuestionController@destroy')->name('questions.destroy');
    Route::patch('/questions/{question}/toggle', 'App\Http\Controllers\Web\QuestionController@toggle')->name('questions.toggle');
    Route::post('/surveys/{survey}/questions/reorder', 'App\Http\Controllers\Web\QuestionController@reorder')->name('questions.reorder');

    // Reports
    Route::get('/surveys/{survey}/reports', 'App\Http\Controllers\Web\ReportController@show')->name('reports.show');
    Route::get('/surveys/{survey}/reports/export', 'App\Http\Controllers\Web\ReportController@export')->name('reports.export');
});
