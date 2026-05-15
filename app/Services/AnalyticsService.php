<?php

namespace App\Services;

use App\Models\TestAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get user performance across all tests
     */
    public function getUserOverview(User $user)
    {
        $attempts = TestAttempt::where('student_id', $user->id)
            ->where('status', 'completed')
            ->get();

        return [
            'total_tests' => $attempts->count(),
            'avg_score' => $attempts->avg('percentage'),
            'total_time' => $attempts->sum('time_taken_seconds'),
            'accuracy' => $this->calculateAccuracy($attempts),
        ];
    }

    /**
     * Calculate global rank and percentile for an attempt
     */
    public function getAttemptRankings(TestAttempt $attempt)
    {
        $totalParticipants = TestAttempt::where('section_id', $attempt->section_id)
            ->where('status', 'completed')
            ->count();

        $rank = TestAttempt::where('section_id', $attempt->section_id)
            ->where('status', 'completed')
            ->where(function($query) use ($attempt) {
                $query->where('score', '>', $attempt->score)
                      ->orWhere(function($q) use ($attempt) {
                          $q->where('score', $attempt->score)
                            ->where('time_taken_seconds', '<', $attempt->time_taken_seconds);
                      });
            })
            ->count() + 1;

        $percentile = $totalParticipants > 1 
            ? round((($totalParticipants - $rank) / ($totalParticipants - 1)) * 100, 2)
            : 100;

        return [
            'rank' => $rank,
            'total' => $totalParticipants,
            'percentile' => $percentile
        ];
    }

    /**
     * Get section-wise performance for a test series
     */
    public function getSectionWiseAnalysis(TestAttempt $attempt)
    {
        return DB::table('student_answers')
            ->join('questions', 'student_answers.question_id', '=', 'questions.id')
            ->join('topics', 'questions.topic_id', '=', 'topics.id')
            ->where('student_answers.test_attempt_id', $attempt->id)
            ->select('topics.name as topic', 
                     DB::raw('count(*) as total'),
                     DB::raw('sum(is_correct) as correct'),
                     DB::raw('sum(marks_obtained) as marks'))
            ->groupBy('topics.name')
            ->get();
    }

    private function calculateAccuracy($attempts)
    {
        $attempted = $attempts->sum('attempted');
        if ($attempted == 0) return 0;
        return round(($attempts->sum('correct') / $attempted) * 100, 2);
    }
}
