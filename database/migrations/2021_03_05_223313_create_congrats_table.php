<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCongratsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('congrats', function (Blueprint $table) {
            $table->id();
            $table->longText('text');
            $table->string('author')->nullable();
            $table->text('video')->nullable();
            $table->text('image')->nullable();
            $table->text('audio')->nullable();
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
        Schema::dropIfExists('congrats');
    }
}
