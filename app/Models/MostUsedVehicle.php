<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MostUsedVehicle extends Model
{
    use HasFactory;

    protected $table = 'most_used_vehicles';

    protected $fillable = [
        'version_id',
        'brand',
        'model',
        'year_range',
        'vehicle_size',
        'special_cost',
        'special_price',
        'is_active',
    ];

    protected $casts = [
        'special_cost' => 'integer',
        'special_price' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con la versión de precios
     */
    public function pricingVersion(): BelongsTo
    {
        return $this->belongsTo(PricingVersion::class, 'version_id');
    }

    /**
     * Relación con servicios aplicados que usaron esta excepción
     */
    public function appliedServices(): HasMany
    {
        return $this->hasMany(AppliedService::class, 'exception_used_id');
    }

    /**
     * Calcular el margen de ganancia
     */
    public function getMarginAttribute(): float
    {
        if ($this->special_price <= 0) {
            return 0;
        }
        
        return round((($this->special_price - $this->special_cost) / $this->special_price) * 100, 2);
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
     * Verificar si un año específico está en el rango
     */
    public function isYearInRange(string $year): bool
    {
        if (empty($this->year_range)) {
            return true; // Sin restricción de año
        }

        $parts = explode('-', $this->year_range);
        
        if (count($parts) !== 2) {
            return false;
        }

        $startYear = (int) trim($parts[0]);
        $endYear = (int) trim($parts[1]);
        $checkYear = (int) $year;

        return $checkYear >= $startYear && $checkYear <= $endYear;
    }

    /**
     * Verificar si coincide con un vehículo específico
     */
    public function matchesVehicle(string $brand, string $model, string $year = null): bool
    {
        $brandMatch = stripos($brand, $this->brand) !== false || stripos($this->brand, $brand) !== false;
        $modelMatch = stripos($model, $this->model) !== false || stripos($this->model, $model) !== false;
        $yearMatch = $year ? $this->isYearInRange($year) : true;

        return $brandMatch && $modelMatch && $yearMatch && $this->is_active;
    }

    /**
     * Scope para vehículos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para una versión específica
     */
    public function scopeForVersion($query, int $versionId)
    {
        return $query->where('version_id', $versionId);
    }

    /**
     * Scope para buscar por marca
     */
    public function scopeByBrand($query, string $brand)
    {
        return $query->where('brand', 'LIKE', '%' . $brand . '%');
    }

    /**
     * Scope para buscar por modelo
     */
    public function scopeByModel($query, string $model)
    {
        return $query->where('model', 'LIKE', '%' . $model . '%');
    }

    /**
     * Scope para buscar vehículos que coincidan
     */
    public function scopeMatchingVehicle($query, string $brand, string $model, string $year = null)
    {
        return $query->active()
            ->where(function ($q) use ($brand) {
                $q->where('brand', 'LIKE', '%' . $brand . '%');
            })
            ->where(function ($q) use ($model) {
                $q->where('model', 'LIKE', '%' . $model . '%');
            })
            ->when($year, function ($q) use ($year) {
                $q->where(function ($subQ) use ($year) {
                    $subQ->whereNull('year_range')
                        ->orWhereRaw("? BETWEEN SUBSTRING_INDEX(year_range, '-', 1) AND SUBSTRING_INDEX(year_range, '-', -1)", [$year]);
                });
            });
    }

    /**
     * Obtener el nombre completo del vehículo
     */
    public function getFullNameAttribute(): string
    {
        $name = "{$this->brand} {$this->model}";
        
        if ($this->year_range) {
            $name .= " ({$this->year_range})";
        }
        
        return $name;
    }

    /**
     * Obtener estadísticas de uso
     */
    public function getUsageStats(): array
    {
        $appliedCount = $this->appliedServices()->count();
        $totalRevenue = $this->appliedServices()->sum('final_price');
        $avgMargin = $this->appliedServices()->avg('margin_achieved');

        return [
            'times_used' => $appliedCount,
            'total_revenue' => $totalRevenue,
            'average_margin' => round($avgMargin, 2),
        ];
    }
}
