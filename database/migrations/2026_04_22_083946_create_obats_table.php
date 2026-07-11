<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obats', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kategori', 50);
            $table->text('deskripsi')->nullable();
            $table->string('indikasi', 100)->nullable();
            $table->string('durasi', 30)->nullable();
            $table->integer('harga');
            $table->integer('stok')->default(0);
            $table->string('gambar', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obats');
    }
};
