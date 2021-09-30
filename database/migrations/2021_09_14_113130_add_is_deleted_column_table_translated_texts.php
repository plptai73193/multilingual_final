<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDeletedColumnTableTranslatedTexts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('translated_texts', function (Blueprint $table) {
            $table->string('is_deleted')->after('is_placeholder')->default('0');
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
            $table->dropColumn('is_deleted');
        });
    }
}
