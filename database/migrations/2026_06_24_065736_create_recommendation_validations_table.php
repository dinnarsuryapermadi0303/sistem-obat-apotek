<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_validations', function (Blueprint $table) {

            $table->id();

            $table->string('kode')->unique();

            $table->string('nama');

            $table->integer('usia')->nullable();

            $table->text('keluhan');

            $table->string('durasi')->nullable();

            $table->text('riwayat')->nullable();

            $table->string('obat');

            $table->double('similarity',8,2)->default(0);

            $table->enum('status',[
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->text('catatan_admin')->nullable();

            $table->string('pdf_path')->nullable();

            $table->string('approved_by')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_validations');
    }
};
