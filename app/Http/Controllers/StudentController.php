<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\TestSeries;
use App\Models\Section;
use App\Models\TestAttempt;
use App\Models\StudentAnswer;
use App\Models\StudyMaterial;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $activeSubscriptions = $user->subscriptions()->where('status', 'active')->with('course.channel')->get();
        $recentAttempts = $user->testAttempts()->with(['section', 'testSeries'])->latest()->take(5)->get();
        $totalTests = $user->testAttempts()->where('status', 'completed')->count();
        $avgScore = $user->testAttempts()->where('status', 'completed')->avg('percentage') ?? 0;

        $featuredCourses = Course::published()->whereHas('channel', function($q) {
            $q->where('is_verified', true);
        })->with(['channel', 'subject'])->latest()->take(6)->get();
        $subjects = Subject::active()->get()->groupBy('level');

        return view('student.dashboard', compact(
            'user', 'activeSubscriptions', 'recentAttempts',
            'totalTests', 'avgScore', 'featuredCourses', 'subjects'
        ));
    }

    // Browse Courses
    public function browseCourses(Request $request)
    {
        $query = Course::published()->whereHas('channel', function($q) {
            $q->where('is_verified', true);
        })->with(['channel', 'subject']);

        if ($request->subject) {
            $query->where('subject_id', $request->subject);
        }
        if ($request->level) {
            $query->where('level', $request->level);
        }
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $courses = $query->latest()->paginate(12);
        $subjects = Subject::active()->get()->groupBy('level');

        return view('student.browse-courses', compact('courses', 'subjects'));
    }

    // Course Detail
    public function courseDetail(Course $course)
    {
        $course->load(['channel', 'subject', 'testSeries.sections', 'studyMaterials']);
        $isSubscribed = false;

        if (Auth::check()) {
            $isSubscribed = Subscription::where('student_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists();
        }

        return view('student.course-detail', compact('course', 'isSubscribed'));
    }

    // Subscribe to Course
    public function subscribeCourse(Request $request, Course $course)
    {
        $user = Auth::user();

        // Check if already subscribed
        $existing = Subscription::where('student_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return back()->with('error', 'You are already subscribed to this course.');
        }

        Subscription::create([
            'student_id' => $user->id,
            'course_id' => $course->id,
            'amount_paid' => $course->is_free ? 0 : $course->price,
            'payment_id' => 'DEMO-' . strtoupper(uniqid()),
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($course->duration_days),
        ]);

        return redirect()->route('student.my-courses')->with('success', 'Successfully subscribed to ' . $course->title);
    }

    // My Courses
    public function myCourses()
    {
        $subscriptions = Auth::user()->subscriptions()
            ->with(['course.channel', 'course.subject', 'course.testSeries'])
            ->latest()
            ->paginate(12);

        return view('student.my-courses', compact('subscriptions'));
    }

    // Test Series for a course
    public function testSeriesList(Course $course)
    {
        $user = Auth::user();

        // Check subscription
        $isSubscribed = Subscription::where('student_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        $testSeries = $course->testSeries()->published()->with('sections')->get();

        // Also show demo test series
        $demoSeries = $course->testSeries()->demo()->published()->with('sections')->get();

        return view('student.test-series-list', compact('course', 'testSeries', 'demoSeries', 'isSubscribed'));
    }

    // View Sections of a Test Series
    public function viewSections(TestSeries $testSeries)
    {
        $user = Auth::user();
        $sections = $testSeries->sections()->with('questions')->get();

        // Check unlock status for each section
        $sectionsData = $sections->map(function ($section) use ($user) {
            $attempt = TestAttempt::where('student_id', $user->id)
                ->where('section_id', $section->id)
                ->first();

            return [
                'section' => $section,
                'is_unlocked' => $section->isUnlockedFor($user->id),
                'attempt' => $attempt,
                'status' => $attempt ? $attempt->status : 'not_started',
            ];
        });

        return view('student.view-sections', compact('testSeries', 'sectionsData'));
    }

    // Start Test
    public function startTest(Section $section)
    {
        $user = Auth::user();

        // Check if section is unlocked
        if (!$section->isUnlockedFor($user->id)) {
            return back()->with('error', 'You must complete the previous section first!');
        }

        // Check if already attempted
        $existingAttempt = TestAttempt::where('student_id', $user->id)
            ->where('section_id', $section->id)
            ->first();

        if ($existingAttempt && $existingAttempt->status === 'completed') {
            return redirect()->route('student.test-report', $existingAttempt->id);
        }

        // Create or resume attempt
        $attempt = TestAttempt::firstOrCreate(
            [
                'student_id' => $user->id,
                'section_id' => $section->id,
                'test_series_id' => $section->test_series_id,
            ],
            [
                'total_questions' => $section->questions()->count(),
                'status' => 'in_progress',
                'started_at' => now(),
            ]
        );

        $questions = $section->questions()->with('options')->get();

        return view('student.take-test', compact('section', 'attempt', 'questions'));
    }

    // Submit Test
    public function submitTest(Request $request, TestAttempt $attempt)
    {
        if ($attempt->student_id !== Auth::id()) {
            abort(403);
        }

        $section = $attempt->section;
        $questions = $section->questions()->with('options')->get();

        $correct = 0;
        $wrong = 0;
        $skipped = 0;
        $totalScore = 0;

        foreach ($questions as $question) {
            $selectedOptionId = $request->input('question_' . $question->id);

            if (!$selectedOptionId) {
                $skipped++;
                StudentAnswer::updateOrCreate(
                    ['test_attempt_id' => $attempt->id, 'question_id' => $question->id],
                    ['is_correct' => false, 'marks_obtained' => 0]
                );
                continue;
            }

            $correctOption = $question->options()->where('is_correct', true)->first();
            $isCorrect = $correctOption && $correctOption->id == $selectedOptionId;

            if ($isCorrect) {
                $correct++;
                $totalScore += $question->marks;
            } else {
                $wrong++;
                $totalScore -= $question->negative_marks;
            }

            StudentAnswer::updateOrCreate(
                ['test_attempt_id' => $attempt->id, 'question_id' => $question->id],
                [
                    'selected_option_id' => $selectedOptionId,
                    'is_correct' => $isCorrect,
                    'marks_obtained' => $isCorrect ? $question->marks : -$question->negative_marks,
                ]
            );
        }

        $totalMarks = $questions->sum('marks');
        $percentage = $totalMarks > 0 ? round(($totalScore / $totalMarks) * 100, 2) : 0;

        $attempt->update([
            'attempted' => $correct + $wrong,
            'correct' => $correct,
            'wrong' => $wrong,
            'skipped' => $skipped,
            'score' => max(0, $totalScore),
            'total_marks' => $totalMarks,
            'percentage' => max(0, $percentage),
            'status' => 'completed',
            'completed_at' => now(),
            'time_taken_seconds' => now()->diffInSeconds($attempt->started_at),
        ]);

        return redirect()->route('student.test-report', $attempt->id)->with('success', 'Test submitted successfully!');
    }

    // Test Report
    public function testReport(TestAttempt $attempt)
    {
        if ($attempt->student_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load(['section.questions.options', 'answers.selectedOption', 'answers.question.options', 'testSeries']);

        $rank = $attempt->getRank();
        $totalAttempts = $attempt->getTotalAttempts();
        $percentile = $attempt->getPercentile();

        // Leaderboard
        $leaderboard = TestAttempt::where('section_id', $attempt->section_id)
            ->where('test_series_id', $attempt->test_series_id)
            ->where('status', 'completed')
            ->with('student')
            ->orderByDesc('score')
            ->take(20)
            ->get();

        return view('student.test-report', compact('attempt', 'rank', 'totalAttempts', 'percentile', 'leaderboard'));
    }

    // Study Materials
    public function studyMaterials(Course $course)
    {
        $materials = $course->studyMaterials()->latest()->paginate(20);
        return view('student.study-materials', compact('course', 'materials'));
    }
}
