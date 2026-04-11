<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'section_id', 'test_series_id', 'total_questions',
        'attempted', 'correct', 'wrong', 'skipped', 'score', 'total_marks',
        'percentage', 'time_taken_seconds', 'status', 'started_at', 'completed_at'
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function testSeries()
    {
        return $this->belongsTo(TestSeries::class);
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Get rank of this attempt among all attempts for the same section
     */
    public function getRank(): int
    {
        return TestAttempt::where('section_id', $this->section_id)
            ->where('test_series_id', $this->test_series_id)
            ->where('status', 'completed')
            ->where('score', '>', $this->score)
            ->count() + 1;
    }

    /**
     * Get total students who attempted this section
     */
    public function getTotalAttempts(): int
    {
        return TestAttempt::where('section_id', $this->section_id)
            ->where('test_series_id', $this->test_series_id)
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get percentile
     */
    public function getPercentile(): float
    {
        $total = $this->getTotalAttempts();
        if ($total <= 1) return 100;
        $rank = $this->getRank();
        return round((($total - $rank) / ($total - 1)) * 100, 2);
    }
}
