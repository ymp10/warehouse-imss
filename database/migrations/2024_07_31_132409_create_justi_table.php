<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJustiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('justi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_justi');
            $table->date('justi');
            $table->string('dasar');
            $table->string('perihal');
            $table->string('id_pr');
            $table->string('nomor_pr');
            $table->date('pr');
            $table->string('nomor_spph');
            $table->date('spph');
            $table->integer('lampiran');
            $table->integer('vendor_id');
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
        Schema::dropIfExists('justi');
    }
}
