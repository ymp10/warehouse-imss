<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailJustiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_justi', function (Blueprint $table) {
            $table->id();
            $table->integer('id_justi');
            $table->integer('id_detail_pr');
            $table->string('nama_vendor');
            $table->string('nomor_vendor');
            $table->string('harga_satuan');
            $table->string('keterangan');
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
        Schema::dropIfExists('detail_justi');
    }
}
