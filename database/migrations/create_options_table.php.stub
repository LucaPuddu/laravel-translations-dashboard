<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use LPuddu\LaravelTranslationsDashboard\Models\Option;
use Spatie\Permission\Models\Permission;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('translator_options')) {
            Schema::create('translator_options', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 64);
                $table->string('value', 256)->nullable();
            });

            Option::create([
                'name' => 'rich_editor',
                'value' => 0
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translator_options');
    }
}
