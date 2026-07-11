<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommendation_validations', function (Blueprint $table) {
            $table->json('recommended_meds')->nullable()->after('obat');
        });
    }

    public function down(): void
    {
        Schema::table('recommendation_validations', function (Blueprint $table) {
            $table->dropColumn('recommended_meds');
        });
    }
};
