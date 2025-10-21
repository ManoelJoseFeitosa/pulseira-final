<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'device_uuid',
        'status',
    ];

    /**
     * Get the patient that owns the device.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the sensor readings for the device.
     */
    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }
}
