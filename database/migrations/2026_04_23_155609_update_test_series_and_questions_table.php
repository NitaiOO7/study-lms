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
        Schema::table('test_series', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('topic_id')->nullable()->constrained('topics')->onDelete('set null');
            // Change enum is tricky in MySQL, better use change() or drop/add
            $table->string('type')->default('mcq')->change(); // Use string for more flexibility (mcq, msq, nat)
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->dropColumn('topic_id');
        });

        Schema::table('test_series', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
