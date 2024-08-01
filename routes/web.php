<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalRapatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function() {
//     return view('maintenance');
// })->name('index');
Route::get('/old', [KegiatanController::class, 'index'])->name('chart-index-old');
Route::get('/', [DashboardController::class, 'index'])->name('chart-index');
Route::get('/jadwal-rapat', function() {
    return view('rapatTabel');
});

Route::get('/calendar-rapat', function() { return view('maintenance'); });
// Route::get('/calendar-rapat', [JadwalRapatController::class, 'calendar']);

Route::get('/tabel', [JadwalRapatController::class, 'table'])->name('tabel.data');
Route::get('/calendar', [JadwalRapatController::class, 'calendar'])->name('calendar');

Route::get('/testing', [KegiatanController::class, 'testing']);

Route::get('/data', [KegiatanController::class, 'datatable'])->name('index.data');
Route::get('/export', [KegiatanController::class, 'export'])->name('export.data');
// Route::get('/api/statistic', [ChartController::class, 'statistic']);
// Route::get('/api/total', [ChartController::class, 'total']);
// Route::get('/api/authority', [ChartController::class, 'authority']);
// Route::get('/api/province', [ChartController::class, 'province']);
// Route::get('/api/cluster', [ChartController::class, 'cluster']);

// GET DATA APIs
Route::get('statistic', [DashboardController::class, 'statistic'])->name('api.statistic');
Route::get('sppl_total', [DashboardController::class, 'sppl_total'])->name('api.sppl_total');
Route::get('uklupl_total', [DashboardController::class, 'uklupl_total'])->name('api.uklupl_total');
Route::get('totalByDate', [DashboardController::class, 'totalByDate'])->name('api.totalByDate');
Route::get('totalSpplByAuthority', [DashboardController::class, 'totalSpplByAuthority'])->name('api.totalSpplByAuthority');
Route::get('totalUkluplByAuthority', [DashboardController::class, 'totalUkluplByAuthority'])->name('api.totalUkluplByAuthority');
Route::get('cluster', [DashboardController::class, 'cluster'])->name('api.cluster');
Route::get('ByProvince', [DashboardController::class, 'ByProvince'])->name('api.ByProvince');
Route::get('datatable_sppl', [DashboardController::class, 'datatable_sppl'])->name('datatable_sppl');
Route::get('datatable_r', [DashboardController::class, 'datatable_r'])->name('datatable_r');

// GET FILE APIs
Route::get('getSpplFile', [DashboardController::class, 'getSpplFile'])->name('getSpplFile');
Route::get('getPkplhFile', [DashboardController::class, 'getPkplhFile'])->name('getPkplhFile');
Route::get('getLampiranFile', [DashboardController::class, 'getLampiranFile'])->name('getLampiranFile');

Route::get('getKey', [Controller::class, 'getKey'])->name('getKey');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');