<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            // Jeden rachunek na użytkownika — usunięcie użytkownika kasuje rachunek.
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('number', 34)->unique();
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 3)->default('PLN');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
