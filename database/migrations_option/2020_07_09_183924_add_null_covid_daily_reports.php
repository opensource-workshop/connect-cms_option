<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullCovidDailyReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('covid_daily_reports', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE `covid_daily_reports` MODIFY COLUMN confirmed int(11) DEFAULT NULL COMMENT '感染者数'");
            DB::statement("ALTER TABLE `covid_daily_reports` MODIFY COLUMN deaths int(11) DEFAULT NULL COMMENT '死亡者数'");
            DB::statement("ALTER TABLE `covid_daily_reports` MODIFY COLUMN recovered int(11) DEFAULT NULL COMMENT '回復者数'");
            DB::statement("ALTER TABLE `covid_daily_reports` MODIFY COLUMN active int(11) DEFAULT NULL COMMENT '感染中'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('covid_daily_reports', function (Blueprint $table) {
            //
        });
    }
}
