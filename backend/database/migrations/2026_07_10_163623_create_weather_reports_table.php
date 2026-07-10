<?php

use App\Enums\RiskLevel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weather_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->decimal('temperature', 4, 2);
            $table->decimal('feels_like', 4, 2);
            $table->unsignedTinyInteger('humidity');
            $table->decimal('wind_speed', 5, 2);
            $table->unsignedTinyInteger('rain_probability')->default(0);
            $table->decimal('uv_index', 3, 1)->default(0.0);
            $table->unsignedTinyInteger('risk_score')->default(0);
            $table->string('risk_level')->default(RiskLevel::LOW->value);
            $table->json('recommendations')->nullable(); // Guardará os insights gerados pelo motor
            $table->timestamp('cached_until'); // TTL para cache no Redis/DB
            $table->timestamps();

            // Índice para controlo rápido de expiração de snapshots expirados
            $table->index('cached_until', 'idx_reports_cached_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_reports');
    }
};