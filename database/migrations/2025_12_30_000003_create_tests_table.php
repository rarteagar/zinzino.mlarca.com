<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();

            // who registered the test (logged-in user)
            $table->foreignId('entered_by_id')->constrained('users')->onDelete('cascade');

            // subject can be a registered user
            $table->foreignId('subject_user_id')->nullable()->constrained('users')->nullOnDelete();

            // or a client managed by the user
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();

            $table->boolean('is_my_test')->default(true);
            $table->date('sample_date')->nullable();
            $table->string('type')->nullable();
            $table->json('data')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
