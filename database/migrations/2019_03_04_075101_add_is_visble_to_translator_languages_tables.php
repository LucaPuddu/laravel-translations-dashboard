<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsVisbleToTranslatorLanguagesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('translator_languages', function(Blueprint $table){
            $table->addColumn('boolean', 'visible')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('translator_languages', function(Blueprint $table){
            $table->dropColumn('visible');
        });
    }
}
