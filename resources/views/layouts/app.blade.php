<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EduVerse LMS - Premium Online Learning Platform">
    <title>@yield('title', 'EduVerse LMS - Learn. Test. Succeed.')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #6366f1; --primary-dark: #4f46e5; --primary-light: #818cf8;
            --accent: #f59e0b; --accent-light: #fbbf24;
            --success: #10b981; --danger: #ef4444; --warning: #f59e0b; --info: #3b82f6;
            --dark-bg: #0f0f23; --dark-card: #1a1a2e; --dark-card-hover: #16213e;
            --dark-surface: #0a0a1a; --dark-border: #2a2a4a;
            --text-primary: #e2e8f0; --text-secondary: #94a3b8; --text-muted: #64748b;
            --gradient-primary: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);
            --gradient-accent: linear-gradient(135deg, #f59e0b, #ef4444);
            --gradient-success: linear-gradient(135deg, #10b981, #06b6d4);
            --glass-bg: rgba(26, 26, 46, 0.8);
            --glass-border: rgba(99, 102, 241, 0.2);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--dark-bg); color: var(--text-primary); min-height: 100vh; overflow-x: hidden; }
        .bg-mesh { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(ellipse at 20% 50%, rgba(99,102,241,0.08) 0%, transparent 50%), radial-gradient(ellipse at 80% 20%, rgba(168,85,247,0.06) 0%, transparent 50%), radial-gradient(ellipse at 50% 80%, rgba(59,130,246,0.05) 0%, transparent 50%); z-index: 0; pointer-events: none; }
        .navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(15, 15, 35, 0.85); backdrop-filter: blur(20px); border-bottom: 1px solid var(--dark-border); padding: 0 2rem; height: 70px; display: flex; align-items: center; justify-content: space-between; }
        .navbar-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; font-size: 1.5rem; font-weight: 800; background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .navbar-brand i { -webkit-text-fill-color: #6366f1; font-size: 1.8rem; }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-link { color: var(--text-secondary); text-decoration: none; padding: 8px 16px; border-radius: 8px; font-size: 0.9rem; font-weight: 500; transition: all 0.3s ease; }
        .nav-link:hover { color: var(--text-primary); background: rgba(99,102,241,0.1); }
        .nav-link.active { color: var(--primary-light); background: rgba(99,102,241,0.15); }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; border-radius: 10px; font-size: 0.9rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s ease; font-family: 'Inter', sans-serif; }
        .btn-primary { background: var(--gradient-primary); color: white; box-shadow: 0 4px 15px rgba(99,102,241,0.3); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 25px rgba(99,102,241,0.4); }
        .btn-secondary { background: transparent; color: var(--text-primary); border: 1px solid var(--dark-border); }
        .btn-secondary:hover { border-color: var(--primary); background: rgba(99,102,241,0.1); }
        .btn-success { background: var(--gradient-success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-sm { padding: 6px 14px; font-size: 0.8rem; border-radius: 8px; }
        .btn-lg { padding: 14px 32px; font-size: 1rem; border-radius: 12px; }
        .btn-block { width: 100%; justify-content: center; }
        .card { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 16px; padding: 24px; transition: all 0.3s ease; }
        .card:hover { border-color: var(--glass-border); transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
        .page-wrapper { position: relative; z-index: 1; padding-top: 70px; min-height: 100vh; }
        .container { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }
        .page-content { padding: 2rem 0; }
        .stat-card { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 16px; padding: 24px; display: flex; align-items: center; gap: 16px; transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.3); }
        .stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: white; }
        .stat-value { font-size: 1.8rem; font-weight: 800; line-height: 1; }
        .stat-label { font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .course-card { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 16px; overflow: hidden; transition: all 0.3s ease; }
        .course-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.4); border-color: var(--glass-border); }
        .course-thumb { width: 100%; height: 180px; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: rgba(255,255,255,0.3); position: relative; overflow: hidden; }
        .course-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .course-body { padding: 20px; }
        .course-title { font-size: 1.05rem; font-weight: 700; margin-bottom: 8px; line-height: 1.4; }
        .course-title a { color: var(--text-primary); text-decoration: none; }
        .course-title a:hover { color: var(--primary-light); }
        .course-meta { display: flex; align-items: center; gap: 12px; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 12px; flex-wrap: wrap; }
        .course-price { font-size: 1.3rem; font-weight: 800; background: var(--gradient-accent); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .course-price.free { -webkit-text-fill-color: var(--success); }
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
        .badge-primary { background: rgba(99,102,241,0.2); color: var(--primary-light); }
        .badge-success { background: rgba(16,185,129,0.2); color: var(--success); }
        .badge-warning { background: rgba(245,158,11,0.2); color: var(--accent); }
        .badge-danger { background: rgba(239,68,68,0.2); color: var(--danger); }
        .badge-info { background: rgba(59,130,246,0.2); color: var(--info); }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 12px 16px; background: var(--dark-surface); border: 1px solid var(--dark-border); border-radius: 10px; color: var(--text-primary); font-family: 'Inter', sans-serif; font-size: 0.9rem; transition: all 0.3s ease; }
        .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
        .form-textarea { min-height: 120px; resize: vertical; }
        .form-select option { background: var(--dark-surface); color: var(--text-primary); }
        .table-container { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 16px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 14px 20px; text-align: left; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); background: var(--dark-surface); border-bottom: 1px solid var(--dark-border); }
        td { padding: 14px 20px; border-bottom: 1px solid rgba(42,42,74,0.5); font-size: 0.9rem; }
        tr:hover td { background: rgba(99,102,241,0.03); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .page-title { font-size: 1.8rem; font-weight: 800; background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .page-subtitle { color: var(--text-secondary); margin-top: 4px; font-size: 0.95rem; }
        .dashboard-layout { display: grid; grid-template-columns: 260px 1fr; min-height: calc(100vh - 70px); }
        .sidebar { background: var(--dark-surface); border-right: 1px solid var(--dark-border); padding: 24px 16px; position: sticky; top: 70px; height: calc(100vh - 70px); overflow-y: auto; }
        .sidebar-section { margin-bottom: 24px; }
        .sidebar-title { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); padding: 0 12px; margin-bottom: 8px; letter-spacing: 1px; }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s ease; margin-bottom: 2px; }
        .sidebar-link:hover { color: var(--text-primary); background: rgba(99,102,241,0.1); }
        .sidebar-link.active { color: var(--primary-light); background: rgba(99,102,241,0.15); }
        .sidebar-link i { width: 20px; text-align: center; }
        .main-content { padding: 32px; }
        .alert { padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: rgba(16,185,129,0.15); color: var(--success); border: 1px solid rgba(16,185,129,0.3); }
        .alert-error { background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); z-index: 2000; justify-content: center; align-items: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 20px; padding: 32px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; }
        .modal-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 20px; }
        .progress-bar { width: 100%; height: 8px; background: var(--dark-surface); border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 4px; background: var(--gradient-primary); transition: width 0.5s ease; }
        .pagination { display: flex; justify-content: center; gap: 6px; margin-top: 32px; list-style: none; padding: 0; }
        .pagination a, .pagination span { padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; color: var(--text-secondary); border: 1px solid var(--dark-border); }
        .pagination a:hover { border-color: var(--primary); color: var(--primary-light); }
        .pagination .active span { background: var(--primary); color: white; border-color: var(--primary); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: fadeInUp 0.5s ease forwards; }
        .delay-1 { animation-delay: 0.1s; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; opacity: 0; }
        .delay-3 { animation-delay: 0.3s; opacity: 0; }
        .avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; color: white; flex-shrink: 0; }
        .post-card { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 16px; padding: 24px; margin-bottom: 16px; transition: all 0.3s ease; }
        .post-card:hover { border-color: var(--glass-border); }
        .post-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .post-author { font-weight: 600; font-size: 0.95rem; }
        .post-time { font-size: 0.8rem; color: var(--text-muted); }
        .post-title-link { font-size: 1.15rem; font-weight: 700; color: var(--text-primary); text-decoration: none; display: block; margin-bottom: 8px; }
        .post-title-link:hover { color: var(--primary-light); }
        .post-body { color: var(--text-secondary); line-height: 1.6; margin-bottom: 16px; }
        .post-actions { display: flex; gap: 16px; }
        .post-action { display: flex; align-items: center; gap: 6px; color: var(--text-muted); font-size: 0.85rem; background: none; border: none; cursor: pointer; transition: all 0.3s ease; text-decoration: none; font-family: 'Inter', sans-serif; }
        .post-action:hover { color: var(--primary-light); }
        .subject-chip { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 20px; background: var(--dark-card); border: 1px solid var(--dark-border); color: var(--text-secondary); font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.3s ease; }
        .subject-chip:hover { border-color: var(--primary); color: var(--primary-light); background: rgba(99,102,241,0.1); }
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
        .empty-state i { font-size: 4rem; margin-bottom: 16px; display: block; opacity: 0.3; }
        .empty-state h3 { font-size: 1.3rem; margin-bottom: 8px; color: var(--text-secondary); }
        .rank-1 { color: #ffd700; } .rank-2 { color: #c0c0c0; } .rank-3 { color: #cd7f32; }
        .section-locked { opacity: 0.5; }
        @media (max-width: 1024px) { .grid-4 { grid-template-columns: repeat(2, 1fr); } .grid-3 { grid-template-columns: repeat(2, 1fr); } .dashboard-layout { grid-template-columns: 1fr; } .sidebar { display: none; } }
        @media (max-width: 768px) { .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; } .navbar { padding: 0 1rem; } .nav-links { gap: 4px; } .page-header { flex-direction: column; gap: 16px; align-items: flex-start; } .main-content { padding: 16px; } }
        ::-webkit-scrollbar { width: 8px; } ::-webkit-scrollbar-track { background: var(--dark-bg); } ::-webkit-scrollbar-thumb { background: var(--dark-border); border-radius: 4px; } ::-webkit-scrollbar-thumb:hover { background: var(--primary); }
        .hero-section { padding: 80px 0 60px; text-align: center; position: relative; }
        .hero-title { font-size: 3.5rem; font-weight: 900; line-height: 1.1; margin-bottom: 20px; background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero-subtitle { font-size: 1.2rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto 32px; line-height: 1.6; }
        .hero-actions { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .section-title { font-size: 1.6rem; font-weight: 800; margin-bottom: 8px; }
        .section-subtitle { color: var(--text-secondary); margin-bottom: 32px; }
        .section-block { padding: 48px 0; }
        .checkbox-wrap { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
        .checkbox-wrap input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--primary); }
    </style>
    @stack('styles')
</head>
<body>
    <div class="bg-mesh"></div>
    <nav class="navbar">
        <a href="{{ route('home') }}" class="navbar-brand"><i class="fas fa-graduation-cap"></i> EduVerse</a>
        <div class="nav-links">
            @guest
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"><i class="fas fa-home"></i> Home</a>
                <a href="{{ route('community.index') }}" class="nav-link"><i class="fas fa-users"></i> Community</a>
                <a href="{{ route('login') }}" class="nav-link"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm"><i class="fas fa-rocket"></i> Get Started</a>
            @else
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"><i class="fas fa-shield-alt"></i> Admin</a>
                @elseif(auth()->user()->hasRole('teacher'))
                    <a href="{{ route('teacher.dashboard') }}" class="nav-link {{ request()->routeIs('teacher.*') ? 'active' : '' }}"><i class="fas fa-chalkboard-teacher"></i> Dashboard</a>
                @else
                    <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="{{ route('student.browse') }}" class="nav-link {{ request()->routeIs('student.browse') ? 'active' : '' }}"><i class="fas fa-compass"></i> Browse</a>
                    <a href="{{ route('student.my-courses') }}" class="nav-link {{ request()->routeIs('student.my-courses') ? 'active' : '' }}"><i class="fas fa-book"></i> My Courses</a>
                @endif
                <a href="{{ route('community.index') }}" class="nav-link {{ request()->routeIs('community.*') ? 'active' : '' }}"><i class="fas fa-comments"></i> Community</a>
                <div style="display:flex;align-items:center;gap:10px;margin-left:8px;">
                    <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <form method="POST" action="{{ route('logout') }}"><@csrf
                        <button type="submit" class="nav-link" style="background:none;border:none;cursor:pointer;font-family:Inter,sans-serif;"><i class="fas fa-sign-out-alt"></i></button>
                    </form>
                </div>
            @endguest
        </div>
    </nav>
    <div class="page-wrapper">
        @if(session('success'))
            <div class="container" style="padding-top:16px;"><div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div></div>
        @endif
        @if(session('error'))
            <div class="container" style="padding-top:16px;"><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div></div>
        @endif
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
