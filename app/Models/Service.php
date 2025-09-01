<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con check-ins a través de la tabla pivote
     */
    public function checkIns(): BelongsToMany
    {
        return $this->belongsToMany(CheckIn::class, 'service_check_ins');
    }

    /**
     * Relación con versiones de precios
     */
    public function pricingVersions(): HasMany
    {
        return $this->hasMany(PricingVersion::class);
    }

    /**
     * Relación con servicios anticorrosivos
     */
    public function corrosionProtection(): HasMany
    {
        return $this->hasMany(ServiceCorrosionProteccion::class);
    }

    /**
     * Obtener la versión de precios activa
     */
    public function getActivePricingVersion()
    {
        return $this->pricingVersions()
            ->where('effective_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Scope para servicios activos
     */
    public function scopeActive($query)
    {
        return $query->whereHas('pricingVersions', function ($q) {
            $q->where('effective_date', '<=', now())
                ->where(function ($subQuery) {
                    $subQuery->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                });
        });
    }
}
