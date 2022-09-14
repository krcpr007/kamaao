<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('application_remarks')) 
        {
            Schema::create('application_remarks', function (Blueprint $table) {
                $table->id();
                $table->string('remarks');
                $table->unsignedBigInteger('application_id');
                $table->foreign('application_id')->references('id')->on('job_application')->onDelete('cascade');
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
        Schema::dropIfExists('application_remarks');
    }
}
