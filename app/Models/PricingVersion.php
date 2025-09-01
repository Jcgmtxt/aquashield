<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class PricingVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'effective_date',
        'end_date',
        'cost',
        'negotiation_margin',
        'min_margin_percent',
        'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'end_date' => 'date',
        'cost' => 'integer',
        'negotiation_margin' => 'integer',
        'min_margin_percent' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el servicio
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relación con precios por tamaño
     */
    public function pricingBySize(): HasMany
    {
        return $this->hasMany(CorrosionPricingBySize::class, 'version_id');
    }

    /**
     * Relación con vehículos más usados
     */
    public function mostUsedVehicles(): HasMany
    {
        return $this->hasMany(MostUsedVehicle::class, 'version_id');
    }

    /**
     * Relación con servicios aplicados
     */
    public function appliedServices(): HasMany
    {
        return $this->hasMany(AppliedService::class, 'pricing_version_id');
    }

    /**
     * Verificar si la versión está activa
     */
    public function isActive(): bool
    {
        $now = Carbon::now()->toDateString();
        
        return $this->effective_date <= $now && 
               ($this->end_date === null || $this->end_date >= $now);
    }

    /**
     * Obtener precio por tamaño de vehículo
     */
    public function getPriceForVehicleSize(string $vehicleSize): ?CorrosionPricingBySize
    {
        return $this->pricingBySize()
            ->where('vehicle_size', $vehicleSize)
            ->first();
    }

    /**
     * Verificar si un vehículo tiene precio especial
     */
    public function getSpecialPriceForVehicle(string $brand, string $model, string $year = null): ?MostUsedVehicle
    {
        $query = $this->mostUsedVehicles()
            ->where('brand', 'LIKE', '%' . $brand . '%')
            ->where('model', 'LIKE', '%' . $model . '%')
            ->where('is_active', true);

        if ($year) {
            $query->where(function ($q) use ($year) {
                $q->whereNull('year_range')
                  ->orWhereRaw("? BETWEEN SUBSTRING_INDEX(year_range, '-', 1) AND SUBSTRING_INDEX(year_range, '-', -1)", [$year]);
            });
        }

        return $query->first();
    }

    /**
     * Scope para versiones activas
     */
    public function scopeActive($query)
    {
        $now = Carbon::now()->toDateString();
        
        return $query->where('effective_date', '<=', $now)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $now);
                    });
    }

    /**
     * Scope para un servicio específico
     */
    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }
}
