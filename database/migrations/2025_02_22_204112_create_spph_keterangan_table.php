<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('spph_keterangan')) {
            Schema::create('spph_keterangan', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('spph_id');
                $table->text('keterangan');
                $table->timestamps();

                $table->foreign('spph_id')->references('id')->on('spph')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('spph_keterangan');
    }
};
