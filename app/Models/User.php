<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Teacher's channel
    public function channel()
    {
        return $this->hasOne(Channel::class, 'teacher_id');
    }

    // Student subscriptions
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'student_id');
    }

    // Test attempts
    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class, 'student_id');
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class, 'student_id');
    }

    // Posts
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Check if user is admin
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    // Check if user is teacher
    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    // Check if user is student
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }
}
