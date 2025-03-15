<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailLoiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_loi', function (Blueprint $table) {
            $table->id();
            $table->integer('id_del_loi');
            $table->integer('loi_id');
            $table->integer('id_pr');
            $table->integer('id_detail_pr');
            $table->integer('loi_qty');
            $table->string('harga');
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
        Schema::dropIfExists('detail_loi');
    }
}
