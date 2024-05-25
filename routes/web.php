<?php

use App\Http\Controllers\ChartController;
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
Route::get('/', [KegiatanController::class, 'index'])->name('chart-index');
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
Route::get('/api/statistic', [ChartController::class, 'statistic']);
Route::get('/api/total', [ChartController::class, 'total']);
Route::get('/api/authority', [ChartController::class, 'authority']);
Route::get('/api/province', [ChartController::class, 'province']);
Route::get('/api/cluster', [ChartController::class, 'cluster']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
