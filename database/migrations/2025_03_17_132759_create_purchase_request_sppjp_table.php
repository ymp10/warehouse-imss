<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestSppjpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_sppjp', function (Blueprint $table) {
            $table->id();
            $table->integer('proyek_id');
            $table->string('no_pr_sppjp');
            $table->string('dasar_pr_sppjp');
            $table->date('tgl_pr_sppjp');
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
        Schema::dropIfExists('purchase_request_sppjp');
    }
}
