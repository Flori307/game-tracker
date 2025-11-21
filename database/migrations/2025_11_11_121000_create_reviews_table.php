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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('game_id');
            $table->string('game_name');
            $table->string('title');
            $table->text('content');
            $table->integer('gameplay_rating');
            $table->integer('graphics_rating');
            $table->integer('story_rating');
            $table->integer('music_rating');
            $table->integer('atmosphere_rating');
            $table->float('overall_rating', 3, 1);
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
        Schema::dropIfExists('reviews');
    }
};
