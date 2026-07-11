<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change user_status from enum to VARCHAR to allow descriptive statuses
        DB::statement("ALTER TABLE recommendation_validations MODIFY COLUMN user_status VARCHAR(100) DEFAULT 'pending'");
    }

    public function down(): void
    {
        // revert to enum with original allowed values
        DB::statement("ALTER TABLE recommendation_validations MODIFY COLUMN user_status ENUM('pending','approved','rejected') DEFAULT 'pending'");
    }
};
