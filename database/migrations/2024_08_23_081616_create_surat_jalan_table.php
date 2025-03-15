<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratJalanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('surat_jalan')) {
            Schema::create('surat_jalan', function (Blueprint $table) {
                $table->id();
                $table->integer('id_sjn');
                $table->string('no_sjn');
                $table->date('tgl_sjn');
                $table->string('kepada');
                $table->string('lokasi');
                $table->string('pengirim');
                $table->string('id_user');
                $table->string('is_read');
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
        Schema::dropIfExists('surat_jalan');
    }
}
