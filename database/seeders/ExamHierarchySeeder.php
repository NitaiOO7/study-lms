<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamHierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. GATE Category
        $gateCategory = \App\Models\ExamCategory::firstOrCreate(
            ['slug' => 'gate'],
            ['name' => 'GATE', 'icon' => '🎓']
        );

        $gateExam = \App\Models\Exam::firstOrCreate(
            ['slug' => 'gate-2026'],
            [
                'category_id' => $gateCategory->id,
                'name' => 'GATE 2026',
                'description' => 'Graduate Aptitude Test in Engineering'
            ]
        );

        $csBranch = \App\Models\Branch::firstOrCreate(
            ['slug' => 'computer-science'],
            ['exam_id' => $gateExam->id, 'name' => 'Computer Science']
        );

        $topics = [
            'Data Structures', 'Algorithms', 'Operating Systems', 
            'Database Management Systems', 'Computer Networks', 
            'Theory of Computation', 'Compiler Design', 'Digital Logic'
        ];

        foreach ($topics as $topic) {
            \App\Models\Topic::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($topic)],
                ['branch_id' => $csBranch->id, 'name' => $topic]
            );
        }

        // 2. SSC Category
        $sscCategory = \App\Models\ExamCategory::firstOrCreate(
            ['slug' => 'ssc'],
            ['name' => 'SSC', 'icon' => '🏛️']
        );

        $sscExam = \App\Models\Exam::firstOrCreate(
            ['slug' => 'ssc-cgl-2026'],
            [
                'category_id' => $sscCategory->id,
                'name' => 'SSC CGL 2026',
                'description' => 'Staff Selection Commission - Combined Graduate Level'
            ]
        );
    }
}
