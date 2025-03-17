<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPrSppjpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_pr_sppjp', function (Blueprint $table) {
            $table->id();
            $table->integer('id_pr_sppjp');
            $table->string('kode_material')->nullable();
            $table->string('uraian');
            $table->string('spek')->nullable();
            $table->string('qty');
            $table->string('satuan');
            $table->date('waktu');
            $table->string('lampiran')->nullable();
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('detail_pr_sppjp');
    }
}
