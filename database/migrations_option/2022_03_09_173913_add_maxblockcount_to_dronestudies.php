<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxblockcountToDronestudies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dronestudies', function (Blueprint $table) {
            //
            $table->integer('max_block_count')->default(0)->comment('最大ブロック実行数')->after('use_stream');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dronestudies', function (Blueprint $table) {
            //
            $table->dropColumn('max_block_count');
        });
    }
}
