<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalRapatController;
use App\Http\Controllers\KegiatanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
// Route::get('/', [KegiatanController::class, 'index'])->name('chart-index');
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
Route::get('statistic', [DashboardController::class, 'statistic']);
Route::get('total', [DashboardController::class, 'total']);
Route::get('totalByDate', [DashboardController::class, 'totalByDate']);
Route::get('totalByAuthority', [DashboardController::class, 'totalByAuthority']);
Route::get('cluster', [DashboardController::class, 'cluster']);
Route::get('ByProvince', [DashboardController::class, 'ByProvince']);
Route::get('datatable_mr', [DashboardController::class, 'datatable_mr'])->name('datatable_mr');
Route::get('datatable_r', [DashboardController::class, 'datatable_r'])->name('datatable_r');

// GET FILE APIs
Route::get('getSpplFile', [DashboardController::class, 'getSpplFile'])->name('getSpplFile');
Route::get('getPkplhFile', [DashboardController::class, 'getPkplhFile'])->name('getPkplhFile');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');