<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedTinyInteger('subject_age')->nullable()->after('type');
            $table->unsignedSmallInteger('subject_height_cm')->nullable()->after('subject_age');
            $table->decimal('subject_weight_kg', 5, 2)->nullable()->after('subject_height_cm');
            $table->text('health_challenges')->nullable()->after('subject_weight_kg');
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn(['subject_age', 'subject_height_cm', 'subject_weight_kg', 'health_challenges']);
        });
    }
};
