<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropShopNoColumnTranslatedTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('translated_texts', function (Blueprint $table) {
            $table->dropColumn('shop_no');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('translated_texts', function (Blueprint $table) {
            $table->string('shop_no')->after('uuid');
        });
    }
}
