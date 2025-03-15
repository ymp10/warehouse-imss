<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKontrakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('kontrak')) {
            Schema::create('kontrak', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->integer('nomor_kontrak');
                $table->integer('nama_pekerjaan');
                $table->date('nilai_pekerjaan');
                $table->string('nama_pelanggan');
                $table->string('status');
                $table->string('nilai');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kontrak');
    }
}
