<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->unsignedTinyInteger('subject_age')->nullable();
            $table->unsignedSmallInteger('subject_height_cm')->nullable();
            $table->decimal('subject_weight_kg', 5, 2)->nullable();
            $table->text('health_challenges')->nullable();
            $table->text('pdf_text')->nullable();
            // estado del test: Registrado, En proceso, Completado, Cancelado
            $table->enum('status', ['Registrado', 'En proceso', 'Completado', 'Cancelado'])->default('Registrado');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
