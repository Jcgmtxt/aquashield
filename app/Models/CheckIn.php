<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CheckIn extends Model
{
    use HasFactory;

    protected $table = 'check-ins';

    protected $fillable = [
        'car_id',
        'user_id',
        'check_in_date',
        'check_in_time',
        'status',
        'check_out_date',
        'check_out_time',
        'mileage',
        'fuel_level',
        'comments',
        'video_url',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'mileage' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Estados disponibles para el check-in
     */
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Relación con el vehículo
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Relación con el usuario responsable
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con servicios a través de la tabla pivote
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_check_ins');
    }

    /**
     * Obtener el cliente a través del vehículo
     */
    public function client(): BelongsTo
    {
        return $this->car->client();
    }

    /**
     * Verificar si el check-in está completado
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Verificar si el check-in está en progreso
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Marcar como completado
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'check_out_date' => now()->toDateString(),
            'check_out_time' => now()->toTimeString(),
        ]);
    }

    /**
     * Scope para check-ins pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope para check-ins en progreso
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope para check-ins completados
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope para un rango de fechas
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('check_in_date', [$startDate, $endDate]);
    }
}
