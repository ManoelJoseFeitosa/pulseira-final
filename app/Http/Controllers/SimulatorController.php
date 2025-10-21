<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device; // Importe o Model Device

class SimulatorController extends Controller
{
    // Usamos Route Model Binding: o Laravel busca o dispositivo
    // automaticamente pelo UUID na URL.
    public function show(Device $device)
    {
        return view('simulator', [
            'device' => $device
        ]);
    }
}
