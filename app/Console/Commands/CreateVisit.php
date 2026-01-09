<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visit;
use function Laravel\Prompts\text;

class CreateVisit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-visit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear una nueva visita';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = text('Nombre del Usuario:', required: true);

        $email = text('Email del Usuario:', required: true, validate: fn ($v) =>
            filter_var($v, FILTER_VALIDATE_EMAIL) ? null : 'Email inválido'
        );

        $latitude = text('Latitud:', required: true, validate: fn ($v) =>
            is_numeric($v) && $v >= -90 && $v <= 90 ? null : 'La latitud debe estar entre -90 y 90'
        );

        $longitude = text('Longitud:', required: true, validate: fn ($v) =>
            is_numeric($v) && $v >= -180 && $v <= 180 ? null : 'Longitud debe estar entre -180 y 180'
        );

        $visit = Visit::create([
            'name' => $name,
            'email' => $email,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        $this->info("Visita creada! ID: {$visit->id}");
    }
}
