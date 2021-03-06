<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('telegram_id')->unique()->nullable()->default(null);
            $table->foreignId('question_id')->nullable();
            $table->foreignId('hint_id')->nullable();
            $table->foreignId('answer_id')->nullable();
            $table->longText('text')->nullable();
            $table->boolean('waits_decision')->default(false);
            $table->boolean('passed')->default(false);
            $table->boolean('admin')->default(false);

            $table->string('email')->nullable();
            $table->string('password');
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->rememberToken();

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
        Schema::dropIfExists('users');
    }
}
