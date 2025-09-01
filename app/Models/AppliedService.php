<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AppliedService extends Model
{
    use HasFactory;

    protected $fillable = [
        'polymorphic_service_id',
        'polymorphic_service_type',
        'pricing_version_id',
        'car_id',
        'vehicle_size_applied',
        'vehicle_brand',
        'vehicle_model',
        'final_cost',
        'final_price',
        'margin_achieved',
        'discount_amount',
        'exception_used_id',
        'notes',
        'approved_by',
    ];

    protected $casts = [
        'polymorphic_service_id' => 'integer',
        'final_cost' => 'integer',
        'final_price' => 'integer',
        'margin_achieved' => 'decimal:2',
        'discount_amount' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Tipos de servicios disponibles
     */
    const SERVICE_TYPE_CORROSION_PROTECTION = 'corrosion_proteccion';
    const SERVICE_TYPE_GENERAL_PAINT = 'general_paint';
    const SERVICE_TYPE_CERAMIC_TREATMENT = 'ceramic_treatment';
    const SERVICE_TYPE_POLARIZE = 'polarize';
    const SERVICE_TYPE_PPF = 'PPF';
    const SERVICE_TYPE_MECHANICAL_WORKSHOP = 'mechanical_workshop';
    const SERVICE_TYPE_SPARE_PARTS = 'spar_parts';

    /**
     * Relación polimórfica con el servicio específico
     */
    public function polymorphicService(): MorphTo
    {
        return $this->morphTo('polymorphic_service', 'polymorphic_service_type', 'polymorphic_service_id');
    }

    /**
     * Relación con la versión de precios utilizada
     */
    public function pricingVersion(): BelongsTo
    {
        return $this->belongsTo(PricingVersion::class, 'pricing_version_id');
    }

    /**
     * Relación con el vehículo
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Relación con la excepción utilizada (si aplica)
     */
    public function exceptionUsed(): BelongsTo
    {
        return $this->belongsTo(MostUsedVehicle::class, 'exception_used_id');
    }

    /**
     * Relación con el usuario que aprobó (si aplica)
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Obtener el cliente a través del vehículo
     */
    public function getClientAttribute(): ?Client
    {
        return $this->car?->client;
    }

    /**
     * Verificar si se usó una excepción
     */
    public function usedException(): bool
    {
        return !is_null($this->exception_used_id);
    }

    /**
     * Verificar si tuvo descuento
     */
    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }

    /**
     * Verificar si requirió aprobación
     */
    public function wasApproved(): bool
    {
        return !is_null($this->approved_by);
    }

    /**
     * Calcular el precio original (antes del descuento)
     */
    public function getOriginalPriceAttribute(): int
    {
        return $this->final_price + $this->discount_amount;
    }

    /**
     * Obtener el porcentaje de descuento
     */
    public function getDiscountPercentageAttribute(): float
    {
        if ($this->original_price <= 0) {
            return 0;
        }
        
        return round(($this->discount_amount / $this->original_price) * 100, 2);
    }

    /**
     * Verificar si el margen está por debajo del mínimo
     */
    public function isBelowMinimumMargin(): bool
    {
        $minMargin = $this->pricingVersion->min_margin_percent;
        return $this->margin_achieved < $minMargin;
    }

    /**
     * Obtener el estado del margen
     */
    public function getMarginStatusAttribute(): string
    {
        $minMargin = $this->pricingVersion->min_margin_percent;
        
        if ($this->margin_achieved >= $minMargin * 1.5) {
            return 'excelente';
        } elseif ($this->margin_achieved >= $minMargin) {
            return 'bueno';
        } elseif ($this->margin_achieved >= $minMargin * 0.8) {
            return 'aceptable';
        } else {
            return 'bajo';
        }
    }

    /**
     * Scope para servicios aplicados en un rango de fechas
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope para servicios con descuento
     */
    public function scopeWithDiscount($query)
    {
        return $query->where('discount_amount', '>', 0);
    }

    /**
     * Scope para servicios que usaron excepción
     */
    public function scopeWithException($query)
    {
        return $query->whereNotNull('exception_used_id');
    }

    /**
     * Scope para servicios por tipo
     */
    public function scopeByServiceType($query, string $serviceType)
    {
        return $query->where('polymorphic_service_type', $serviceType);
    }

    /**
     * Scope para servicios por tamaño de vehículo
     */
    public function scopeByVehicleSize($query, string $vehicleSize)
    {
        return $query->where('vehicle_size_applied', $vehicleSize);
    }

    /**
     * Scope para servicios con margen bajo
     */
    public function scopeLowMargin($query)
    {
        return $query->whereHas('pricingVersion', function ($q) {
            $q->whereRaw('applied_services.margin_achieved < pricing_versions.min_margin_percent');
        });
    }

    /**
     * Obtener estadísticas generales
     */
    public static function getGeneralStats(array $filters = []): array
    {
        $query = self::query();
        
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }
        
        if (isset($filters['service_type'])) {
            $query->byServiceType($filters['service_type']);
        }

        return [
            'total_services' => $query->count(),
            'total_revenue' => $query->sum('final_price'),
            'total_cost' => $query->sum('final_cost'),
            'average_margin' => $query->avg('margin_achieved'),
            'total_discounts' => $query->sum('discount_amount'),
            'services_with_exception' => $query->withException()->count(),
            'services_requiring_approval' => $query->whereNotNull('approved_by')->count(),
        ];
    }

    /**
     * Obtener tipos de servicios disponibles
     */
    public static function getAvailableServiceTypes(): array
    {
        return [
            self::SERVICE_TYPE_CORROSION_PROTECTION => 'Protección Anticorrosiva',
            self::SERVICE_TYPE_GENERAL_PAINT => 'Pintura General',
            self::SERVICE_TYPE_CERAMIC_TREATMENT => 'Tratamiento Cerámico',
            self::SERVICE_TYPE_POLARIZE => 'Polarizado',
            self::SERVICE_TYPE_PPF => 'PPF',
            self::SERVICE_TYPE_MECHANICAL_WORKSHOP => 'Taller Mecánico',
            self::SERVICE_TYPE_SPARE_PARTS => 'Repuestos',
        ];
    }
}
