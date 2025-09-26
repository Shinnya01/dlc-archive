<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->year('year'); // or $table->integer('year') if you prefer
            $table->string('file')->nullable(); // path or filename for uploaded file
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_projects');
    }
};