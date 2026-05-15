<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForumGroup;

class ForumGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            // Universal Group
            [
                'name' => 'Global Community Hub',
                'slug' => 'global-community',
                'description' => 'A space for everyone to interact, share general updates, and network across the platform.',
                'icon' => '🌍',
                'type' => 'universal',
                'is_universal' => true,
                'is_active' => true
            ],
            // Teacher Groups
            [
                'name' => 'Teacher Lounge',
                'slug' => 'teacher-lounge',
                'description' => 'Strictly for educators. Discuss teaching methods, student progress, and collaborate on course content.',
                'icon' => '☕',
                'type' => 'teacher',
                'is_universal' => false,
                'is_active' => true
            ],
            [
                'name' => 'GATE CS Teachers',
                'slug' => 'gate-cs-teachers',
                'description' => 'Discussion group for GATE Computer Science educators to share strategies and feedback.',
                'icon' => '👨‍🏫',
                'type' => 'teacher',
                'is_universal' => false,
                'is_active' => true
            ],
            // Student Groups
            [
                'name' => 'Student Sanctuary',
                'slug' => 'student-sanctuary',
                'description' => 'A student-only zone for peer support, study tips, and sharing learning experiences without teacher presence.',
                'icon' => '🎓',
                'type' => 'student',
                'is_universal' => false,
                'is_active' => true
            ],
            [
                'name' => 'GATE Prep Strategy',
                'slug' => 'gate-prep-strategy',
                'description' => 'Students discussing the best ways to crack GATE. Share notes and schedules.',
                'icon' => '📚',
                'type' => 'student',
                'is_universal' => false,
                'is_active' => true
            ],
        ];

        foreach ($groups as $group) {
            ForumGroup::updateOrCreate(['slug' => $group['slug']], $group);
        }
    }
}
