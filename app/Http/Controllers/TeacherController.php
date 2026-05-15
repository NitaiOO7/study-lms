<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Course;
use App\Models\Subject;
use App\Models\StudyMaterial;
use App\Models\TestSeries;
use App\Models\Section;
use App\Models\Question;
use App\Models\Option;
use App\Models\Subscription;
use App\Models\TestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $channel = $user->channel;

        if (!$channel) {
            $plans = \App\Models\SubscriptionPlan::where('is_active', true)->get();
            return view('teacher.create-channel', compact('plans'));
        }

        $stats = [
            'total_courses' => $channel->courses()->count(),
            'total_students' => Subscription::whereIn('course_id', $channel->courses()->pluck('id'))->distinct('student_id')->count('student_id'),
            'total_test_series' => $channel->testSeries()->count(),
            'total_materials' => $channel->studyMaterials()->count(),
            'total_revenue' => Subscription::whereIn('course_id', $channel->courses()->pluck('id'))->sum('amount_paid'),
        ];

        $recentSubscriptions = Subscription::with(['student', 'course'])
            ->whereIn('course_id', $channel->courses()->pluck('id'))
            ->latest()->take(5)->get();

        $activeSubscription = \App\Models\ChannelSubscription::where('channel_id', $channel->id)
            ->where('status', 'active')
            ->with('plan')
            ->latest()
            ->first();

        return view('teacher.dashboard', compact('channel', 'stats', 'recentSubscriptions', 'activeSubscription'));
    }

    // Channel Management
    public function storeChannel(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $plan = \App\Models\SubscriptionPlan::find($request->plan_id);

        $data = [
            'teacher_id' => Auth::id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'is_active' => $plan->price == 0, // Active immediately only if free
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('channels/logos', 'public');
        }

        $channel = Channel::create($data);

        if ($plan->price > 0) {
            return redirect()->route('teacher.checkout', $plan->slug)->with('info', 'Please complete payment to activate your channel.');
        }

        // Create free subscription record
        \App\Models\ChannelSubscription::create([
            'channel_id' => $channel->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($plan->duration_days),
            'amount_paid' => 0,
        ]);

        return redirect()->route('teacher.dashboard')->with('success', 'Channel created successfully with Basic Plan!');
    }

    // Course Management
    public function courses()
    {
        $channel = Auth::user()->channel;
        $courses = $channel->courses()->with('subject')->latest()->paginate(20);
        $subjects = Subject::active()->get();
        return view('teacher.courses', compact('courses', 'subjects', 'channel'));
    }

    public function createCourse()
    {
        $subjects = Subject::active()->get()->groupBy('level');
        return view('teacher.create-course', compact('subjects'));
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'level' => 'required|in:hs,graduate,master',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $channel = Auth::user()->channel;

        $data = [
            'channel_id' => $channel->id,
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'description' => $request->description,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'level' => $request->level,
            'is_free' => $request->price == 0,
            'is_published' => $request->has('is_published'),
        ];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        Course::create($data);
        return redirect()->route('teacher.courses')->with('success', 'Course created successfully!');
    }

    // Study Materials
    public function materials()
    {
        $channel = Auth::user()->channel;
        $materials = $channel->studyMaterials()->with(['course', 'subject'])->latest()->paginate(20);
        $courses = $channel->courses;
        $subjects = Subject::active()->get();
        return view('teacher.materials', compact('materials', 'courses', 'subjects', 'channel'));
    }

    public function storeMaterial(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'course_id' => 'nullable|exists:courses,id',
            'type' => 'required|in:pdf,video,document,link,image,video_file',
            'file' => 'nullable|file|max:102400', // 100MB
            'external_url' => 'nullable|url',
            'description' => 'nullable|string',
        ]);

        $channel = Auth::user()->channel;

        $type = $request->type;
        if ($type === 'video_file') {
            $type = 'video'; // Map to valid ENUM value
        }

        $data = [
            'channel_id' => $channel->id,
            'subject_id' => $request->subject_id,
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $type,
            'external_url' => $request->external_url,
            'is_free' => $request->has('is_free'),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = $file->store('materials', 'public');
            $data['file_size'] = round($file->getSize() / 1024);
        }

        StudyMaterial::create($data);
        return redirect()->route('teacher.materials')->with('success', 'Material uploaded successfully!');
    }

    // Test Series Management
    public function testSeries()
    {
        $channel = Auth::user()->channel;
        $testSeries = $channel->testSeries()->with(['course', 'sections'])->latest()->paginate(20);
        $courses = $channel->courses;
        return view('teacher.test-series', compact('testSeries', 'courses', 'channel'));
    }

    public function createTestSeries()
    {
        $channel = Auth::user()->channel;
        $courses = $channel->courses;
        return view('teacher.create-test-series', compact('courses'));
    }

    public function storeTestSeries(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description' => 'nullable|string',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0',
        ]);

        $channel = Auth::user()->channel;

        TestSeries::create([
            'course_id' => $request->course_id,
            'channel_id' => $channel->id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'description' => $request->description,
            'is_demo' => $request->has('is_demo'),
            'is_published' => $request->has('is_published'),
            'total_marks' => $request->total_marks,
            'passing_marks' => $request->passing_marks,
        ]);

        return redirect()->route('teacher.test-series')->with('success', 'Test Series created!');
    }

    // Section Management
    public function manageSections(TestSeries $testSeries)
    {
        $this->authorizeChannel($testSeries->channel_id);
        $sections = $testSeries->sections()->with('questions.options')->get();
        return view('teacher.manage-sections', compact('testSeries', 'sections'));
    }

    public function storeSection(Request $request, TestSeries $testSeries)
    {
        $this->authorizeChannel($testSeries->channel_id);

        $request->validate([
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0',
        ]);

        $sortOrder = $testSeries->sections()->max('sort_order') + 1;

        Section::create([
            'test_series_id' => $testSeries->id,
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'total_marks' => $request->total_marks,
            'passing_marks' => $request->passing_marks,
            'sort_order' => $sortOrder,
            'is_locked' => $sortOrder > 1,
        ]);

        return back()->with('success', 'Section added!');
    }

    // Question Management
    public function storeQuestion(Request $request, Section $section)
    {
        $this->authorizeChannel($section->testSeries->channel_id);

        $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:mcq,msq,nat',
            'marks' => 'required|integer|min:1',
            'negative_marks' => 'nullable|numeric|min:0',
            'explanation' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'correct_option' => 'required|integer',
        ]);

        $sortOrder = $section->questions()->max('sort_order') + 1;

        $question = Question::create([
            'section_id' => $section->id,
            'question_text' => $request->question_text,
            'type' => $request->type,
            'marks' => $request->marks,
            'negative_marks' => $request->negative_marks ?? 0,
            'explanation' => $request->explanation,
            'sort_order' => $sortOrder,
        ]);

        foreach ($request->options as $index => $optionData) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $optionData['text'],
                'is_correct' => $index == $request->correct_option,
                'sort_order' => $index + 1,
            ]);
        }

        return back()->with('success', 'Question added!');
    }

    // Lesson Management
    public function lessons(Course $course)
    {
        $this->authorizeChannel($course->channel_id);
        $lessons = $course->lessons()->latest()->paginate(20);
        return view('teacher.lessons', compact('course', 'lessons'));
    }

    public function createLesson(Course $course)
    {
        $this->authorizeChannel($course->channel_id);
        return view('teacher.create-lesson', compact('course'));
    }

    public function storeLesson(Request $request, Course $course)
    {
        $this->authorizeChannel($course->channel_id);

        $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|url',
            'video_file' => 'nullable|mimes:mp4,webm|max:102400', // 100MB
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'annotated_pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'description' => 'nullable|string',
        ]);

        $data = [
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'video_url' => $request->video_url,
            'sort_order' => $course->lessons()->count() + 1,
            'is_free' => $request->has('is_free'),
        ];

        if ($request->hasFile('video_file')) {
            $data['video_path'] = $request->file('video_file')->store('lessons/videos', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            $data['pdf_path'] = $request->file('pdf_file')->store('lessons/pdfs', 'public');
        }

        if ($request->hasFile('annotated_pdf_file')) {
            $data['annotated_pdf_path'] = $request->file('annotated_pdf_file')->store('lessons/pdfs/annotated', 'public');
        }

        \App\Models\Lesson::create($data);

        return redirect()->route('teacher.lessons', $course->id)->with('success', 'Lesson added successfully!');
    }

    // Bundle Management
    public function bundles()
    {
        $bundles = \App\Models\CourseBundle::where('creator_id', Auth::id())
            ->with(['courses', 'collaborations.teacher'])
            ->latest()
            ->paginate(12);
        
        $receivedCollaborations = \App\Models\BundleCollaboration::where('teacher_id', Auth::id())
            ->where('status', 'pending')
            ->with('bundle')
            ->get();

        return view('teacher.bundles.index', compact('bundles', 'receivedCollaborations'));
    }

    public function createBundle()
    {
        $courses = \App\Models\Course::where('channel_id', Auth::user()->channel->id)->get();
        $otherTeachers = \App\Models\User::role('teacher')->where('id', '!=', Auth::id())->get();
        
        return view('teacher.bundles.create', compact('courses', 'otherTeachers'));
    }

    public function storeBundle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'bundle_price' => 'required|numeric|min:0',
            'course_ids' => 'required|array|min:1',
            'collaborators' => 'nullable|array',
            'collaborators.*.teacher_id' => 'required|exists:users,id',
            'collaborators.*.share' => 'required|integer|min:0|max:100',
        ]);

        $originalPrice = \App\Models\Course::whereIn('id', $request->course_ids)->sum('price');

        $bundle = \App\Models\CourseBundle::create([
            'creator_id' => Auth::id(),
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title) . '-' . uniqid(),
            'description' => $request->description,
            'original_price' => $originalPrice,
            'bundle_price' => $request->bundle_price,
            'is_published' => true,
        ]);

        $bundle->courses()->attach($request->course_ids);

        if ($request->collaborators) {
            foreach ($request->collaborators as $collab) {
                \App\Models\BundleCollaboration::create([
                    'bundle_id' => $bundle->id,
                    'teacher_id' => $collab['teacher_id'],
                    'revenue_share_percentage' => $collab['share'],
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()->route('teacher.bundles')->with('success', 'Course bundle created and collaboration invites sent!');
    }

    public function acceptCollaboration(\App\Models\BundleCollaboration $collaboration)
    {
        if ($collaboration->teacher_id !== Auth::id()) abort(403);
        $collaboration->update(['status' => 'accepted']);
        return back()->with('success', 'Collaboration accepted!');
    }

    // Helper
    private function authorizeChannel(int $channelId): void
    {
        $channel = Auth::user()->channel;
        if (!$channel || $channel->id !== $channelId) {
            abort(403);
        }
    }
}
