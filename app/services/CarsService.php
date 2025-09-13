<?php

namespace App\Services;

use App\Models\Car;
use Inertia\Response;

class CarsService
{
    public function __construct(private Car $car){}

    public function create() : Response
    {
        return Inertia::render('cars/create');
    }
}
