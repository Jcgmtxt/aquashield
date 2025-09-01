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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identity_number')->unique()->index();
            $table->enum('identity_type', ['CC', 'CE', 'NIT', 'Passport']);
            $table->string('phone_number')->unique();
            $table->string('email')->nullable();
            $table->timestamps();
            $table->index('identity_number');
        });

        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->string('plate_number')->unique()->index()->max(6);
            $table->string('brand');  // Marca del automovil
            $table->string('model');  // Linea de automovil
            $table->string('year'); 
            $table->string('color');
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('check-ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cars');
            $table->foreignId('user_id')->constrained('users')->alias('responsable');
            $table->date('check_in_date');
            $table->time('check_in_time');
            $table->enum('status', // TODO: Voy a dejar esto asi de momento, pero se puede cambiar
            ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('check_out_date')->nullable();
            $table->time('check_out_time')->nullable();
            $table->integer('mileage');
            $table->string('fuel_level');
            $table->string('comments')->nullable()->max;
            $table->string('video_url');
            $table->timestamps();
        });

        Schema::create('service_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_in_id')->constrained('check-ins');
            $table->foreignId('service_id')->constrained('services');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_check_ins');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('cars');
        Schema::dropIfExists('services');
        Schema::dropIfExists('check-ins');
    }
};
