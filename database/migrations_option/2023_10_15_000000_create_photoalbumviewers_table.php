<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * フォトアルバムビューア・テーブル
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category フォトアルバムビューア・プラグイン
 * @package Controller
 */
class CreatePhotoalbumviewersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photoalbumviewers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('bucket_id');
            $table->string('bucket_name');
            $table->integer('photoalbum_id');
            $table->integer('col_count')->default(0);
            $table->integer('row_count')->default(0);
            $table->integer('link_frame_id')->nullable()->default(0);
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
        Schema::dropIfExists('photoalbumviewers');
    }
}
