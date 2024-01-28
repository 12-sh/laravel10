<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('social_id')->comment('ソーシャルID');
            $table->unsignedBigInteger('user_id')->comment('ユーザーID');
            $table->string('social_user_id')->unique()->comment('ソーシャルユーザーID');
            $table->timestamps();

            $table->foreign('social_id')->references('id')->on('socials');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_user');
    }
};
