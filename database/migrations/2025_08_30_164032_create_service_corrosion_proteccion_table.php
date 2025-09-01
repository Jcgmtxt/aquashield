<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabla principal de servicios anticorrosivos
        Schema::create('service_corrosion_proteccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services');
            $table->timestamps();
        });

        // Versiones de precios (cada 4 meses)
        Schema::create('pricing_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services');
            $table->string('name'); // ej: "2025-Enero-Abril"
            $table->date('effective_date');
            $table->date('end_date')->nullable();
            $table->integer('cost'); // costos en pesos colombianos
            $table->integer('negotiation_margin')->nullable(); // costos negociados en pesos colombianos
            $table->decimal('min_margin_percent', 5, 2)->default(20.00); // Margen mínimo global
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['effective_date', 'service_id']);
        });

        // Precios por tamaño de vehículo
        Schema::create('corrosion_pricing_by_size', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained('pricing_versions')->onDelete('cascade');
            $table->enum('vehicle_size', ['pequeño', 'mediano', 'grande', 'extra_grande']);
            $table->integer('base_cost'); // Costo base en pesos colombianos (sin decimales)
            $table->integer('suggested_price'); // Precio sugerido en pesos colombianos
            $table->text('size_description')->nullable(); // Descripción del tamaño
            $table->timestamps();
            $table->unique(['version_id', 'vehicle_size']);
            $table->index('vehicle_size');
        });

        // Excepciones para modelos específic os
        Schema::create('most_used_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained('pricing_versions')->onDelete('cascade');
            $table->string('brand');
            $table->string('model');
            $table->string('year_range')->nullable(); // ej: "2015-2023"
            $table->enum('vehicle_size', ['pequeño', 'mediano', 'grande', 'extra_grande']);
            $table->integer('special_cost'); // Costo especial en pesos colombianos
            $table->integer('special_price'); // Precio especial en pesos colombianos
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['brand', 'model']);
            $table->index(['version_id', 'is_active']);
        });

        // Servicios aplicados (conexión con check-ins y registro de precios aplicados)
        Schema::create('applied_services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('polymorphic_service_id');
            $table->enum('polymorphic_service_type', ['corrosion_proteccion']); // TODO: Agregar LA LISTA DE servicios
            // Esta es la lista de servicios que aun no estan: 
            // 'general_paint', 'ceramic_treatment', 'polarize', 'PPF', 'mechanical_workshop', 'spar_parts' 
            $table->foreignId('pricing_version_id')->constrained('pricing_versions');
            $table->foreignId('car_id')->constrained('cars'); // Referencia al vehículo
            $table->enum('vehicle_size_applied', ['pequeño', 'mediano', 'grande', 'extra_grande']);
            $table->string('vehicle_brand');
            $table->string('vehicle_model');
            $table->integer('final_cost'); // Costo real aplicado
            $table->integer('final_price'); // Precio final cobrado
            $table->decimal('margin_achieved', 5, 2); // Margen real obtenido
            $table->integer('discount_amount')->default(0); // Descuento aplicado en pesos
            $table->foreignId('exception_used_id')->nullable()->constrained('most_used_vehicles');
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users'); // Quién aprobó precio especial
            $table->timestamps();
            $table->index(['pricing_version_id', 'vehicle_size_applied']);
            $table->index(['car_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar en orden inverso respetando foreign keys
        Schema::dropIfExists('applied_services');
        Schema::dropIfExists('most_used_vehicles');
        Schema::dropIfExists('corrosion_pricing_by_size');
        Schema::dropIfExists('pricing_versions');
        Schema::dropIfExists('service_corrosion_proteccion');
    }
};
