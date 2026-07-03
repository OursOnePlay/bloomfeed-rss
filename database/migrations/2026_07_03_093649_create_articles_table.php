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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_id')->constrained()->cascadeOnDelete();
            $table->string('guid', 512);
            $table->string('title', 512)->default('');
            $table->string('link', 2048);
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->text('ai_summary')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->timestamps();

            $table->unique(['feed_id', 'guid']);
            $table->index(['feed_id', 'published_at']);
            $table->index(['feed_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
