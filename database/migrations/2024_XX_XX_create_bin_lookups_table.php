<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bin_lookups', function (Blueprint $table) {
            $table->id();
            $table->string('bin', 6);
            $table->string('bank_name')->nullable();
            $table->string('bank_url')->nullable();
            $table->string('brand')->nullable();
            $table->string('type')->nullable();
            $table->string('country_name')->nullable();
            $table->string('country_emoji')->nullable();
            $table->boolean('is_valid')->default(true);
            $table->timestamps();

            $table->index('bin');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bin_lookups');
    }
}; 