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
use App\Models\Lesson;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function analytics()
    {
        $user = Auth::user();
        $overview = $this->analyticsService->getUserOverview($user);
        
        $attempts = $user->testAttempts()
            ->where('status', 'completed')
            ->with('section')
            ->orderBy('completed_at')
            ->get();

        $trendLabels = $attempts->map(fn($a) => $a->completed_at->format('M d'));
        $trendData = $attempts->pluck('percentage');

        // Aggregate topic analysis across all tests
        $topicAnalysis = \Illuminate\Support\Facades\DB::table('student_answers')
            ->join('questions', 'student_answers.question_id', '=', 'questions.id')
            ->join('topics', 'questions.topic_id', '=', 'topics.id')
            ->join('test_attempts', 'student_answers.test_attempt_id', '=', 'test_attempts.id')
            ->where('test_attempts.student_id', $user->id)
            ->select('topics.name as topic', 
                     \Illuminate\Support\Facades\DB::raw('count(*) as total'),
                     \Illuminate\Support\Facades\DB::raw('sum(is_correct) as correct'))
            ->groupBy('topics.name')
            ->get();

        $avgPercentile = $attempts->avg(fn($a) => $this->analyticsService->getAttemptRankings($a)['percentile']) ?? 0;

        return view('student.analytics', compact('overview', 'trendLabels', 'trendData', 'topicAnalysis', 'avgPercentile'));
    }

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
        $course->load(['channel', 'subject', 'testSeries.sections', 'studyMaterials', 'lessons']);
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

        $responses = json_decode($request->input('responses'), true) ?? [];
        $timeSpent = $request->input('time_spent', 0);

        $section = $attempt->section;
        $questions = $section->questions()->with('options')->get();

        $correctCount = 0;
        $wrongCount = 0;
        $skippedCount = 0;
        $totalScore = 0;

        foreach ($questions as $question) {
            $userResponse = $responses[$question->id] ?? null;
            
            if (!$userResponse || empty($userResponse['value'])) {
                $skippedCount++;
                continue;
            }

            $isCorrect = false;
            $marksObtained = 0;

            if ($question->type === 'mcq') {
                $correctOption = $question->options()->where('is_correct', true)->first();
                $isCorrect = $correctOption && $correctOption->id == $userResponse['value'];
            } 
            elseif ($question->type === 'msq') {
                $correctOptionIds = $question->options()->where('is_correct', true)->pluck('id')->toArray();
                $userOptionIds = (array) $userResponse['value'];
                sort($correctOptionIds);
                sort($userOptionIds);
                $isCorrect = $correctOptionIds === $userOptionIds;
            } 
            elseif ($question->type === 'nat') {
                $correctOption = $question->options()->first(); // For NAT, we might store the answer in option_text or a specific field
                // Simple equality check for now
                $isCorrect = $correctOption && floatval($correctOption->option_text) == floatval($userResponse['value']);
            }

            if ($isCorrect) {
                $correctCount++;
                $marksObtained = $question->marks;
            } else {
                $wrongCount++;
                $marksObtained = -$question->negative_marks;
            }

            $totalScore += $marksObtained;

            StudentAnswer::updateOrCreate(
                ['test_attempt_id' => $attempt->id, 'question_id' => $question->id],
                [
                    'selected_option_id' => $question->type === 'mcq' ? $userResponse['value'] : null,
                    'selected_option_ids' => $question->type === 'msq' ? $userResponse['value'] : null,
                    'text_answer' => $question->type === 'nat' ? $userResponse['value'] : null,
                    'is_correct' => $isCorrect,
                    'marks_obtained' => $marksObtained,
                    'time_spent_seconds' => 0, // We could track per-question time if we wanted
                ]
            );
        }

        $totalPossibleMarks = $questions->sum('marks');
        $percentage = $totalPossibleMarks > 0 ? round(($totalScore / $totalPossibleMarks) * 100, 2) : 0;

        $attempt->update([
            'attempted' => $correctCount + $wrongCount,
            'correct' => $correctCount,
            'wrong' => $wrongCount,
            'skipped' => $skippedCount,
            'score' => max(0, $totalScore),
            'total_marks' => $totalPossibleMarks,
            'percentage' => max(0, $percentage),
            'status' => 'completed',
            'completed_at' => now(),
            'time_taken_seconds' => $timeSpent,
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

    // Learning Room (Videos & PDFs)
    public function learn(Course $course, Lesson $lesson = null)
    {
        $isSubscribed = Subscription::where('student_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        // Allow access if subscribed, or course is free, or specific lesson is free (demo)
        $canAccess = $isSubscribed || $course->is_free;

        $lessons = $course->lessons()->orderBy('sort_order')->get();

        if ($lessons->isEmpty()) {
            return back()->with('error', 'No lessons available for this course yet.');
        }

        if (!$lesson) {
            $lesson = $lessons->first();
        } else {
            if ($lesson->course_id !== $course->id) {
                abort(404);
            }
        }

        if (!$canAccess && !$lesson->is_free) {
            return redirect()->route('student.course.detail', $course->slug)
                ->with('error', 'Please subscribe to access this premium lesson.');
        }

        return view('student.learning-room', compact('course', 'lessons', 'lesson', 'isSubscribed'));
    }
}
