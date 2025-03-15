<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pr');
            $table->string('nomor_loi');
            $table->integer('lampiran');
            $table->integer('vendor_id');
            $table->date('tanggal_loi');
            $table->string('batas_loi');
            $table->string('perihal');
            $table->string('penerima');
            $table->string('alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loi');
    }
}
