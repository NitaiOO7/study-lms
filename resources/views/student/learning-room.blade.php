<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $lesson->title }} - {{ $course->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --page: #08090d;
            --panel: #12151d;
            --panel-strong: #181c25;
            --line: rgba(255, 255, 255, 0.1);
            --muted: #9ca3af;
            --text: #f8fafc;
            --accent: #2563eb;
            --accent-2: #14b8a6;
            --danger: #ef4444;
            --success: #22c55e;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--page);
            color: var(--text);
            overflow: hidden;
        }

        button, select, input { font: inherit; }

        .learn-shell {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            min-height: 100vh;
            background:
                radial-gradient(circle at 20% 0%, rgba(37, 99, 235, 0.18), transparent 30%),
                linear-gradient(135deg, #08090d 0%, #11141c 48%, #050608 100%);
        }

        .theater {
            min-width: 0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--line);
        }

        .topbar {
            height: 64px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 18px;
            border-bottom: 1px solid var(--line);
            background: rgba(10, 12, 18, 0.86);
            backdrop-filter: blur(18px);
            z-index: 5;
        }

        .back-link {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
            text-decoration: none;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
        }

        .course-kicker {
            margin: 0 0 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .course-title {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.3;
        }

        .viewer-wrap {
            flex: 1;
            min-height: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 22px;
        }

        .lms-player {
            position: relative;
            width: min(100%, 1280px);
            aspect-ratio: 16 / 9;
            overflow: hidden;
            border-radius: 8px;
            background: #000;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.58);
        }

        .lms-player video,
        .pdf-viewer {
            width: 100%;
            height: 100%;
            display: block;
            background: #000;
        }

        .pdf-viewer {
            border: 0;
            background: #f8fafc;
        }

        .video-fallback {
            height: 100%;
            display: grid;
            place-items: center;
            color: var(--muted);
            text-align: center;
            padding: 32px;
        }

        .player-shade {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.68), transparent 24%, transparent 58%, rgba(0, 0, 0, 0.86));
            opacity: 1;
            transition: opacity 0.25s ease;
        }

        .player-meta {
            position: absolute;
            top: 28px;
            left: 32px;
            right: 32px;
            display: flex;
            justify-content: space-between;
            gap: 18px;
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .lesson-heading {
            max-width: min(680px, 68vw);
        }

        .lesson-heading h1 {
            margin: 0;
            font-size: clamp(24px, 3vw, 44px);
            line-height: 1.1;
            letter-spacing: 0;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.55);
        }

        .lesson-heading p,
        .teacher-stamp p {
            margin: 12px 0 0;
            color: rgba(255, 255, 255, 0.68);
            font-weight: 600;
        }

        .teacher-stamp {
            align-self: end;
            text-align: right;
            color: rgba(255, 255, 255, 0.86);
            font-weight: 800;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.55);
        }

        .center-controls {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 42px;
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .control-btn {
            border: 0;
            color: #fff;
            background: rgba(20, 24, 33, 0.72);
            backdrop-filter: blur(14px);
            cursor: pointer;
            transition: transform 0.18s ease, background 0.18s ease, opacity 0.18s ease;
        }

        .control-btn:hover { background: rgba(37, 99, 235, 0.92); transform: translateY(-1px); }

        .control-btn:disabled {
            cursor: not-allowed;
            opacity: 0.45;
        }

        .center-controls .control-btn {
            pointer-events: auto;
            width: 54px;
            height: 54px;
            border-radius: 50%;
            font-size: 21px;
        }

        .center-controls .play-toggle {
            width: 72px;
            height: 72px;
            font-size: 26px;
            background: rgba(255, 255, 255, 0.22);
        }

        .control-deck {
            position: absolute;
            left: 22px;
            right: 22px;
            bottom: 18px;
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 8px;
            background: rgba(8, 10, 15, 0.74);
            backdrop-filter: blur(20px);
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .progress-rail {
            position: relative;
            height: 6px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.22);
            cursor: pointer;
            overflow: hidden;
        }

        .progress-buffer,
        .progress-fill {
            position: absolute;
            inset: 0 auto 0 0;
            width: 0;
            border-radius: inherit;
        }

        .progress-buffer { background: rgba(255, 255, 255, 0.28); }
        .progress-fill { background: linear-gradient(90deg, var(--accent), var(--accent-2)); }

        .control-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 12px;
        }

        .control-group {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            font-size: 15px;
        }

        .timecode {
            min-width: 124px;
            color: rgba(255, 255, 255, 0.88);
            font-size: 14px;
            font-weight: 700;
            text-align: center;
        }

        .volume {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .volume input {
            width: 82px;
            accent-color: var(--accent);
        }

        .select-shell {
            position: relative;
        }

        .player-select {
            height: 36px;
            min-width: 76px;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            padding: 0 10px;
            cursor: pointer;
            outline: none;
        }

        .player-select option {
            color: #0f172a;
            background: #fff;
        }

        .player-status {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, 74px);
            padding: 8px 12px;
            border-radius: 8px;
            color: #fff;
            background: rgba(15, 23, 42, 0.86);
            font-size: 13px;
            font-weight: 700;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .player-status.show { opacity: 1; }

        .lms-player.is-playing.is-idle .player-shade,
        .lms-player.is-playing.is-idle .player-meta,
        .lms-player.is-playing.is-idle .center-controls,
        .lms-player.is-playing.is-idle .control-deck {
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
        }

        .content-details {
            padding: 0 22px 22px;
            color: var(--muted);
        }

        .content-details h2 {
            margin: 0 0 8px;
            color: var(--text);
            font-size: 22px;
        }

        .content-details p {
            margin: 0;
            line-height: 1.65;
        }

        .sidebar {
            min-height: 0;
            display: flex;
            flex-direction: column;
            background: rgba(18, 21, 29, 0.95);
        }

        .sidebar-head {
            padding: 22px;
            border-bottom: 1px solid var(--line);
        }

        .sidebar-head h2 {
            margin: 0 0 8px;
            font-size: 18px;
        }

        .course-progress {
            height: 7px;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
        }

        .course-progress span {
            display: block;
            height: 100%;
            width: var(--complete, 0%);
            background: linear-gradient(90deg, var(--accent), var(--success));
        }

        .lesson-list {
            overflow: auto;
            padding: 10px;
        }

        .lesson-card {
            display: block;
            padding: 14px;
            margin-bottom: 8px;
            color: var(--text);
            text-decoration: none;
            border: 1px solid transparent;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.04);
            transition: background 0.2s ease, border-color 0.2s ease;
        }

        .lesson-card:hover,
        .lesson-card.active {
            background: rgba(37, 99, 235, 0.13);
            border-color: rgba(37, 99, 235, 0.42);
        }

        .lesson-card h3 {
            margin: 0 0 10px;
            font-size: 14px;
            line-height: 1.45;
        }

        .lesson-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .lesson-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 9px;
            color: var(--text);
            text-decoration: none;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            font-size: 12px;
            font-weight: 800;
        }

        .pill.active { border-color: var(--accent); background: rgba(37, 99, 235, 0.2); }
        .pill.locked { color: #fecaca; }

        @media (max-width: 1024px) {
            body { overflow: auto; }

            .learn-shell {
                grid-template-columns: 1fr;
                min-height: 100vh;
            }

            .theater { border-right: 0; }
            .sidebar { min-height: 420px; }
            .viewer-wrap { padding: 14px; }
            .lms-player { border-radius: 0; width: calc(100% + 28px); }
        }

        @media (max-width: 720px) {
            .topbar { padding: 0 14px; height: 58px; }
            .course-kicker { display: none; }
            .course-title { font-size: 14px; }
            .player-meta { top: 16px; left: 16px; right: 16px; }
            .teacher-stamp { display: none; }
            .lesson-heading { max-width: 100%; }
            .lesson-heading h1 { font-size: 22px; }
            .lesson-heading p { margin-top: 8px; font-size: 13px; }
            .center-controls { gap: 18px; }
            .center-controls .control-btn { width: 46px; height: 46px; }
            .center-controls .play-toggle { width: 60px; height: 60px; }
            .control-deck { left: 10px; right: 10px; bottom: 10px; padding: 10px; }
            .control-row { flex-wrap: wrap; }
            .control-group { gap: 7px; }
            .volume input { display: none; }
            .timecode { min-width: auto; font-size: 12px; }
            .player-select { min-width: 66px; padding: 0 6px; }
            .content-details { padding: 0 14px 18px; }
        }
    </style>
</head>
<body>
@php
    use Illuminate\Support\Facades\Storage;

    $viewType = request('view', 'video');
    $videoUrl = $lesson->video_path ? Storage::url($lesson->video_path) : $lesson->video_url;
    $teacherName = optional(optional($course->channel)->teacher)->name ?? optional($course->channel)->name ?? 'Instructor';
    $lessonDate = optional($lesson->created_at)->format('M d, Y');
    $completedCount = $lessons->filter(fn ($item) => optional($item->progressForStudent)->is_completed)->count();
    $courseCompletion = $lessons->count() ? round(($completedCount / $lessons->count()) * 100) : 0;
    $restrictSeekingAhead = (bool) ($lesson->restrict_seeking ?? false);
    $resumeTime = (float) ($progress->current_time ?? 0);
    $maxWatchedTime = (float) ($progress->max_watched_time ?? 0);
@endphp

<div class="learn-shell">
    <main class="theater">
        <header class="topbar">
            <a href="{{ route('student.course.detail', $course->slug) }}" class="back-link" aria-label="Back to course">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <p class="course-kicker">Learning Room</p>
                <h1 class="course-title">{{ $course->title }}</h1>
            </div>
        </header>

        <section class="viewer-wrap">
            @if($viewType === 'video' && $videoUrl)
                <div
                    class="lms-player"
                    id="lmsPlayer"
                    data-progress-url="{{ route('student.lesson.progress', $lesson) }}"
                    data-resume-time="{{ $resumeTime }}"
                    data-max-watched-time="{{ $maxWatchedTime }}"
                    data-restrict-seeking="{{ $restrictSeekingAhead ? '1' : '0' }}"
                >
                    <video
                        id="lessonVideo"
                        playsinline
                        preload="metadata"
                        controlslist="nodownload noplaybackrate"
                        disablepictureinpicture
                        oncontextmenu="return false"
                    >
                        <source src="{{ $videoUrl }}" type="video/mp4" data-quality="720">
                        Your browser does not support HTML5 video.
                    </video>

                    <div class="player-shade"></div>

                    <div class="player-meta">
                        <div class="lesson-heading">
                            <h1>{{ $lesson->title }}</h1>
                            <p>Special Class</p>
                        </div>
                        <div class="teacher-stamp">
                            {{ $teacherName }}
                            <p>{{ $lessonDate }}</p>
                        </div>
                    </div>

                    <div class="center-controls">
                        <button type="button" class="control-btn" data-skip="-10" aria-label="Backward 10 seconds">
                            <i class="fa-solid fa-backward"></i>
                        </button>
                        <button type="button" class="control-btn play-toggle" aria-label="Play or pause">
                            <i class="fa-solid fa-play"></i>
                        </button>
                        <button type="button" class="control-btn" data-skip="10" aria-label="Forward 10 seconds">
                            <i class="fa-solid fa-forward"></i>
                        </button>
                    </div>

                    <div class="control-deck">
                        <div class="progress-rail" id="progressRail" aria-label="Video progress">
                            <span class="progress-buffer" id="progressBuffer"></span>
                            <span class="progress-fill" id="progressFill"></span>
                        </div>

                        <div class="control-row">
                            <div class="control-group">
                                <button type="button" class="control-btn icon-btn play-toggle" aria-label="Play or pause">
                                    <i class="fa-solid fa-play"></i>
                                </button>
                                <button type="button" class="control-btn icon-btn" data-skip="-10" aria-label="Backward 10 seconds">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                                <button type="button" class="control-btn icon-btn" data-skip="10" aria-label="Forward 10 seconds">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </button>
                                <div class="volume">
                                    <button type="button" class="control-btn icon-btn" id="muteButton" aria-label="Mute">
                                        <i class="fa-solid fa-volume-high"></i>
                                    </button>
                                    <input type="range" id="volumeSlider" min="0" max="1" step="0.05" value="1" aria-label="Volume">
                                </div>
                            </div>

                            <div class="timecode">
                                <span id="currentTime">0:00</span> / <span id="duration">0:00</span>
                            </div>

                            <div class="control-group">
                                <select id="speedSelect" class="player-select" aria-label="Playback speed">
                                    <option value="0.5">0.5x</option>
                                    <option value="0.75">0.75x</option>
                                    <option value="1" selected>1x</option>
                                    <option value="1.25">1.25x</option>
                                    <option value="1.3">1.30x</option>
                                    <option value="1.45">1.45x</option>
                                    <option value="1.5">1.5x</option>
                                    <option value="2">2x</option>
                                    <option value="custom">Custom...</option>
                                </select>
                                <select id="qualitySelect" class="player-select" aria-label="Video quality">
                                    <option value="720" selected>720p</option>
                                    <option value="480">480p</option>
                                    <option value="360">360p</option>
                                    <option value="240">240p</option>
                                    <option value="144">144p</option>
                                </select>
                                <button type="button" class="control-btn icon-btn" id="fullscreenButton" aria-label="Fullscreen">
                                    <i class="fa-solid fa-expand"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="player-status" id="playerStatus">Progress saved</div>
                </div>
            @elseif($viewType === 'clean_pdf' && $lesson->pdf_path)
                <div class="lms-player">
                    <iframe src="{{ Storage::url($lesson->pdf_path) }}#toolbar=1&navpanes=0&scrollbar=1" class="pdf-viewer"></iframe>
                </div>
            @elseif($viewType === 'annotated_pdf' && $lesson->annotated_pdf_path)
                <div class="lms-player">
                    <iframe src="{{ Storage::url($lesson->annotated_pdf_path) }}#toolbar=1&navpanes=0&scrollbar=1" class="pdf-viewer"></iframe>
                </div>
            @else
                <div class="lms-player">
                    <div class="video-fallback">
                        <div>
                            <i class="fa-solid fa-circle-exclamation" style="font-size: 42px; margin-bottom: 14px;"></i>
                            <p>Requested content is not available.</p>
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <section class="content-details">
            <h2>{{ $lesson->title }}</h2>
            <p>{{ $lesson->description ?: 'No description provided.' }}</p>
        </section>
    </main>

    <aside class="sidebar">
        <div class="sidebar-head">
            <h2>Course Content</h2>
            <div class="course-progress" style="--complete: {{ $courseCompletion }}%">
                <span></span>
            </div>
        </div>

        <div class="lesson-list">
            @foreach($lessons as $item)
                @php
                    $itemProgress = $item->progressForStudent;
                    $isLocked = !$isSubscribed && !$item->is_free && !$course->is_free;
                @endphp
                <div class="lesson-card {{ $lesson->id === $item->id ? 'active' : '' }}">
                    <h3>{{ $item->sort_order }}. {{ $item->title }}</h3>
                    <div class="lesson-meta">
                        <span>
                            @if($isLocked)
                                <i class="fa-solid fa-lock"></i> Premium
                            @elseif(optional($itemProgress)->is_completed)
                                <i class="fa-solid fa-circle-check" style="color: var(--success);"></i> Complete
                            @else
                                <i class="fa-solid fa-play"></i> {{ round(optional($itemProgress)->watched_percentage ?? 0) }}% watched
                            @endif
                        </span>
                    </div>
                    <div class="lesson-actions">
                        <a href="{{ route('student.learn', ['course' => $course->slug, 'lesson' => $item->id, 'view' => 'video']) }}"
                           class="pill {{ $lesson->id === $item->id && $viewType === 'video' ? 'active' : '' }} {{ $isLocked ? 'locked' : '' }}">
                            <i class="fa-solid fa-play"></i> Watch
                        </a>
                        @if($item->pdf_path)
                            <a href="{{ route('student.learn', ['course' => $course->slug, 'lesson' => $item->id, 'view' => 'clean_pdf']) }}"
                               class="pill {{ $lesson->id === $item->id && $viewType === 'clean_pdf' ? 'active' : '' }}">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </a>
                        @endif
                        @if($item->annotated_pdf_path)
                            <a href="{{ route('student.learn', ['course' => $course->slug, 'lesson' => $item->id, 'view' => 'annotated_pdf']) }}"
                               class="pill {{ $lesson->id === $item->id && $viewType === 'annotated_pdf' ? 'active' : '' }}">
                                <i class="fa-solid fa-pen-nib"></i> Notes
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </aside>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const shell = document.getElementById('lmsPlayer');
        const video = document.getElementById('lessonVideo');

        if (!shell || !video) return;

        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const progressUrl = shell.dataset.progressUrl;
        const progressFill = document.getElementById('progressFill');
        const progressBuffer = document.getElementById('progressBuffer');
        const progressRail = document.getElementById('progressRail');
        const currentTimeLabel = document.getElementById('currentTime');
        const durationLabel = document.getElementById('duration');
        const playButtons = document.querySelectorAll('.play-toggle');
        const muteButton = document.getElementById('muteButton');
        const volumeSlider = document.getElementById('volumeSlider');
        const speedSelect = document.getElementById('speedSelect');
        const qualitySelect = document.getElementById('qualitySelect');
        const fullscreenButton = document.getElementById('fullscreenButton');
        const status = document.getElementById('playerStatus');
        const restrictSeeking = shell.dataset.restrictSeeking === '1';
        let maxWatchedTime = Number(shell.dataset.maxWatchedTime || 0);
        let watchedSeconds = maxWatchedTime;
        let idleTimer = null;
        let saveTimer = null;
        let statusTimer = null;

        const formatTime = (seconds) => {
            const value = Number.isFinite(seconds) ? Math.max(0, seconds) : 0;
            const h = Math.floor(value / 3600);
            const m = Math.floor((value % 3600) / 60);
            const s = Math.floor(value % 60);
            return h > 0
                ? `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
                : `${m}:${String(s).padStart(2, '0')}`;
        };

        const showStatus = (message) => {
            status.textContent = message;
            status.classList.add('show');
            clearTimeout(statusTimer);
            statusTimer = setTimeout(() => status.classList.remove('show'), 1200);
        };

        const setIdleTimer = () => {
            shell.classList.remove('is-idle');
            clearTimeout(idleTimer);
            if (!video.paused) {
                idleTimer = setTimeout(() => shell.classList.add('is-idle'), 2200);
            }
        };

        const updatePlayIcons = () => {
            playButtons.forEach((button) => {
                button.innerHTML = video.paused
                    ? '<i class="fa-solid fa-play"></i>'
                    : '<i class="fa-solid fa-pause"></i>';
            });
            shell.classList.toggle('is-playing', !video.paused);
        };

        const updateVolumeIcon = () => {
            const icon = video.muted || video.volume === 0 ? 'fa-volume-xmark' : 'fa-volume-high';
            muteButton.innerHTML = `<i class="fa-solid ${icon}"></i>`;
        };

        const updateProgress = () => {
            const duration = video.duration || 0;
            const current = video.currentTime || 0;
            const percent = duration ? (current / duration) * 100 : 0;
            progressFill.style.width = `${Math.min(100, percent)}%`;
            currentTimeLabel.textContent = formatTime(current);
            durationLabel.textContent = formatTime(duration);

            if (current > maxWatchedTime) maxWatchedTime = current;
            if (current > watchedSeconds) watchedSeconds = current;

            if (video.buffered.length && duration) {
                const end = video.buffered.end(video.buffered.length - 1);
                progressBuffer.style.width = `${Math.min(100, (end / duration) * 100)}%`;
            }
        };

        const saveProgress = async (silent = true) => {
            if (!video.duration || !progressUrl) return;

            try {
                const response = await fetch(progressUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        current_time: video.currentTime,
                        duration: video.duration,
                        watched_seconds: watchedSeconds,
                        max_watched_time: maxWatchedTime,
                    }),
                });

                if (!response.ok) throw new Error('Save failed');

                const data = await response.json();
                if (!silent) showStatus(data.is_completed ? 'Lesson complete' : 'Progress saved');
            } catch (error) {
                if (!silent) showStatus('Could not save progress');
            }
        };

        const seekTo = (seconds) => {
            const target = Math.max(0, Math.min(video.duration || 0, seconds));
            if (restrictSeeking && target > maxWatchedTime + 3) {
                video.currentTime = maxWatchedTime;
                showStatus('Complete this part before skipping ahead');
                return;
            }
            video.currentTime = target;
        };

        playButtons.forEach((button) => {
            button.addEventListener('click', () => video.paused ? video.play() : video.pause());
        });

        document.querySelectorAll('[data-skip]').forEach((button) => {
            button.addEventListener('click', () => seekTo(video.currentTime + Number(button.dataset.skip)));
        });

        progressRail.addEventListener('click', (event) => {
            const rect = progressRail.getBoundingClientRect();
            const percent = (event.clientX - rect.left) / rect.width;
            seekTo(percent * (video.duration || 0));
        });

        muteButton.addEventListener('click', () => {
            video.muted = !video.muted;
            updateVolumeIcon();
        });

        volumeSlider.addEventListener('input', () => {
            video.volume = Number(volumeSlider.value);
            video.muted = video.volume === 0;
            updateVolumeIcon();
        });

        const applySpeed = (rate) => {
            const playbackRate = Math.max(0.25, Math.min(4, Number(rate)));
            if (!Number.isFinite(playbackRate)) return;

            video.playbackRate = playbackRate;

            const value = String(playbackRate);
            let option = Array.from(speedSelect.options).find((item) => Number(item.value) === playbackRate);

            if (!option) {
                option = new Option(`${playbackRate.toFixed(2).replace(/\.?0+$/, '')}x`, value);
                speedSelect.add(option, speedSelect.options[speedSelect.options.length - 1]);
            }

            speedSelect.value = option.value;
            showStatus(`${option.text.replace('x', '')}x speed`);
        };

        speedSelect.addEventListener('change', () => {
            if (speedSelect.value === 'custom') {
                const customRate = prompt('Enter playback speed between 0.25 and 4. Example: 1.30', video.playbackRate);
                applySpeed(customRate || video.playbackRate);
                return;
            }

            applySpeed(speedSelect.value);
        });

        qualitySelect.addEventListener('change', () => {
            const wasPaused = video.paused;
            const current = video.currentTime;
            video.currentTime = current;
            showStatus(`${qualitySelect.value}p selected`);
            if (!wasPaused) video.play();
        });

        fullscreenButton.addEventListener('click', async () => {
            if (!document.fullscreenElement) {
                await shell.requestFullscreen();
            } else {
                await document.exitFullscreen();
            }
        });

        document.addEventListener('fullscreenchange', () => {
            fullscreenButton.innerHTML = document.fullscreenElement
                ? '<i class="fa-solid fa-compress"></i>'
                : '<i class="fa-solid fa-expand"></i>';
        });

        shell.addEventListener('mousemove', setIdleTimer);
        shell.addEventListener('touchstart', setIdleTimer, { passive: true });
        video.addEventListener('contextmenu', (event) => event.preventDefault());
        video.addEventListener('play', () => {
            updatePlayIcons();
            setIdleTimer();
            saveTimer = setInterval(() => saveProgress(true), 5000);
        });
        video.addEventListener('pause', () => {
            updatePlayIcons();
            shell.classList.remove('is-idle');
            clearInterval(saveTimer);
            saveProgress(false);
        });
        video.addEventListener('ended', () => {
            watchedSeconds = video.duration || watchedSeconds;
            maxWatchedTime = video.duration || maxWatchedTime;
            clearInterval(saveTimer);
            saveProgress(false);
        });
        video.addEventListener('timeupdate', updateProgress);
        video.addEventListener('loadedmetadata', () => {
            const resumeTime = Number(shell.dataset.resumeTime || 0);
            if (resumeTime > 3 && resumeTime < video.duration - 3) {
                video.currentTime = resumeTime;
                showStatus(`Resumed at ${formatTime(resumeTime)}`);
            }
            updateProgress();
        });
        window.addEventListener('beforeunload', () => saveProgress(true));

        updatePlayIcons();
        updateVolumeIcon();
    });
</script>
</body>
</html>
