<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ServiceCorrosionProteccion extends Model
{
    use HasFactory;

    protected $table = 'service_corrosion_proteccion';

    protected $fillable = [
        'service_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el servicio base
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relación polimórfica con servicios aplicados
     */
    public function appliedServices(): MorphMany
    {
        return $this->morphMany(AppliedService::class, 'polymorphic_service', 'polymorphic_service_type', 'polymorphic_service_id');
    }

    /**
     * Obtener la versión de precios activa para este servicio
     */
    public function getActivePricingVersion(): ?PricingVersion
    {
        return $this->service->getActivePricingVersion();
    }

    /**
     * Calcular precio para un vehículo específico
     */
    public function calculatePriceForVehicle(Car $car): array
    {
        $pricingVersion = $this->getActivePricingVersion();
        
        if (!$pricingVersion) {
            throw new \Exception('No hay versión de precios activa para este servicio');
        }

        $vehicleSize = $car->getVehicleSize();
        
        // Verificar si hay precio especial para este vehículo
        $specialPrice = $pricingVersion->getSpecialPriceForVehicle(
            $car->brand, 
            $car->model, 
            $car->year
        );

        if ($specialPrice) {
            return [
                'cost' => $specialPrice->special_cost,
                'price' => $specialPrice->special_price,
                'margin' => round((($specialPrice->special_price - $specialPrice->special_cost) / $specialPrice->special_price) * 100, 2),
                'used_exception' => true,
                'exception_id' => $specialPrice->id,
                'vehicle_size' => $specialPrice->vehicle_size,
            ];
        }

        // Usar precio por tamaño
        $sizePrice = $pricingVersion->getPriceForVehicleSize($vehicleSize);
        
        if (!$sizePrice) {
            throw new \Exception("No hay precio configurado para vehículos de tamaño: {$vehicleSize}");
        }

        return [
            'cost' => $sizePrice->base_cost,
            'price' => $sizePrice->suggested_price,
            'margin' => round((($sizePrice->suggested_price - $sizePrice->base_cost) / $sizePrice->suggested_price) * 100, 2),
            'used_exception' => false,
            'exception_id' => null,
            'vehicle_size' => $vehicleSize,
        ];
    }

    /**
     * Aplicar servicio a un vehículo
     */
    public function applyToVehicle(Car $car, array $options = []): AppliedService
    {
        $calculation = $this->calculatePriceForVehicle($car);
        
        $finalPrice = $options['final_price'] ?? $calculation['price'];
        $discountAmount = $calculation['price'] - $finalPrice;
        
        // Verificar margen mínimo
        $pricingVersion = $this->getActivePricingVersion();
        $minMarginPercent = $pricingVersion->min_margin_percent;
        $actualMargin = (($finalPrice - $calculation['cost']) / $finalPrice) * 100;
        
        if ($actualMargin < $minMarginPercent && !isset($options['approved_by'])) {
            throw new \Exception("El margen {$actualMargin}% es menor al mínimo requerido {$minMarginPercent}%. Se requiere aprobación.");
        }

        return AppliedService::create([
            'polymorphic_service_id' => $this->id,
            'polymorphic_service_type' => 'corrosion_proteccion',
            'pricing_version_id' => $pricingVersion->id,
            'car_id' => $car->id,
            'vehicle_size_applied' => $calculation['vehicle_size'],
            'vehicle_brand' => $car->brand,
            'vehicle_model' => $car->model,
            'final_cost' => $calculation['cost'],
            'final_price' => $finalPrice,
            'margin_achieved' => $actualMargin,
            'discount_amount' => $discountAmount,
            'exception_used_id' => $calculation['exception_id'],
            'notes' => $options['notes'] ?? null,
            'approved_by' => $options['approved_by'] ?? null,
        ]);
    }
}
