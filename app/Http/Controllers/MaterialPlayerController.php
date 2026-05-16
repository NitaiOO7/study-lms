<?php

namespace App\Http\Controllers;

use App\Models\StudyMaterial;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class MaterialPlayerController extends Controller
{
    public function show(StudyMaterial $studyMaterial)
    {
        abort_unless($studyMaterial->isVideo(), 404);
        abort_unless($this->canView($studyMaterial), 403);

        $studyMaterial->load(['course', 'subject', 'channel.teacher']);

        $playlist = StudyMaterial::query()
            ->where('type', 'video')
            ->where(function ($query) use ($studyMaterial) {
                if ($studyMaterial->course_id) {
                    $query->where('course_id', $studyMaterial->course_id);
                } else {
                    $query->where('channel_id', $studyMaterial->channel_id);
                }
            })
            ->orderBy('sort_order')
            ->latest()
            ->get()
            ->filter(fn (StudyMaterial $material) => $material->isVideo())
            ->values();

        return view('materials.watch', [
            'material' => $studyMaterial,
            'playlist' => $playlist,
            'course' => $studyMaterial->course,
            'teacherName' => optional(optional($studyMaterial->channel)->teacher)->name
                ?? optional($studyMaterial->channel)->name
                ?? 'Instructor',
        ]);
    }

    private function canView(StudyMaterial $material): bool
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return optional($user->channel)->id === $material->channel_id;
        }

        if ($material->is_free || optional($material->course)->is_free) {
            return true;
        }

        if (!$material->course_id) {
            return false;
        }

        return Subscription::where('student_id', $user->id)
            ->where('course_id', $material->course_id)
            ->where('status', 'active')
            ->exists();
    }
}
