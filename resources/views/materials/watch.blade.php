<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $material->title }} - Video</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --accent: #8b5cf6;
            --dark-bg: #0f0f23;
            --dark-surface: #0a0a1a;
            --dark-card: #1a1a2e;
            --dark-border: #2a2a4a;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --soft-muted: #64748b;
            --gradient-primary: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);
            --player: #050505;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(ellipse at 20% 30%, rgba(99, 102, 241, 0.12), transparent 42%),
                radial-gradient(ellipse at 80% 10%, rgba(168, 85, 247, 0.1), transparent 40%),
                var(--dark-bg);
            min-height: 100vh;
        }

        button, select, input { font: inherit; }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 30;
            background: rgba(15, 15, 35, 0.9);
            border-bottom: 1px solid var(--dark-border);
            backdrop-filter: blur(16px);
        }

        .header-inner {
            max-width: 1440px;
            margin: 0 auto;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 0 28px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0;
            text-decoration: none;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand i {
            color: var(--primary);
            -webkit-text-fill-color: var(--primary);
            font-size: 29px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            color: var(--muted);
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-light);
            background: rgba(99, 102, 241, 0.15);
        }

        .user-chip {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
        }

        .page {
            max-width: 1440px;
            margin: 0 auto;
            padding: 26px 28px 56px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 22px;
            color: var(--text);
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
        }

        .back-link:hover { color: var(--primary-light); }

        .watch-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 486px;
            gap: 30px;
            align-items: start;
        }

        .video-card {
            border: 1px solid var(--dark-border);
            border-radius: 16px;
            overflow: hidden;
            background: var(--dark-card);
            box-shadow: 0 18px 55px rgba(0, 0, 0, 0.3);
        }

        .complete-row {
            height: 40px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            padding: 0 20px;
            color: var(--text);
            font-size: 15px;
            font-weight: 600;
        }

        .complete-row input {
            width: 20px;
            height: 20px;
            accent-color: var(--primary);
        }

        .player {
            position: relative;
            aspect-ratio: 16 / 9;
            background: var(--player);
            overflow: hidden;
        }

        .player video,
        .video-embed {
            width: 100%;
            height: 100%;
            display: block;
            background: #000;
        }

        .video-embed {
            border: 0;
        }

        .shade {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(180deg, rgba(0,0,0,0.22), transparent 40%, rgba(0,0,0,0.7));
            transition: opacity 0.22s ease;
        }

        .center-controls {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 58px;
            pointer-events: none;
            transition: opacity 0.22s ease;
        }

        .control-btn {
            border: 0;
            color: #fff;
            background: rgba(16, 16, 16, 0.68);
            cursor: pointer;
            transition: transform 0.18s ease, background 0.18s ease;
        }

        .control-btn:hover {
            background: rgba(99, 102, 241, 0.92);
            transform: translateY(-1px);
        }

        .center-controls .control-btn {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            font-size: 22px;
            pointer-events: auto;
        }

        .center-controls .play-toggle {
            width: 74px;
            height: 74px;
            font-size: 28px;
            background: rgba(255,255,255,0.26);
        }

        .control-deck {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 0 22px 12px;
            transition: opacity 0.22s ease, transform 0.22s ease;
        }

        .progress {
            height: 8px;
            background: rgba(255,255,255,0.36);
            cursor: pointer;
            overflow: hidden;
        }

        .progress span {
            display: block;
            width: 0;
            height: 100%;
            background: #2d7df6;
        }

        .control-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 10px;
        }

        .control-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .icon-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: transparent;
            font-size: 18px;
        }

        .volume {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .volume input {
            width: 104px;
            accent-color: #2d7df6;
        }

        .time {
            color: #fff;
            font-size: 19px;
            font-weight: 700;
            min-width: 160px;
            text-align: center;
        }

        .select {
            min-width: 72px;
            height: 34px;
            border: 0;
            color: #fff;
            background: transparent;
            font-size: 20px;
            font-weight: 700;
            outline: none;
        }

        .select option {
            color: #0f172a;
        }

        .player.is-playing.is-idle .shade,
        .player.is-playing.is-idle .center-controls,
        .player.is-playing.is-idle .control-deck {
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
        }

        .video-info {
            padding: 22px;
        }

        .video-info h1 {
            margin: 0 0 8px;
            color: var(--text);
            font-size: 24px;
            line-height: 1.25;
        }

        .video-info p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .playlist h2 {
            margin: 0 0 18px;
            color: var(--text);
            font-size: 26px;
        }

        .playlist-list {
            max-height: calc(100vh - 245px);
            overflow: auto;
            padding-right: 8px;
        }

        .playlist-item {
            display: grid;
            grid-template-columns: 190px minmax(0, 1fr);
            gap: 16px;
            padding: 10px;
            margin-bottom: 16px;
            color: var(--ink);
            text-decoration: none;
            border: 1px solid var(--dark-border);
            border-radius: 16px;
            background: var(--dark-card);
            color: var(--text);
            transition: border-color 0.2s ease, transform 0.2s ease, background 0.2s ease;
        }

        .playlist-item:hover {
            border-color: rgba(99, 102, 241, 0.55);
            background: #20203a;
            transform: translateY(-1px);
        }

        .playlist-item.active {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.16);
        }

        .thumb {
            aspect-ratio: 16 / 9;
            border-radius: 12px;
            background:
                linear-gradient(135deg, rgba(124, 92, 255, 0.8), rgba(14, 165, 233, 0.55)),
                #0f172a;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 34px;
            overflow: hidden;
        }

        .playlist-title {
            margin: 6px 0 10px;
            font-size: 20px;
            line-height: 1.35;
            font-weight: 800;
        }

        .playlist-meta {
            color: var(--muted);
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        @media (max-width: 1180px) {
            .watch-grid {
                grid-template-columns: 1fr;
            }

            .playlist-list {
                max-height: none;
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }

            .playlist-item {
                margin: 0;
                grid-template-columns: 150px minmax(0, 1fr);
            }
        }

        @media (max-width: 760px) {
            .header-inner { padding: 0 14px; }
            .brand { font-size: 21px; }
            .nav-link span { display: none; }
            .page { padding: 14px; }
            .back-link { font-size: 17px; margin-bottom: 16px; }
            .video-card { border-radius: 12px; }
            .complete-row { font-size: 15px; }
            .center-controls { gap: 18px; }
            .center-controls .control-btn { width: 44px; height: 44px; font-size: 18px; }
            .center-controls .play-toggle { width: 58px; height: 58px; }
            .control-deck { padding: 0 10px 8px; }
            .control-row { flex-wrap: wrap; }
            .time { min-width: auto; font-size: 14px; }
            .volume input { display: none; }
            .select { font-size: 15px; min-width: 56px; }
            .playlist-list { grid-template-columns: 1fr; }
            .playlist-item { grid-template-columns: 132px minmax(0, 1fr); }
            .playlist-title { font-size: 16px; }
        }
    </style>
</head>
<body>
@php
    $source = $material->file_path ? route('materials.stream', $material) : $material->playbackUrl();
    $extension = strtolower(pathinfo(parse_url((string) $source, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
    $mimeType = $extension === 'webm' ? 'video/webm' : 'video/mp4';
    $backUrl = auth()->user()->hasRole('teacher')
        ? route('teacher.materials')
        : ($course ? route('student.materials', $course) : route('student.dashboard'));
    $isEmbed = $source && (str_contains($source, 'youtube.com') || str_contains($source, 'youtu.be') || str_contains($source, 'vimeo.com'));
    $embedSource = $source;

    if ($isEmbed && str_contains($source, 'youtu.be')) {
        $embedSource = 'https://www.youtube.com/embed/'.trim(parse_url($source, PHP_URL_PATH), '/');
    } elseif ($isEmbed && str_contains($source, 'youtube.com')) {
        parse_str(parse_url($source, PHP_URL_QUERY) ?? '', $query);
        $embedSource = isset($query['v']) ? 'https://www.youtube.com/embed/'.$query['v'] : $source;
    } elseif ($isEmbed && str_contains($source, 'vimeo.com')) {
        $embedSource = 'https://player.vimeo.com/video/'.trim(parse_url($source, PHP_URL_PATH), '/');
    }
@endphp

<header class="site-header">
    <div class="header-inner">
        <a href="{{ route('home') }}" class="brand"><i class="fas fa-graduation-cap"></i> EduVerse</a>
        <div class="nav-actions">
            @if(auth()->user()->hasRole('teacher'))
                <a href="{{ route('teacher.dashboard') }}" class="nav-link"><i class="fas fa-chalkboard-teacher"></i> <span>Dashboard</span></a>
            @elseif(auth()->user()->hasRole('student'))
                <a href="{{ route('student.dashboard') }}" class="nav-link"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                <a href="{{ route('student.my-courses') }}" class="nav-link"><i class="fas fa-book"></i> <span>My Courses</span></a>
            @endif
            <a href="{{ route('community.index') }}" class="nav-link"><i class="fas fa-comments"></i> <span>Community</span></a>
            <div class="user-chip">{{ substr(auth()->user()->name, 0, 1) }}</div>
        </div>
    </div>
</header>

<main class="page">
    <a href="{{ $backUrl }}" class="back-link"><i class="fa-solid fa-chevron-left"></i> Back to Content</a>

    <div class="watch-grid">
        <section>
            <article class="video-card">
                <div class="complete-row">
                    <input type="checkbox" id="markComplete">
                    <label for="markComplete">Mark as complete</label>
                </div>

                <div class="player" id="materialPlayer">
                    @if($isEmbed)
                        <iframe class="video-embed" src="{{ $embedSource }}" allowfullscreen allow="autoplay; fullscreen; picture-in-picture"></iframe>
                    @else
                        <video id="video" playsinline preload="metadata" controlslist="nodownload noplaybackrate" disablepictureinpicture oncontextmenu="return false">
                            <source src="{{ $source }}" type="{{ $mimeType }}">
                            Your browser does not support this video.
                        </video>

                        <div class="shade"></div>
                        <div class="center-controls">
                            <button class="control-btn" type="button" data-skip="-10" aria-label="Back 10 seconds">
                                <i class="fa-solid fa-backward"></i>
                            </button>
                            <button class="control-btn play-toggle" type="button" aria-label="Play">
                                <i class="fa-solid fa-play"></i>
                            </button>
                            <button class="control-btn" type="button" data-skip="10" aria-label="Forward 10 seconds">
                                <i class="fa-solid fa-forward"></i>
                            </button>
                        </div>

                        <div class="control-deck">
                            <div class="progress" id="progress"><span id="progressFill"></span></div>
                            <div class="control-row">
                                <div class="control-group">
                                    <button class="control-btn icon-btn play-toggle" type="button" aria-label="Play"><i class="fa-solid fa-play"></i></button>
                                    <button class="control-btn icon-btn" type="button" data-skip="-10" aria-label="Back 10 seconds"><i class="fa-solid fa-rotate-left"></i></button>
                                    <button class="control-btn icon-btn" type="button" data-skip="10" aria-label="Forward 10 seconds"><i class="fa-solid fa-rotate-right"></i></button>
                                    <div class="volume">
                                        <button class="control-btn icon-btn" type="button" id="mute"><i class="fa-solid fa-volume-high"></i></button>
                                        <input type="range" id="volume" min="0" max="1" step="0.05" value="1">
                                    </div>
                                </div>
                                <div class="time"><span id="current">0:00</span>/<span id="duration">0:00</span></div>
                                <div class="control-group">
                                    <select id="speed" class="select" aria-label="Playback speed">
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
                                    <button class="control-btn icon-btn" type="button" id="fullscreen" aria-label="Fullscreen"><i class="fa-solid fa-expand"></i></button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="video-info">
                    <h1>{{ $material->title }}</h1>
                    <p>{{ $material->description ?: $teacherName.' · '.$material->subject?->name }}</p>
                </div>
            </article>
        </section>

        <aside class="playlist">
            <h2>Videos</h2>
            <div class="playlist-list">
                @foreach($playlist as $item)
                    <a href="{{ route('materials.watch', $item) }}" class="playlist-item {{ $item->id === $material->id ? 'active' : '' }}">
                        <div class="thumb"><i class="fa-solid fa-play"></i></div>
                        <div>
                            <div class="playlist-title">{{ $item->title }}</div>
                            <div class="playlist-meta">
                                <i class="fa-regular fa-clock"></i>
                                {{ $item->file_size ? number_format($item->file_size / 1024, 1).' MB' : 'Video lesson' }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </aside>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const player = document.getElementById('materialPlayer');
        const video = document.getElementById('video');
        if (!player || !video) return;

        const playButtons = player.querySelectorAll('.play-toggle');
        const skipButtons = player.querySelectorAll('[data-skip]');
        const progress = document.getElementById('progress');
        const progressFill = document.getElementById('progressFill');
        const current = document.getElementById('current');
        const duration = document.getElementById('duration');
        const mute = document.getElementById('mute');
        const volume = document.getElementById('volume');
        const speed = document.getElementById('speed');
        const fullscreen = document.getElementById('fullscreen');
        let idleTimer = null;

        const hasDuration = () => Number.isFinite(video.duration) && video.duration > 0;

        const format = (seconds) => {
            const value = Number.isFinite(seconds) ? Math.max(0, seconds) : 0;
            const h = Math.floor(value / 3600);
            const m = Math.floor((value % 3600) / 60);
            const s = Math.floor(value % 60);
            return h > 0 ? `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}` : `${m}:${String(s).padStart(2, '0')}`;
        };

        const updatePlay = () => {
            playButtons.forEach((button) => {
                button.innerHTML = video.paused ? '<i class="fa-solid fa-play"></i>' : '<i class="fa-solid fa-pause"></i>';
            });
            player.classList.toggle('is-playing', !video.paused);
        };

        const wake = () => {
            player.classList.remove('is-idle');
            clearTimeout(idleTimer);
            if (!video.paused) {
                idleTimer = setTimeout(() => player.classList.add('is-idle'), 2200);
            }
        };

        const update = () => {
            const percent = hasDuration() ? (video.currentTime / video.duration) * 100 : 0;
            progressFill.style.width = `${Math.min(100, percent)}%`;
            current.textContent = format(video.currentTime);
            duration.textContent = format(video.duration);
        };

        const seekTo = (seconds) => {
            if (!hasDuration()) return;

            const target = Math.max(0, Math.min(video.duration, Number(seconds) || 0));

            video.currentTime = target;

            update();
            wake();
        };

        playButtons.forEach((button) => button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            video.paused ? video.play() : video.pause();
        }));

        skipButtons.forEach((button) => button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            seekTo(video.currentTime + Number(button.dataset.skip));
        }));

        progress.addEventListener('pointerdown', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const rect = progress.getBoundingClientRect();
            const ratio = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
            seekTo(ratio * video.duration);
        });

        mute.addEventListener('click', () => {
            video.muted = !video.muted;
            mute.innerHTML = video.muted ? '<i class="fa-solid fa-volume-xmark"></i>' : '<i class="fa-solid fa-volume-high"></i>';
        });

        volume.addEventListener('input', () => {
            video.volume = Number(volume.value);
            video.muted = video.volume === 0;
        });

        const applySpeed = (rate) => {
            const playbackRate = Math.max(0.25, Math.min(4, Number(rate)));
            if (!Number.isFinite(playbackRate)) return;

            video.playbackRate = playbackRate;

            const value = String(playbackRate);
            let option = Array.from(speed.options).find((item) => Number(item.value) === playbackRate);

            if (!option) {
                option = new Option(`${playbackRate.toFixed(2).replace(/\.?0+$/, '')}x`, value);
                speed.add(option, speed.options[speed.options.length - 1]);
            }

            speed.value = option.value;
        };

        speed.addEventListener('change', () => {
            if (speed.value === 'custom') {
                const customRate = prompt('Enter playback speed between 0.25 and 4. Example: 1.30', video.playbackRate);
                applySpeed(customRate || video.playbackRate);
                return;
            }

            applySpeed(speed.value);
        });

        fullscreen.addEventListener('click', async () => {
            if (!document.fullscreenElement) {
                await player.requestFullscreen();
            } else {
                await document.exitFullscreen();
            }
        });

        document.addEventListener('fullscreenchange', () => {
            fullscreen.innerHTML = document.fullscreenElement ? '<i class="fa-solid fa-compress"></i>' : '<i class="fa-solid fa-expand"></i>';
        });

        video.addEventListener('play', () => { updatePlay(); wake(); });
        video.addEventListener('pause', () => { updatePlay(); player.classList.remove('is-idle'); });
        video.addEventListener('timeupdate', update);
        video.addEventListener('loadedmetadata', update);
        video.addEventListener('durationchange', update);
        video.addEventListener('seeked', update);
        video.addEventListener('seeking', update);
        video.addEventListener('contextmenu', (event) => event.preventDefault());
        player.addEventListener('mousemove', wake);
        player.addEventListener('touchstart', wake, { passive: true });

        updatePlay();
    });
</script>
</body>
</html>
