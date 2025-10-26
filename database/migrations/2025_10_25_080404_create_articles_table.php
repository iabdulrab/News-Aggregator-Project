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
            $table->foreignId('source_id')->constrained('sources')->cascadeOnDelete();
            $table->string('source_article_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('url')->unique(); // dedupe by URL
            $table->string('url_to_image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('author_name')->nullable();
            $table->json('raw')->nullable(); // raw provider payload
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['published_at']);
            $table->index(['category']);
            $table->index(['source_id']);
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
