<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Pulseira Autism Watc</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; padding: 2rem; background-color: #f4f4f9; }
        .simulator { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #333; }
        small { color: #777; font-family: monospace; }
        .buttons { margin-top: 1.5rem; display: flex; gap: 1rem; }
        button { padding: 0.8rem 1.5rem; border: none; border-radius: 5px; color: white; font-size: 1rem; cursor: pointer; transition: transform 0.2s; }
        button:hover { transform: scale(1.05); }
        .normal { background-color: #28a745; }
        .atencao { background-color: #ffc107; color: #333; }
        .alerta { background-color: #dc3545; }
        #response { margin-top: 1.5rem; padding: 1rem; background: #e9ecef; border-radius: 5px; min-width: 400px; text-align: left; font-family: monospace; white-space: pre-wrap; word-break: break-all; }
    </style>
</head>
<body>

    <div class="simulator">
        <h1>Simulador de Pulseira Autism Watc</h1>
        <p>Enviando dados para o dispositivo:</p>
        <small>{{ $device->device_uuid }}</small>

        <div class="buttons">
            <button class="normal" onclick="sendData('normal')">Nível Normal</button>
            <button class="atencao" onclick="sendData('atencao')">Nível de Atenção</button>
            <button class="alerta" onclick="sendData('alerta_vermelho')">Alerta Vermelho</button>
        </div>

        <div id="response">Aguardando envio...</div>
    </div>

    <script>
        async function sendData(level) {
            const deviceUuid = '{{ $device->device_uuid }}';
            const responseDiv = document.getElementById('response');
            responseDiv.textContent = 'Enviando dados...';

            let dataPayload = {
                device_uuid: deviceUuid,
                status_level: level
            };

            switch (level) {
                case 'normal':
                    dataPayload.temperature = 36.5; dataPayload.heart_rate = 80; dataPayload.gsr_value = 400; dataPayload.movement_level = 0.1;
                    break;
                case 'atencao':
                    dataPayload.temperature = 37.6; dataPayload.heart_rate = 105; dataPayload.gsr_value = 950; dataPayload.movement_level = 1.3;
                    break;
                case 'alerta_vermelho':
                    dataPayload.temperature = 38.7; dataPayload.heart_rate = 125; dataPayload.gsr_value = 1600; dataPayload.movement_level = 2.8;
                    break;
            }

            try {
                const response = await fetch('/readings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        // PARTE 2: ENVIANDO O TOKEN PARA O SERVIDOR
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(dataPayload)
                });

                const result = await response.json();

                if (response.ok) {
                    responseDiv.textContent = 'Sucesso!\n\n' + JSON.stringify(result, null, 2);
                } else {
                    responseDiv.textContent = 'Erro!\n\n' + JSON.stringify(result, null, 2);
                }

            } catch (error) {
                responseDiv.textContent = 'Erro de rede!\n\n' + error.message;
            }
        }
    </script>
</body>
</html>