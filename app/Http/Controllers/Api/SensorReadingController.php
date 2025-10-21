<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Notifications\StressAlertNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class SensorReadingController extends Controller
{
    /**
     * Armazena uma nova leitura de sensor e dispara alertas se necessário.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_uuid'    => 'required|string|exists:devices,device_uuid',
            'temperature'    => 'required|numeric',
            'heart_rate'     => 'required|integer',
            'gsr_value'      => 'required|integer',
            'movement_level' => 'required|numeric',
            'status_level'   => 'required|string|in:normal,atencao,alerta_vermelho',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $device = Device::where('device_uuid', $request->input('device_uuid'))->firstOrFail();

        try {
            $reading = $device->sensorReadings()->create($request->all());

            $statusLevel = $reading->status_level;

            if ($statusLevel === 'atencao' || $statusLevel === 'alerta_vermelho') {
                $patientName = $device->patient->name;

                $message = ($statusLevel === 'atencao')
                    ? "ATENÇÃO: Nível de stress MODERADO detectado para {$patientName}."
                    : "ALERTA: Nível de stress ALTO detectado para {$patientName}.";

                $caregivers = $device->patient->tenant->users;

                // Escreve no arquivo de log antes de tentar enviar.
                Log::info("Tentando enviar notificação via Twilio para ".count($caregivers)." cuidadores.");

                Notification::send($caregivers, new StressAlertNotification($message));
            }

            return response()->json([
                'message' => 'Data received successfully',
                'data' => $reading
            ], 201);

        } catch (\Exception $e) {
            // Adiciona o erro real ao log para depuração futura
            Log::error("Falha ao enviar notificação: " . $e->getMessage());

            return response()->json(['message' => 'Failed to store data', 'error' => $e->getMessage()], 500);
        }
    }
}
