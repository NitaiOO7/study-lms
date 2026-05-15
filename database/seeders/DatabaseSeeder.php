<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Subject;
use App\Models\ForumGroup;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // Create Permissions
        $permissions = [
            'manage-users', 'manage-channels', 'manage-courses', 'manage-subjects',
            'manage-tests', 'manage-forums', 'view-reports', 'manage-materials',
            'create-channel', 'create-course', 'create-test', 'upload-material',
            'take-test', 'view-test-report', 'purchase-course', 'post-in-forum',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions
        $adminRole->givePermissionTo(Permission::all());
        $teacherRole->givePermissionTo([
            'create-channel', 'create-course', 'create-test', 'upload-material',
            'view-reports', 'manage-materials', 'post-in-forum',
        ]);
        $studentRole->givePermissionTo([
            'take-test', 'view-test-report', 'purchase-course', 'post-in-forum',
        ]);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@lms.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create Demo Teacher
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@lms.com'],
            [
                'name' => 'Demo Teacher',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'bio' => 'Experienced educator with 10+ years of teaching.',
            ]
        );
        if (!$teacher->hasRole('teacher')) {
            $teacher->assignRole('teacher');
        }

        // Create Demo Student
        $student = User::firstOrCreate(
            ['email' => 'student@lms.com'],
            [
                'name' => 'Demo Student',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (!$student->hasRole('student')) {
            $student->assignRole('student');
        }

        // Create Subjects — All levels
        $subjectData = [
            // HS Level
            ['name' => 'Bengali', 'level' => 'hs', 'icon' => '📝'],
            ['name' => 'English', 'level' => 'hs', 'icon' => '📖'],
            ['name' => 'Hindi', 'level' => 'hs', 'icon' => '📜'],
            ['name' => 'History', 'level' => 'hs', 'icon' => '🏛️'],
            ['name' => 'Geography', 'level' => 'hs', 'icon' => '🌍'],
            ['name' => 'Mathematics', 'level' => 'hs', 'icon' => '📐'],
            ['name' => 'Physics', 'level' => 'hs', 'icon' => '⚛️'],
            ['name' => 'Chemistry', 'level' => 'hs', 'icon' => '🧪'],
            ['name' => 'Biology', 'level' => 'hs', 'icon' => '🧬'],
            ['name' => 'Life Science', 'level' => 'hs', 'icon' => '🔬'],
            ['name' => 'Computer Science', 'level' => 'hs', 'icon' => '💻'],
            ['name' => 'Political Science', 'level' => 'hs', 'icon' => '🏛️'],
            ['name' => 'Economics', 'level' => 'hs', 'icon' => '📊'],
            ['name' => 'Philosophy', 'level' => 'hs', 'icon' => '🤔'],
            ['name' => 'Education', 'level' => 'hs', 'icon' => '🎓'],
            ['name' => 'Sanskrit', 'level' => 'hs', 'icon' => '📿'],
            ['name' => 'Sociology', 'level' => 'hs', 'icon' => '👥'],
            ['name' => 'Physical Education', 'level' => 'hs', 'icon' => '🏃'],
            ['name' => 'Environmental Studies', 'level' => 'hs', 'icon' => '🌱'],

            // Graduate Level
            ['name' => 'Bengali', 'level' => 'graduate', 'icon' => '📝'],
            ['name' => 'English', 'level' => 'graduate', 'icon' => '📖'],
            ['name' => 'Hindi', 'level' => 'graduate', 'icon' => '📜'],
            ['name' => 'History', 'level' => 'graduate', 'icon' => '🏛️'],
            ['name' => 'Geography', 'level' => 'graduate', 'icon' => '🌍'],
            ['name' => 'Mathematics', 'level' => 'graduate', 'icon' => '📐'],
            ['name' => 'Physics', 'level' => 'graduate', 'icon' => '⚛️'],
            ['name' => 'Chemistry', 'level' => 'graduate', 'icon' => '🧪'],
            ['name' => 'Biology', 'level' => 'graduate', 'icon' => '🧬'],
            ['name' => 'Life Science', 'level' => 'graduate', 'icon' => '🔬'],
            ['name' => 'Computer Science', 'level' => 'graduate', 'icon' => '💻'],
            ['name' => 'Political Science', 'level' => 'graduate', 'icon' => '🏛️'],
            ['name' => 'Economics', 'level' => 'graduate', 'icon' => '📊'],
            ['name' => 'Philosophy', 'level' => 'graduate', 'icon' => '🤔'],
            ['name' => 'Commerce', 'level' => 'graduate', 'icon' => '💼'],
            ['name' => 'Accounting', 'level' => 'graduate', 'icon' => '📒'],
            ['name' => 'Sociology', 'level' => 'graduate', 'icon' => '👥'],
            ['name' => 'Psychology', 'level' => 'graduate', 'icon' => '🧠'],
            ['name' => 'Environmental Science', 'level' => 'graduate', 'icon' => '🌱'],
            ['name' => 'Statistics', 'level' => 'graduate', 'icon' => '📈'],
            ['name' => 'Zoology', 'level' => 'graduate', 'icon' => '🐾'],
            ['name' => 'Botany', 'level' => 'graduate', 'icon' => '🌿'],

            // Master Level
            ['name' => 'Bengali', 'level' => 'master', 'icon' => '📝'],
            ['name' => 'English', 'level' => 'master', 'icon' => '📖'],
            ['name' => 'History', 'level' => 'master', 'icon' => '🏛️'],
            ['name' => 'Geography', 'level' => 'master', 'icon' => '🌍'],
            ['name' => 'Mathematics', 'level' => 'master', 'icon' => '📐'],
            ['name' => 'Physics', 'level' => 'master', 'icon' => '⚛️'],
            ['name' => 'Chemistry', 'level' => 'master', 'icon' => '🧪'],
            ['name' => 'Computer Science', 'level' => 'master', 'icon' => '💻'],
            ['name' => 'Economics', 'level' => 'master', 'icon' => '📊'],
            ['name' => 'Political Science', 'level' => 'master', 'icon' => '🏛️'],
            ['name' => 'Sociology', 'level' => 'master', 'icon' => '👥'],
            ['name' => 'Psychology', 'level' => 'master', 'icon' => '🧠'],
            ['name' => 'Commerce', 'level' => 'master', 'icon' => '💼'],
            ['name' => 'Education', 'level' => 'master', 'icon' => '🎓'],
            ['name' => 'Environmental Science', 'level' => 'master', 'icon' => '🌱'],
            ['name' => 'Life Science', 'level' => 'master', 'icon' => '🔬'],
        ];

        foreach ($subjectData as $subject) {
            $slug = Str::slug($subject['name'] . '-' . $subject['level']);
            Subject::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $subject['name'],
                    'icon' => $subject['icon'],
                    'level' => $subject['level'],
                    'description' => $subject['name'] . ' - ' . strtoupper($subject['level']) . ' Level',
                    'is_active' => true,
                ]
            );
        }

        // Create Universal Forum Group
        ForumGroup::updateOrCreate(
            ['slug' => 'universal-group'],
            [
                'name' => 'Universal Group',
                'description' => 'A universal discussion group for all students across all subjects. Post any question, share answers, and connect with peers.',
                'is_universal' => true,
                'is_active' => true,
            ]
        );

        // Create Subject-specific Forum Groups (one per unique subject name)
        $uniqueSubjects = Subject::select('name')->distinct()->pluck('name');
        foreach ($uniqueSubjects as $subjectName) {
            $subject = Subject::where('name', $subjectName)->first();
            ForumGroup::updateOrCreate(
                ['slug' => Str::slug($subjectName . '-discussion')],
                [
                    'name' => $subjectName . ' Discussion',
                    'description' => 'Discussion forum for ' . $subjectName . ' students.',
                    'subject_id' => $subject->id,
                    'icon' => $subject->icon,
                    'is_universal' => false,
                    'is_active' => true,
                ]
            );
        }

        // Subscription Plans
        $this->call(SubscriptionPlanSeeder::class);

        // Exam Hierarchy
        $this->call(ExamHierarchySeeder::class);

        // Community Groups
        $this->call(ForumGroupSeeder::class);
    }
}
