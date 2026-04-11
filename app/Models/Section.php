<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_series_id', 'title', 'description', 'duration_minutes',
        'total_marks', 'passing_marks', 'sort_order', 'is_locked'
    ];

    public function testSeries()
    {
        return $this->belongsTo(TestSeries::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    /**
     * Check if this section is unlocked for a given student.
     * First section is always unlocked; subsequent sections require previous completion.
     */
    public function isUnlockedFor(int $studentId): bool
    {
        // First section is always unlocked
        if ($this->sort_order === 1) {
            return true;
        }

        // Find the previous section
        $previousSection = Section::where('test_series_id', $this->test_series_id)
            ->where('sort_order', $this->sort_order - 1)
            ->first();

        if (!$previousSection) {
            return true;
        }

        // Check if previous section is completed
        return TestAttempt::where('student_id', $studentId)
            ->where('section_id', $previousSection->id)
            ->where('status', 'completed')
            ->exists();
    }
}
