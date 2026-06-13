<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── AI Chat Sessions ───────────────────────────────────────────────
        Schema::create('ai_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('role', ['student', 'teacher', 'admin', 'developer'])->default('student');
            $table->string('session_token', 64)->unique();
            $table->string('title', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // ── AI Chat Messages ───────────────────────────────────────────────
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('ai_chat_sessions')->onDelete('cascade');
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->longText('content');
            $table->integer('tokens_used')->default(0);
            $table->string('model', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['session_id', 'created_at']);
        });

        // ── AI Knowledge Chunks (RAG index) ────────────────────────────────
        Schema::create('ai_knowledge_chunks', function (Blueprint $table) {
            $table->id();
            $table->enum('source_type', ['code', 'docs', 'faq', 'schema', 'route']);
            $table->string('source_path', 500);
            $table->integer('chunk_index')->default(0);
            $table->longText('content');
            $table->json('keywords')->nullable();   // TF-IDF keyword list
            $table->string('checksum', 64);         // SHA-256 for change detection
            $table->timestamp('indexed_at')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'source_path']);
            $table->index('checksum');
        });

        // ── AI FAQs Knowledge Base ─────────────────────────────────────────
        Schema::create('ai_faqs', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['student', 'teacher', 'admin', 'all']);
            $table->text('question');
            $table->longText('answer');
            $table->string('category', 100)->nullable();
            $table->json('keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['role', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_faqs');
        Schema::dropIfExists('ai_knowledge_chunks');
        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_chat_sessions');
    }
};
