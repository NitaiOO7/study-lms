<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_series_id')->constrained()->onDelete('cascade');
            $table->integer('total_questions')->default(0);
            $table->integer('attempted')->default(0);
            $table->integer('correct')->default(0);
            $table->integer('wrong')->default(0);
            $table->integer('skipped')->default(0);
            $table->decimal('score', 10, 2)->default(0);
            $table->decimal('total_marks', 10, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->integer('time_taken_seconds')->default(0);
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'section_id', 'test_series_id']);
        });

        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('selected_option_id')->nullable()->constrained('options')->onDelete('set null');
            $table->text('text_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->decimal('marks_obtained', 5, 2)->default(0);
            $table->integer('time_spent_seconds')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_answers');
        Schema::dropIfExists('test_attempts');
    }
};
