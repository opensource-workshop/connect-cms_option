<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCovid19japanPatients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid19japan_patients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('patientId', 255);
            $table->boolean('confirmedPatient')->nullable();
            $table->date('dateAnnounced')->nullable();
            $table->integer('ageBracket')->nullable();
            $table->string('gender', 255)->nullable();
            $table->string('residence', 255)->nullable();
            $table->string('detectedCityTown', 255)->nullable();
            $table->string('detectedPrefecture', 255)->nullable();
            $table->string('patientStatus', 255)->nullable();
            $table->string('mhlwPatientNumber', 255)->nullable();
            $table->string('prefecturePatientNumber', 255)->nullable();
            $table->text('prefectureSourceURL')->nullable();
            $table->text('sourceURL')->nullable();
            $table->text('notes')->nullable();
            $table->text('knownCluster')->nullable();
            $table->integer('created_id')->nullable();
            $table->string('created_name', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->integer('updated_id')->nullable();
            $table->string('updated_name', 255)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('deleted_id')->nullable();
            $table->string('deleted_name', 255)->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('covid19japan_patients');
    }
}
