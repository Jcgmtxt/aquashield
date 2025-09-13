<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\IdentityType;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'identity_type',
        'identity_number',
        'phone_number',
        'email',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
 
    /**
     * Relación con los vehículos del cliente
     */
    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    /**
     * Obtener todos los check-ins a través de los vehículos
     */
    public function checkIns(): HasMany
    {
        return $this->hasManyThrough(CheckIn::class, Car::class);
    }

    /**
     * Scope para buscar por número de identificación
     */
    public function scopeByIdentityNumber($query, $identityNumber)
    {
        return $query->where('identity_number', $identityNumber);
    }

    /**
     * Scope para buscar por teléfono
     */
    public function scopeByPhoneNumber($query, $phoneNumber)
    {
        return $query->where('phone_number', $phoneNumber);
    }
}
