<?php

use App\Enums\EventType;
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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            // Chave estrangeira ligada ao utilizador com remoção em cascata
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('city', 100);
            $table->string('state', 50);
            $table->string('country', 3); // ISO 3166-1 alpha-3 (ex: BRA)
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            // Default definido a partir do Enum nativo do PHP
            $table->string('event_type')->default(EventType::OUTDOOR->value);
            $table->timestamp('start_at');
            $table->timestamps();

            // Índices de Performance cruciais para relatórios cronológicos e geográficos
            $table->index('start_at', 'idx_events_start_at');
            $table->index(['city', 'country'], 'idx_events_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};