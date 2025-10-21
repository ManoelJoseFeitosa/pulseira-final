<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Painel de Acompanhamento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Evolução dos Sensores</h3>
                    <div>
                        <canvas id="sensorChart"
                            data-labels="{{ $chartLabels->toJson() }}"
                            data-temperatures="{{ $temperatureData->toJson() }}"
                            data-stresses="{{ $stressData->toJson() }}"
                        ></canvas>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temperatura</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batimentos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stress (GSR)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
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
                                                @if($reading->status_level == 'alerta_vermelho') bg-red-100 text-red-800 @endif
                                            ">
                                                {{ ucwords(str_replace('_', ' ', $reading->status_level)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Nenhum dado recebido ainda. Tente usar o simulador.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const canvas = document.getElementById('sensorChart');
        let sensorChart;

        // MUDANÇA AQUI: Nova função para atualizar a tabela
        function updateTable(readings) {
            const tableBody = document.getElementById('readings-table-body');
            if (!tableBody) return;

            // Limpa a tabela atual
            tableBody.innerHTML = '';

            // Se não houver leituras, mostra uma mensagem
            if (readings.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Nenhum dado recebido ainda.</td></tr>`;
                return;
            }

            // Cria as novas linhas da tabela
            readings.forEach(reading => {
                let statusClass = '';
                if (reading.status_level === 'normal') statusClass = 'bg-green-100 text-green-800';
                if (reading.status_level === 'atencao') statusClass = 'bg-yellow-100 text-yellow-800';
                if (reading.status_level === 'alerta_vermelho') statusClass = 'bg-red-100 text-red-800';

                const row = `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">${reading.patient_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${reading.timestamp}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${reading.temperature}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${reading.heart_rate}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${reading.gsr_value}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                ${reading.status_level_formatted}
                            </span>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        }

        function updateChartData(chart, labels, tempData, stressData) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = tempData;
            chart.data.datasets[1].data = stressData;
            chart.update();
        }

        async function fetchUpdatedData() {
            try {
                const response = await fetch('{{ route("dashboard.chart-data") }}');
                const data = await response.json();
                
                // MUDANÇA AQUI: Chama as duas funções de atualização
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

            // Inicia o Polling: busca novos dados a cada 10 segundos
            setInterval(fetchUpdatedData, 10000);
        }
    </script>
    @endpush
</x-app-layout>
