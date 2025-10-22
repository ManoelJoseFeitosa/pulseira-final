<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SensorReading;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $readings = SensorReading::whereHas('device.patient', function ($query) use ($user) {
            $query->where('tenant_id', $user->tenant_id);
        })
        ->with('device.patient')
        ->latest('timestamp')
        ->take(15)
        ->get()
        ->reverse(); // Inverte para o gráfico mostrar em ordem cronológica

        // Prepara os dados para serem usados pelo Chart.js
        $chartLabels = $readings->pluck('timestamp')->map(fn($date) => $date->format('H:i:s'));
        $temperatureData = $readings->pluck('temperature');
        $stressData = $readings->pluck('gsr_value');
        
        return view('dashboard', [
            'readings' => $readings->reverse(), // Reverte de novo para a tabela mostrar o mais novo em cima
            'chartLabels' => $chartLabels,
            'temperatureData' => $temperatureData,
            'stressData' => $stressData,
        ]);
    }

    /**
    * Fornece os dados do gráfico E DA TABELA como JSON.
    */
    public function getChartData()
    {
        $user = Auth::user();

        $readingsQuery = SensorReading::whereHas('device.patient', function ($query) use ($user) {
            $query->where('tenant_id', $user->tenant_id);
        })
        ->with('device.patient') // Otimização
        ->latest('timestamp')
        ->take(15);

        // Clonamos a query para não executar duas vezes a mesma busca
        $readingsForChart = (clone $readingsQuery)->get()->reverse();
        $readingsForTable = (clone $readingsQuery)->get();

        // Formata os dados para a tabela, já prontos para o JS
        $latestReadings = $readingsForTable->map(function ($reading) {
        return [
            'patient_name' => $reading->device->patient->name,
            'timestamp'    => $reading->timestamp->format('d/m/Y H:i:s'),
            'temperature'  => $reading->temperature . ' °C',
            'heart_rate'   => $reading->heart_rate . ' bpm',
            'gsr_value'    => $reading->gsr_value,
            'status_level' => $reading->status_level,
            'status_level_formatted' => ucwords(str_replace('_', ' ', $reading->status_level)),
        ];
        });

        return response()->json([
            // Dados para o gráfico
            'labels'          => $readingsForChart->pluck('timestamp')->map(fn($date) => $date->format('H:i:s')),
            'temperatureData' => $readingsForChart->pluck('temperature'),
            'stressData'      => $readingsForChart->pluck('gsr_value'),
            // Dados para a tabela
            'latestReadings'  => $latestReadings,
        ]);
    }

    /**
 * Busca dados históricos para o relatório por período.
 */
public function getReportData(Request $request)
{
    // Valida se as datas foram enviadas e estão no formato correto
    $validated = $request->validate([
        'start_date' => 'required|date_format:Y-m-d',
        'end_date'   => 'required|date_format:Y-m-d',
    ]);

    $user = Auth::user();
    $startDate = $validated['start_date'] . ' 00:00:00';
    $endDate = $validated['end_date'] . ' 23:59:59';

    // Query principal para buscar os dados no período
    $query = SensorReading::whereHas('device.patient', function ($q) use ($user) {
        $q->where('tenant_id', $user->tenant_id);
    })->whereBetween('timestamp', [$startDate, $endDate]);

    // **** CORREÇÃO AQUI ****
    // Agora, para cada estatística, nós clonamos a query original
    // para garantir que os filtros não se acumulem.
    $statistics = [
        'total_readings'    => (clone $query)->count(),
        'avg_temperature'   => round((clone $query)->avg('temperature'), 2),
        'max_stress'        => (clone $query)->max('gsr_value'),
        'attention_alerts'  => (clone $query)->where('status_level', 'atencao')->count(),
        'high_stress_alerts' => (clone $query)->where('status_level', 'alerta_vermelho')->count(),
    ];

    // Busca os registros detalhados, ordenados por data
    $detailedReadings = $query->with('device.patient')->latest('timestamp')->get();

    return response()->json([
        'statistics' => $statistics,
        'readings' => $detailedReadings,
    ]);
}
}
