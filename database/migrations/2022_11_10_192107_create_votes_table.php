<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->references('id')->on('polls');
//            $table->foreignId('voter_id')->nullable()->references('id')->on('voters');
            $table->foreignId('nominee_id')->references('id')->on('nominees');
            $table->foreignId('position_id')->references('id')->on('positions');
            $table->string('student_number');
            $table->string('college');
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
        Schema::dropIfExists('votes');
    }
};
