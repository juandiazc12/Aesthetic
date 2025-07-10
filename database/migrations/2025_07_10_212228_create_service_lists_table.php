<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('service_list_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_list_id');
            $table->unsignedBigInteger('professional_id');
            $table->foreign('service_list_id')->references('id')->on('service_lists')->onDelete('cascade');
            $table->foreign('professional_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_list_user');
        Schema::dropIfExists('service_lists');
    }
};