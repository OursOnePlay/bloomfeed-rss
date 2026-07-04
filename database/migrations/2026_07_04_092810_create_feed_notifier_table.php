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
        // Table name follows Laravel's belongsToMany convention: alphabetical
        // order of the two model names ("feed" < "notifier").
        Schema::create('feed_notifier', function (Blueprint $table) {
            $table->foreignId('notifier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feed_id')->constrained()->cascadeOnDelete();
            $table->primary(['notifier_id', 'feed_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feed_notifier');
    }
};
