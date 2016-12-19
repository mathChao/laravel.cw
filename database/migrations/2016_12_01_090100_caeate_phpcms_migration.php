<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CaeatePhpcmsMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phpcms_migration', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 64);
            $table->string('name', 64);
            $table->string('phpcms_table', 64);
            $table->integer('phpcms_id');
            $table->string('ecms_table', 64);
            $table->integer('ecms_id');
            $table->text('data')->default('');
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
        Schema::dropIfExists('phpcms_migration');
    }
}
