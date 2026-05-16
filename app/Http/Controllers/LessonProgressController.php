<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonProgressController extends Controller
{
    public function store(Request $request, Lesson $lesson)
    {
        $user = Auth::user();
        $lesson->load('course');
        $course = $lesson->course;

        $hasAccess = $course->is_free
            || $lesson->is_free
            || Subscription::where('student_id', $user->id)
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists();

        abort_unless($hasAccess, 403);

        $data = $request->validate([
            'current_time' => ['required', 'numeric', 'min:0'],
            'duration' => ['nullable', 'numeric', 'min:0'],
            'watched_seconds' => ['nullable', 'numeric', 'min:0'],
            'max_watched_time' => ['nullable', 'numeric', 'min:0'],
        ]);

        $duration = (float) ($data['duration'] ?? 0);
        $currentTime = (float) $data['current_time'];
        $watchedSeconds = (float) ($data['watched_seconds'] ?? $currentTime);
        $maxWatchedTime = (float) ($data['max_watched_time'] ?? $currentTime);
        $watchedPercentage = $duration > 0
            ? min(100, round(($watchedSeconds / $duration) * 100, 2))
            : 0;

        $progress = LessonProgress::firstOrNew([
            'student_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $wasCompleted = (bool) $progress->is_completed;
        $isCompleted = $wasCompleted || $watchedPercentage >= 90;

        $progress->fill([
            'course_id' => $course->id,
            'current_time' => $currentTime,
            'duration' => $duration,
            'watched_seconds' => max((float) $progress->watched_seconds, $watchedSeconds),
            'watched_percentage' => max((float) $progress->watched_percentage, $watchedPercentage),
            'max_watched_time' => max((float) $progress->max_watched_time, $maxWatchedTime),
            'is_completed' => $isCompleted,
            'completed_at' => $isCompleted && !$wasCompleted ? now() : $progress->completed_at,
            'last_watched_at' => now(),
        ])->save();

        return response()->json([
            'saved' => true,
            'current_time' => $progress->current_time,
            'watched_percentage' => $progress->watched_percentage,
            'is_completed' => $progress->is_completed,
        ]);
    }
}
