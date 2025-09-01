<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceCheckIn extends Model
{
    use HasFactory;

    protected $table = 'service_check_ins';

    protected $fillable = [
        'check_in_id',
        'service_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el check-in
     */
    public function checkIn(): BelongsTo
    {
        return $this->belongsTo(CheckIn::class);
    }

    /**
     * Relación con el servicio
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Obtener el vehículo a través del check-in
     */
    public function getCarAttribute(): ?Car
    {
        return $this->checkIn?->car;
    }

    /**
     * Obtener el cliente a través del check-in y vehículo
     */
    public function getClientAttribute(): ?Client
    {
        return $this->car?->client;
    }

    /**
     * Scope para un check-in específico
     */
    public function scopeForCheckIn($query, int $checkInId)
    {
        return $query->where('check_in_id', $checkInId);
    }

    /**
     * Scope para un servicio específico
     */
    public function scopeForService($query, int $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Scope para check-ins completados
     */
    public function scopeCompleted($query)
    {
        return $query->whereHas('checkIn', function ($q) {
            $q->completed();
        });
    }

    /**
     * Scope para check-ins pendientes
     */
    public function scopePending($query)
    {
        return $query->whereHas('checkIn', function ($q) {
            $q->pending();
        });
    }
}
