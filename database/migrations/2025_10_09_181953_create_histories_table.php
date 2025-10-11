<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();

            // Reference to users
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('detail'); // e.g. "ACM Request" or "Download"

            // You can rely on created_at for date/time
            $table->timestamps(); // created_at (datetime) and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
