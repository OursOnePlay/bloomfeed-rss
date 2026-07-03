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
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('ai_summary');
            $table->longText('full_content_markdown')->nullable();
            $table->timestamp('full_content_fetched_at')->nullable();
            $table->string('full_content_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('ai_summary')->nullable();
            $table->dropColumn([
                'full_content_markdown',
                'full_content_fetched_at',
                'full_content_error',
            ]);
        });
    }
};
