<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDronestudiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dronestudies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bucket_id');
            $table->string('name', 255)->comment('DroneStudy名');
            $table->integer('command_interval')->nullable()->comment('命令間隔(秒)');
            $table->integer('use_stream')->nullable()->comment('映像ブロックの使用');
            $table->string('remote_url', 255)->nullable()->comment('リモートURL');
            $table->integer('remote_id')->nullable()->comment('リモートDroneStudy-ID');
            $table->string('secret_code', 255)->nullable()->comment('秘密コード');
            $table->integer('test_mode')->nullable()->comment('テストモード');
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
        Schema::dropIfExists('dronestudies');
    }
}
