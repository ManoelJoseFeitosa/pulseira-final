<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SimulatorController;
use App\Http\Controllers\Api\SensorReadingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Rota CORRIGIDA do Painel de Acompanhamento (Dashboard)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// Rota de API para os dados do gráfico do dashboard
Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])
    ->middleware(['auth'])->name('dashboard.chart-data');

// ROTA DE API para os dados do RELATÓRIO por período
Route::get('/dashboard/report-data', [DashboardController::class, 'getReportData'])
    ->middleware(['auth'])->name('dashboard.report-data');

// Rota ADICIONADA DE VOLTA para exibir a página do Simulador
Route::get('/simulator/{device:device_uuid}', [SimulatorController::class, 'show'])->name('simulator.show');

// Rota ADICIONADA DE VOLTA para RECEBER os dados do simulador
Route::post('/readings', [SensorReadingController::class, 'store'])->name('readings.store');


// Rotas de Perfil (criadas pelo Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Arquivo de rotas de autenticação (criado pelo Breeze)
require __DIR__.'/auth.php';