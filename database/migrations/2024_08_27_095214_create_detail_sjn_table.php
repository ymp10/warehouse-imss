<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailSjnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('detail_sjn')) {
            Schema::create('detail_sjn', function (Blueprint $table) {
                $table->id();
                $table->integer('id_sjn');
                $table->string('kode_material')->nullable();
                $table->string('barang');
                $table->string('spek')->nullable();
                $table->string('qty');
                $table->string('satuan');
                $table->string('keterangan')->nullable();
                $table->string('user_id')->nullable();
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
        Schema::dropIfExists('detail_sjn');
    }
}
