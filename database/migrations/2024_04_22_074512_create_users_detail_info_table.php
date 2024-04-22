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
        Schema::create('users_detail_info', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('users_roles')->onDelete('cascade');

            $table->string('name')->nullable();
            $table->string('profession')->nullable();
            $table->text('about_text')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_telegram')->nullable();
            $table->string('avatar')->nullable();
            $table->bigInteger('jobs_count')->default(0);
            $table->json('skills')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_detail_info');
    }
};
