<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Painel de Acompanhamento') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'dashboard' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'dashboard'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'dashboard', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'dashboard' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Painel em Tempo Real
                    </button>
                    <button @click="activeTab = 'reports'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'reports', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'reports' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Relatórios
                    </button>
                </nav>
            </div>

            <div x-show="activeTab === 'dashboard'">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Evolução dos Sensores</h3>
                        <div>
                            <canvas id="sensorChart" data-labels="{{ $chartLabels->toJson() }}" data-temperatures="{{ $temperatureData->toJson() }}" data-stresses="{{ $stressData->toJson() }}"></canvas>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Últimas Leituras da Pulseira</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data/Hora</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Temperatura</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batimentos</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stress (GSR)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="readings-table-body" class="bg-white divide-y divide-gray-200">
                                    @forelse ($readings as $reading)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $reading->device->patient->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $reading->timestamp->format('d/m/Y H:i:s') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $reading->temperature }} °C</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $reading->heart_rate }} bpm</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $reading->gsr_value }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($reading->status_level == 'normal') bg-green-100 text-green-800 @endif
                                                    @if($reading->status_level == 'atencao') bg-yellow-100 text-yellow-800 @endif
                                                    @if($reading->status_level == 'alerta_vermelho') bg-red-100 text-red-800 @endif">
                                                    {{ ucwords(str_replace('_', ' ', $reading->status_level)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum dado recebido ainda.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'reports'" style="display: none;">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Gerar Relatório por Período</h3>
                        <form id="report-form" class="flex flex-wrap items-end gap-4 mb-6">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Data de Início</label>
                                <input type="date" id="start_date" name="start_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                                <input type="date" id="end_date" name="end_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">Gerar Relatório</button>
                        </form>
                        <div id="report-results">
                            <p class="text-gray-500">Selecione um período e clique em "Gerar Relatório" para ver os dados.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ====================================================================
        // LÓGICA PARA O PAINEL EM TEMPO REAL (GRÁFICO E TABELA)
        // ====================================================================
        const canvas = document.getElementById('sensorChart');
        let sensorChart;

        function updateTable(readings) {
            const tableBody = document.getElementById('readings-table-body');
            if (!tableBody) return;
            tableBody.innerHTML = '';
            if (readings.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Nenhum dado recebido ainda.</td></tr>`;
                return;
            }
            readings.forEach(reading => {
                let statusClass = '';
                if (reading.status_level === 'normal') statusClass = 'bg-green-100 text-green-800';
                if (reading.status_level === 'atencao') statusClass = 'bg-yellow-100 text-yellow-800';
                if (reading.status_level === 'alerta_vermelho') statusClass = 'bg-red-100 text-red-800';
                const row = `<tr>
                    <td class="px-6 py-4 whitespace-nowrap">${reading.patient_name}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${reading.timestamp}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${reading.temperature}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${reading.heart_rate}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${reading.gsr_value}</td>
                    <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${reading.status_level_formatted}</span></td>
                </tr>`;
                tableBody.innerHTML += row;
            });
        }

        function updateChartData(chart, labels, tempData, stressData) {
            if (!chart) return;
            chart.data.labels = labels;
            chart.data.datasets[0].data = tempData;
            chart.data.datasets[1].data = stressData;
            chart.update();
        }

        async function fetchUpdatedData() {
            try {
                const response = await fetch('{{ route("dashboard.chart-data") }}');
                const data = await response.json();
                updateChartData(sensorChart, data.labels, data.temperatureData, data.stressData);
                updateTable(data.latestReadings);
            } catch (error) {
                console.error('Erro ao buscar dados atualizados:', error);
            }
        }

        if (canvas) {
            const initialLabels = JSON.parse(canvas.dataset.labels);
            const initialTemperatureData = JSON.parse(canvas.dataset.temperatures);
            const initialStressData = JSON.parse(canvas.dataset.stresses);
            sensorChart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: initialLabels,
                    datasets: [
                        { label: 'Temperatura (°C)', data: initialTemperatureData, borderColor: 'rgb(255, 99, 132)', backgroundColor: 'rgba(255, 99, 132, 0.5)', yAxisID: 'y' },
                        { label: 'Nível de Stress (GSR)', data: initialStressData, borderColor: 'rgb(54, 162, 235)', backgroundColor: 'rgba(54, 162, 235, 0.5)', yAxisID: 'y1' }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Temperatura' } },
                        y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Stress (GSR)' }, grid: { drawOnChartArea: false } }
                    }
                }
            });
            setInterval(fetchUpdatedData, 10000);
        }

        // ====================================================================
        // LÓGICA PARA A ABA DE RELATÓRIOS
        // ====================================================================
        document.getElementById('report-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const resultsDiv = document.getElementById('report-results');
            resultsDiv.innerHTML = '<p class="text-gray-500">Gerando relatório...</p>';

            try {
                const url = `{{ route('dashboard.report-data') }}?start_date=${startDate}&end_date=${endDate}`;
                const response = await fetch(url);
                const data = await response.json();
                renderReport(data);
            } catch (error) {
                console.error('Erro ao gerar relatório:', error);
                resultsDiv.innerHTML = '<p class="text-red-500">Ocorreu um erro ao gerar o relatório.</p>';
            }
        });

        function renderReport(data) {
            const resultsDiv = document.getElementById('report-results');
            const stats = data.statistics;
            let reportHtml = `<h4 class="text-md font-medium text-gray-800 mb-4">Resumo do Período</h4>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8 text-center">
                    <div class="p-4 bg-gray-100 rounded-lg"><p class="text-sm text-gray-600">Total de Leituras</p><p class="text-2xl font-bold">${stats.total_readings}</p></div>
                    <div class="p-4 bg-blue-100 rounded-lg"><p class="text-sm text-gray-600">Temp. Média</p><p class="text-2xl font-bold">${stats.avg_temperature}°C</p></div>
                    <div class="p-4 bg-yellow-100 rounded-lg"><p class="text-sm text-gray-600">Pico de Stress</p><p class="text-2xl font-bold">${stats.max_stress}</p></div>
                    <div class="p-4 bg-orange-100 rounded-lg"><p class="text-sm text-gray-600">Alertas de Atenção</p><p class="text-2xl font-bold">${stats.attention_alerts}</p></div>
                    <div class="p-4 bg-red-100 rounded-lg"><p class="text-sm text-gray-600">Alertas Vermelhos</p><p class="text-2xl font-bold">${stats.high_stress_alerts}</p></div>
                </div>
                <h4 class="text-md font-medium text-gray-800 mb-4">Registros Detalhados</h4>`;

            reportHtml += '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data/Hora</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Temperatura</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stress (GSR)</th></tr></thead><tbody class="bg-white divide-y divide-gray-200">';
            if (data.readings.length > 0) {
                data.readings.forEach(reading => {
                    let statusClass = '';
                    if (reading.status_level === 'normal') statusClass = 'bg-green-100 text-green-800';
                    if (reading.status_level === 'atencao') statusClass = 'bg-yellow-100 text-yellow-800';
                    if (reading.status_level === 'alerta_vermelho') statusClass = 'bg-red-100 text-red-800';
                    const formattedTimestamp = new Date(reading.timestamp).toLocaleString('pt-BR');
                    reportHtml += `<tr>
                        <td class="px-6 py-4">${formattedTimestamp}</td>
                        <td class="px-6 py-4"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${ucwords(reading.status_level.replace('_', ' '))}</span></td>
                        <td class="px-6 py-4">${reading.temperature}°C</td>
                        <td class="px-6 py-4">${reading.gsr_value}</td>
                    </tr>`;
                });
            } else {
                reportHtml += '<tr><td colspan="4" class="px-6 py-4 text-center">Nenhum registro encontrado para este período.</td></tr>';
            }
            reportHtml += '</tbody></table></div>';
            resultsDiv.innerHTML = reportHtml;
        }

        function ucwords(str) {
            return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
        }
    </script>
    @endpush
</x-app-layout>