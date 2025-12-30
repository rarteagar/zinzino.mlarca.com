<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // owner (who manages this client)
            $table->boolean('is_self')->default(false);
            $table->string('name');
            $table->string('identifier')->nullable(); // e.g., client code
            $table->string('email')->nullable();
            $table->date('birthdate')->nullable();
            $table->integer('height_cm')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
