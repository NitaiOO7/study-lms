<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subject;
use App\Models\Channel;
use App\Models\TestSeries;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCourses = Course::published()->with(['channel', 'subject'])->latest()->take(8)->get();
        $subjects = Subject::active()->get()->groupBy('level');
        $channels = Channel::active()->with('teacher')->withCount('courses')->latest()->take(6)->get();
        $demoTestSeries = TestSeries::demo()->published()->with(['course.channel'])->latest()->take(4)->get();

        return view('welcome', compact('featuredCourses', 'subjects', 'channels', 'demoTestSeries'));
    }

    public function channelProfile(Channel $channel)
    {
        $channel->load(['teacher', 'courses.subject', 'testSeries']);
        $courses = $channel->courses()->published()->with('subject')->paginate(12);

        return view('channel-profile', compact('channel', 'courses'));
    }

    public function subjectCourses(Subject $subject)
    {
        $courses = Course::published()->where('subject_id', $subject->id)->with(['channel'])->paginate(12);
        return view('subject-courses', compact('subject', 'courses'));
    }
}
