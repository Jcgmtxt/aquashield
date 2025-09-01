<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorrosionPricingBySize extends Model
{
    use HasFactory;

    protected $table = 'corrosion_pricing_by_size';

    protected $fillable = [
        'version_id',
        'vehicle_size',
        'base_cost',
        'suggested_price',
        'size_description',
    ];

    protected $casts = [
        'base_cost' => 'integer',
        'suggested_price' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Tamaños de vehículo disponibles
     */
    const SIZE_PEQUENO = 'pequeño';
    const SIZE_MEDIANO = 'mediano';
    const SIZE_GRANDE = 'grande';
    const SIZE_EXTRA_GRANDE = 'extra_grande';

    /**
     * Relación con la versión de precios
     */
    public function pricingVersion(): BelongsTo
    {
        return $this->belongsTo(PricingVersion::class, 'version_id');
    }

    /**
     * Calcular el margen de ganancia
     */
    public function getMarginAttribute(): float
    {
        if ($this->suggested_price <= 0) {
            return 0;
        }
        
        return round((($this->suggested_price - $this->base_cost) / $this->suggested_price) * 100, 2);
    }

    /**
     * Verificar si cumple con el margen mínimo
     */
    public function meetsMinimumMargin(): bool
    {
        $minMargin = $this->pricingVersion->min_margin_percent;
        return $this->margin >= $minMargin;
    }

    /**
     * Scope para un tamaño específico
     */
    public function scopeForSize($query, string $size)
    {
        return $query->where('vehicle_size', $size);
    }

    /**
     * Scope para una versión específica
     */
    public function scopeForVersion($query, int $versionId)
    {
        return $query->where('version_id', $versionId);
    }

    /**
     * Obtener todos los tamaños disponibles
     */
    public static function getAvailableSizes(): array
    {
        return [
            self::SIZE_PEQUENO => 'Pequeño',
            self::SIZE_MEDIANO => 'Mediano',
            self::SIZE_GRANDE => 'Grande',
            self::SIZE_EXTRA_GRANDE => 'Extra Grande',
        ];
    }

    /**
     * Obtener descripción del tamaño
     */
    public function getSizeDescriptionAttribute(): string
    {
        $sizes = self::getAvailableSizes();
        return $sizes[$this->vehicle_size] ?? $this->vehicle_size;
    }

    /**
     * Validar que el precio sugerido no sea menor al costo
     */
    public function validatePricing(): bool
    {
        return $this->suggested_price >= $this->base_cost;
    }
}
