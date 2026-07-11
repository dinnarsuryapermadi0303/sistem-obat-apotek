<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommendation_validations', function (Blueprint $table) {
            if (!Schema::hasColumn('recommendation_validations', 'approved_meds')) {
                $table->json('approved_meds')->nullable()->after('obat');
            }

            if (!Schema::hasColumn('recommendation_validations', 'confidence')) {
                $table->string('confidence')->nullable()->after('approved_meds');
            }

            if (!Schema::hasColumn('recommendation_validations', 'user_status')) {
                $table->enum('user_status', ['pending', 'approved', 'rejected'])
                    ->default('pending')
                    ->after('status');
            }

            if (!Schema::hasColumn('recommendation_validations', 'admin_status')) {
                $table->string('admin_status')->default('Menunggu Validasi')->after('user_status');
            }

            if (!Schema::hasColumn('recommendation_validations', 'admin_conditions')) {
                $table->text('admin_conditions')->nullable()->after('catatan_admin');
            }

            if (!Schema::hasColumn('recommendation_validations', 'pdf_ready')) {
                $table->boolean('pdf_ready')->default(false)->after('admin_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recommendation_validations', function (Blueprint $table) {
            if (Schema::hasColumn('recommendation_validations', 'approved_meds')) {
                $table->dropColumn('approved_meds');
            }

            if (Schema::hasColumn('recommendation_validations', 'confidence')) {
                $table->dropColumn('confidence');
            }

            if (Schema::hasColumn('recommendation_validations', 'user_status')) {
                $table->dropColumn('user_status');
            }

            if (Schema::hasColumn('recommendation_validations', 'admin_status')) {
                $table->dropColumn('admin_status');
            }

            if (Schema::hasColumn('recommendation_validations', 'admin_conditions')) {
                $table->dropColumn('admin_conditions');
            }

            if (Schema::hasColumn('recommendation_validations', 'pdf_ready')) {
                $table->dropColumn('pdf_ready');
            }
        });
    }
};
