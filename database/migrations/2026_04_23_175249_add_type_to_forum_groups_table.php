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
        Schema::table('forum_groups', function (Blueprint $table) {
            $table->enum('type', ['student', 'teacher', 'universal'])->default('universal');
            $table->foreignId('exam_id')->nullable()->constrained('exams')->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('forum_groups', function (Blueprint $table) {
            $table->dropColumn(['type', 'exam_id', 'branch_id']);
        });
    }
};
