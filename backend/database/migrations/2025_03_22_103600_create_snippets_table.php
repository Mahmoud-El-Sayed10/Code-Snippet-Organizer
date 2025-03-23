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
        Schema::create('snippets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('code_content');
            $table->foreignId('language_id')->constrained();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('language_id');
        });

        Schema::create('favorites', function (Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('snippet_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'snippet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('snippets');
    }
};