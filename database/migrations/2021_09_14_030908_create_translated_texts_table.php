<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslatedTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translated_texts', function (Blueprint $table) {
            $table->id();
            $table->string('cafe24_mall_id');
            $table->string('shop_no');
            $table->string('page');
            $table->string('selector');
            $table->string('language');
            $table->string('input_text');
            $table->string('is_placeholder');
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
        Schema::dropIfExists('translated_texts');
    }
}
