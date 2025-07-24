<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained()->onDelete('cascade');

            // Cliente que califica
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');

            // Profesional calificado (usuario con rol "profesional")
            $table->foreignId('professional_id')->constrained('users')->onDelete('cascade');

            $table->unsignedTinyInteger('rating'); // 1 a 5 estrellas
            $table->text('comment')->nullable();   // Comentario opcional

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
