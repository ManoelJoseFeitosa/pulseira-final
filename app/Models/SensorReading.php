<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'temperature',
        'heart_rate',
        'gsr_value',
        'movement_level',
        'status_level',
        'timestamp',
    ];

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
        ];
    }

    /**
     * Get the device that the reading belongs to.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
