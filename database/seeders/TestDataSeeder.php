<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Patient;
use App\Models\Device;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cria um Tenant (uma família)
        $tenant = Tenant::create(['name' => 'Família Teste']);

        // 2. Cria um Paciente associado a esse Tenant
        $patient = $tenant->patients()->create([
            'name' => 'João Teste',
            'birth_date' => '2015-01-10',
        ]);

        // 3. Cria um Dispositivo (pulseira) para o Paciente
        $device = $patient->device()->create([
            'device_uuid' => Str::uuid(),
            'status' => 'active',
        ]);

        // 4. Exibe o UUID no console para facilitar o teste
        $this->command->info('Dados de teste criados com sucesso!');
        $this->command->info('Use este UUID para acessar o simulador:');
        $this->command->warn($device->device_uuid);
    }
}
