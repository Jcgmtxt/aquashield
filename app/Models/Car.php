<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'plate_number',
        'brand',
        'model',
        'year',
        'color',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el cliente propietario
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relación con los check-ins
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    /**
     * Relación con servicios aplicados
     */
    public function appliedServices(): HasMany
    {
        return $this->hasMany(AppliedService::class);
    }

    /**
     * Determinar el tamaño del vehículo basado en marca y modelo
     */
    public function getVehicleSize(): string
    {
        // TODO: hacer una lista de los vehiculos mas usados y su tamaño, entonces se puede usar el fullname para determinar el tamaño
        // Lógica básica para determinar tamaño - se puede expandir
        $smallBrands = ['chevrolet spark', 'hyundai i10', 'kia picanto'];
        $largeBrands = ['ford explorer', 'chevrolet tahoe', 'toyota prado'];
        
        $fullName = strtolower($this->brand . ' ' . $this->model);
        
        foreach ($smallBrands as $small) {
            if (str_contains($fullName, $small)) {
                return 'pequeño';
            }
        }
        
        foreach ($largeBrands as $large) {
            if (str_contains($fullName, $large)) {
                return 'extra_grande';
            }
        }
        
        // Por defecto mediano
        return 'mediano';
    }

    /**
     * Scope para buscar por placa
     */
    public function scopeByPlateNumber($query, $plateNumber)
    {
        return $query->where('plate_number', 'LIKE', '%' . $plateNumber . '%');
    }

    /**
     * Scope para buscar por marca
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', 'LIKE', '%' . $brand . '%');
    }

    /**
     * Obtener nombre completo del vehículo
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model} {$this->year}";
    }
}
