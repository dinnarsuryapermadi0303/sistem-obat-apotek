<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori')->nullable();
            $table->string('jenis')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('indikasi')->nullable();
            $table->text('komposisi')->nullable();
            $table->text('dosis')->nullable();
            $table->text('efek_samping')->nullable();
            $table->text('kontraindikasi')->nullable();
            $table->string('harga')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
