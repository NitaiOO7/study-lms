<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ==========================================
// PUBLIC ROUTES
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/channel/{channel:slug}', [HomeController::class, 'channelProfile'])->name('channel.profile');
Route::get('/subject/{subject:slug}', [HomeController::class, 'subjectCourses'])->name('subject.courses');

// Post-login redirect
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
    if ($user->hasRole('teacher')) return redirect()->route('teacher.dashboard');
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ==========================================
// ADMIN ROUTES
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/channels', [AdminController::class, 'channels'])->name('channels');
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects');
    Route::post('/channel/{channel}/toggle', [AdminController::class, 'toggleChannelStatus'])->name('channel.toggle');
    Route::post('/channel/{channel}/verify', [AdminController::class, 'verifyChannel'])->name('channel.verify');
});

// ==========================================
// TEACHER ROUTES
// ==========================================
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::post('/channel', [TeacherController::class, 'storeChannel'])->name('channel.store');

    // Courses
    Route::get('/courses', [TeacherController::class, 'courses'])->name('courses');
    Route::get('/courses/create', [TeacherController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [TeacherController::class, 'storeCourse'])->name('courses.store');

    // Study Materials
    Route::get('/materials', [TeacherController::class, 'materials'])->name('materials');
    Route::post('/materials', [TeacherController::class, 'storeMaterial'])->name('materials.store');

    // Test Series
    Route::get('/test-series', [TeacherController::class, 'testSeries'])->name('test-series');
    Route::get('/test-series/create', [TeacherController::class, 'createTestSeries'])->name('test-series.create');
    Route::post('/test-series', [TeacherController::class, 'storeTestSeries'])->name('test-series.store');
    Route::get('/test-series/{testSeries}/sections', [TeacherController::class, 'manageSections'])->name('test-series.sections');
    Route::post('/test-series/{testSeries}/sections', [TeacherController::class, 'storeSection'])->name('sections.store');
    Route::post('/sections/{section}/questions', [TeacherController::class, 'storeQuestion'])->name('questions.store');
});

// ==========================================
// STUDENT ROUTES
// ==========================================
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/browse', [StudentController::class, 'browseCourses'])->name('browse');
    Route::get('/course/{course:slug}', [StudentController::class, 'courseDetail'])->name('course.detail');
    Route::post('/course/{course}/subscribe', [StudentController::class, 'subscribeCourse'])->name('course.subscribe');
    Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('my-courses');

    // Test Series
    Route::get('/course/{course}/test-series', [StudentController::class, 'testSeriesList'])->name('test-series');
    Route::get('/test-series/{testSeries}/sections', [StudentController::class, 'viewSections'])->name('view-sections');
    Route::get('/section/{section}/start', [StudentController::class, 'startTest'])->name('start-test');
    Route::post('/test/{attempt}/submit', [StudentController::class, 'submitTest'])->name('submit-test');
    Route::get('/test-report/{attempt}', [StudentController::class, 'testReport'])->name('test-report');

    // Study Materials
    Route::get('/course/{course}/materials', [StudentController::class, 'studyMaterials'])->name('materials');
});

// ==========================================
// COMMUNITY ROUTES (All authenticated users)
// ==========================================
Route::middleware(['auth'])->prefix('community')->name('community.')->group(function () {
    Route::get('/', [CommunityController::class, 'index'])->name('index');
    Route::get('/group/{forumGroup:slug}', [CommunityController::class, 'showGroup'])->name('group');
    Route::post('/group/{forumGroup:slug}/post', [CommunityController::class, 'storePost'])->name('post.store');
    Route::get('/post/{post}', [CommunityController::class, 'showPost'])->name('post.show');
    Route::post('/post/{post}/comment', [CommunityController::class, 'storeComment'])->name('comment.store');
    Route::post('/comment/{comment}/answer', [CommunityController::class, 'markAsAnswer'])->name('comment.answer');
    Route::post('/like', [CommunityController::class, 'toggleLike'])->name('like');
});

require __DIR__.'/auth.php';
