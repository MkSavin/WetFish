<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForeigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('no action');
            $table->foreign('hint_id')->references('id')->on('hints')->onDelete('no action');
            $table->foreign('answer_id')->references('id')->on('answers')->onDelete('no action');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('next_id')->references('id')->on('questions')->onDelete('no action');
            $table->foreign('congrat_id')->references('id')->on('congrats')->onDelete('no action');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('no action');
        });

        Schema::table('hints', function (Blueprint $table) {
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('no action');
        });

        Schema::table('stats', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('answer_id')->references('id')->on('answers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('question_id');
            $table->dropForeign('hint_id');
            $table->dropForeign('answer_id');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign('next_id');
            $table->dropForeign('congrat_id');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign('question_id');
        });

        Schema::table('hints', function (Blueprint $table) {
            $table->dropForeign('question_id');
        });

        Schema::table('stats', function (Blueprint $table) {
            $table->dropForeign('user_id');
            $table->dropForeign('answer_id');
        });
    }
}
