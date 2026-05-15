<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} - Learning Room</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
        :root {
            --primary: #8b5cf6;
            --primary-hover: #7c3aed;
            --bg-dark: #0f111a;
            --surface: #1e1e2e;
            --surface-hover: #2a2a3c;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Navbar / Header */
        .header {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 60px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            z-index: 100;
        }

        .header .back-btn {
            color: var(--text-muted);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .header .back-btn:hover { color: var(--primary); }

        .header-title {
            margin-left: 24px;
            font-weight: 600;
            border-left: 1px solid var(--border);
            padding-left: 24px;
        }

        /* Main Layout */
        .layout {
            display: flex;
            width: 100%;
            margin-top: 60px;
            height: calc(100vh - 60px);
        }

        .main-content {
            flex: 1;
            background: #000;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .sidebar {
            width: 350px;
            background: var(--surface);
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Playlist Items */
        .playlist-item {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            transition: background 0.2s;
            display: block;
            text-decoration: none;
            color: var(--text-main);
        }

        .playlist-item:hover {
            background: var(--surface-hover);
        }

        .playlist-item.active {
            background: rgba(139, 92, 246, 0.1);
            border-left: 3px solid var(--primary);
        }

        .item-title {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .item-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .item-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); }
        
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text-main); }
        .btn-outline:hover { background: var(--surface-hover); border-color: var(--text-muted); }

        .btn-outline.active {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(139, 92, 246, 0.05);
        }

        /* Viewer Area */
        .viewer-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .plyr {
            width: 100%;
            height: 100%;
        }

        iframe.pdf-viewer {
            width: 100%;
            height: 100%;
            border: none;
            background: #fff;
        }

        .content-details {
            padding: 24px;
            background: var(--surface);
            border-top: 1px solid var(--border);
        }

        .content-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 8px 0;
        }

        .content-desc {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>

    <header class="header">
        <a href="{{ route('student.course.detail', $course->slug) }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Course
        </a>
        <div class="header-title">{{ $course->title }}</div>
    </header>

    <div class="layout">
        <!-- Main Content Area -->
        <main class="main-content">
            <div class="viewer-container">
                @php
                    $viewType = request('view', 'video'); // video, clean_pdf, annotated_pdf
                @endphp

                @if($viewType === 'video')
                    @if($lesson->video_path)
                        <video id="player" playsinline controls>
                            <!-- Mocking multi-resolution for UI -->
                            <source src="{{ Storage::url($lesson->video_path) }}" type="video/mp4" size="1080" />
                            <source src="{{ Storage::url($lesson->video_path) }}" type="video/mp4" size="720" />
                            <source src="{{ Storage::url($lesson->video_path) }}" type="video/mp4" size="480" />
                        </video>
                    @elseif($lesson->video_url)
                        <div class="plyr__video-embed" id="player">
                            <iframe src="{{ $lesson->video_url }}" allowfullscreen allowtransparency allow="autoplay"></iframe>
                        </div>
                    @else
                        <div style="color: var(--text-muted); text-align: center;">
                            <i class="fas fa-video-slash" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.5;"></i>
                            <p>No video available for this lesson.</p>
                        </div>
                    @endif
                @elseif($viewType === 'clean_pdf' && $lesson->pdf_path)
                    <iframe src="{{ Storage::url($lesson->pdf_path) }}#toolbar=1&navpanes=0&scrollbar=1" class="pdf-viewer"></iframe>
                @elseif($viewType === 'annotated_pdf' && $lesson->annotated_pdf_path)
                    <iframe src="{{ Storage::url($lesson->annotated_pdf_path) }}#toolbar=1&navpanes=0&scrollbar=1" class="pdf-viewer"></iframe>
                @else
                    <div style="color: var(--text-muted); text-align: center;">
                        <i class="fas fa-exclamation-circle" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.5;"></i>
                        <p>Requested content is not available.</p>
                    </div>
                @endif
            </div>
            
            <div class="content-details">
                <h1 class="content-title">{{ $lesson->title }}</h1>
                <p class="content-desc">{{ $lesson->description ?: 'No description provided.' }}</p>
            </div>
        </main>

        <!-- Sidebar Playlist -->
        <aside class="sidebar">
            <div class="sidebar-header">
                Course Content
            </div>
            <div class="playlist">
                @foreach($lessons as $item)
                    <div class="playlist-item {{ $lesson->id === $item->id ? 'active' : '' }}">
                        <div class="item-title">{{ $item->sort_order }}. {{ $item->title }}</div>
                        <div class="item-meta">
                            @if(!$isSubscribed && !$item->is_free && !$course->is_free)
                                <span style="color: #ef4444;"><i class="fas fa-lock"></i> Premium</span>
                            @else
                                <span style="color: #10b981;"><i class="fas fa-unlock"></i> Available</span>
                            @endif
                        </div>
                        
                        <div class="item-actions">
                            <a href="{{ route('student.learn', ['course' => $course->slug, 'lesson' => $item->id, 'view' => 'video']) }}" 
                               class="btn-action {{ $lesson->id === $item->id && $viewType === 'video' ? 'btn-primary' : 'btn-outline' }}">
                                <i class="fas fa-play"></i> Watch
                            </a>
                            
                            @if($item->pdf_path)
                            <a href="{{ route('student.learn', ['course' => $course->slug, 'lesson' => $item->id, 'view' => 'clean_pdf']) }}" 
                               class="btn-action {{ $lesson->id === $item->id && $viewType === 'clean_pdf' ? 'btn-outline active' : 'btn-outline' }}">
                                <i class="fas fa-file-pdf"></i> Without Annotation
                            </a>
                            @endif
                            
                            @if($item->annotated_pdf_path)
                            <a href="{{ route('student.learn', ['course' => $course->slug, 'lesson' => $item->id, 'view' => 'annotated_pdf']) }}" 
                               class="btn-action {{ $lesson->id === $item->id && $viewType === 'annotated_pdf' ? 'btn-outline active' : 'btn-outline' }}">
                                <i class="fas fa-file-signature"></i> With Annotation
                            </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>
    </div>

    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('player')) {
                const player = new Plyr('#player', {
                    settings: ['quality', 'speed', 'loop'],
                    speed: { selected: 1, options: [0.5, 0.75, 1, 1.25, 1.5, 2] },
                    quality: { default: 1080, options: [1080, 720, 480], forced: true, onChange: (e) => console.log('Quality changed') }
                });
            }
        });
    </script>
</body>
</html>
