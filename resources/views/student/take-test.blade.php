@extends('layouts.app')

@section('content')
<div class="test-interface" id="test-app">
    <!-- Top Header: Title and Timer -->
    <header class="test-header">
        <div class="test-info">
            <h2 class="test-title">{{ $section->title }}</h2>
            <div class="test-meta">{{ $section->testSeries->title }}</div>
        </div>
        <div class="test-timer-wrapper">
            <div class="timer-icon"><i class="fas fa-clock"></i></div>
            <div id="timer" class="timer-value">00:00:00</div>
        </div>
        <div class="test-actions-top">
            <button class="btn btn-danger btn-sm" onclick="confirmSubmit()">Submit Test</button>
        </div>
    </header>

    <div class="test-body">
        <!-- Main Question Area -->
        <main class="question-container">
            <div class="question-header">
                <span class="question-number">Question <span id="current-q-num">1</span></span>
                <div class="question-marks">
                    <span class="mark-pos">+{{ $questions[0]->marks }}</span>
                    <span class="mark-neg">-{{ $questions[0]->negative_marks }}</span>
                </div>
            </div>

            <div id="question-content" class="question-content">
                <!-- Question text and options will be injected here via JS -->
                <div class="animate-pulse">Loading question...</div>
            </div>

            <footer class="question-footer">
                <div class="footer-left">
                    <button class="btn btn-outline" onclick="markForReview()">Mark for Review & Next</button>
                    <button class="btn btn-outline" onclick="clearResponse()">Clear Response</button>
                </div>
                <div class="footer-right">
                    <button class="btn btn-secondary" onclick="prevQuestion()">Previous</button>
                    <button class="btn btn-primary" id="next-btn" onclick="nextQuestion()">Save & Next</button>
                </div>
            </footer>
        </main>

        <!-- Right Sidebar: Question Palette -->
        <aside class="test-sidebar">
            <div class="user-profile-test">
                <div class="avatar-sm">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div class="user-name-test">{{ auth()->user()->name }}</div>
            </div>

            <div class="palette-stats">
                <div class="stat-item"><span class="badge-status answered">0</span> Answered</div>
                <div class="stat-item"><span class="badge-status marked">0</span> Marked</div>
                <div class="stat-item"><span class="badge-status current">0</span> Current</div>
                <div class="stat-item"><span class="badge-status not-visited">0</span> Not Visited</div>
            </div>

            <div class="palette-grid-container">
                <div class="palette-title">Question Palette</div>
                <div class="palette-grid" id="palette-grid">
                    @foreach($questions as $index => $q)
                        <button class="palette-btn not-visited" id="p-btn-{{ $index }}" onclick="jumpToQuestion({{ $index }})">{{ $index + 1 }}</button>
                    @endforeach
                </div>
            </div>

            <div class="sidebar-footer">
                <button class="btn btn-primary btn-block" onclick="confirmSubmit()">Submit Test</button>
            </div>
        </aside>
    </div>
</div>

<!-- Submit Form -->
<form id="submit-test-form" action="{{ route('student.submit-test', $attempt->id) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="responses" id="responses-input">
    <input type="hidden" name="time_spent" id="time-spent-input">
</form>

<style>
    .test-interface {
        height: 100vh;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }

    .test-header {
        height: 60px;
        background: #1e293b;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .test-title { margin: 0; font-size: 1.1rem; font-weight: 600; }
    .test-meta { font-size: 0.8rem; opacity: 0.7; }

    .test-timer-wrapper {
        display: flex;
        align-items: center;
        background: rgba(255,255,255,0.1);
        padding: 5px 15px;
        border-radius: 4px;
        gap: 10px;
    }
    .timer-value { font-family: monospace; font-size: 1.2rem; font-weight: bold; }

    .test-body {
        flex: 1;
        display: flex;
        overflow: hidden;
    }

    .question-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 30px;
        overflow-y: auto;
    }

    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 25px;
    }
    .question-number { font-size: 1.2rem; font-weight: bold; color: #1e293b; }
    .question-marks { display: flex; gap: 15px; font-size: 0.9rem; }
    .mark-pos { color: #10b981; font-weight: bold; }
    .mark-neg { color: #ef4444; font-weight: bold; }

    .question-content { flex: 1; font-size: 1.1rem; line-height: 1.6; color: #334155; }

    .option-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-top: 15px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .option-item:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .option-item.selected { background: #eff6ff; border-color: #3b82f6; }
    .option-item input { margin-right: 15px; }

    .question-footer {
        display: flex;
        justify-content: space-between;
        padding-top: 25px;
        margin-top: 40px;
        border-top: 1px solid #e2e8f0;
    }
    .footer-left, .footer-right { display: flex; gap: 10px; }

    .test-sidebar {
        width: 320px;
        background: white;
        border-left: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        padding: 20px;
    }

    .user-profile-test {
        display: flex;
        align-items: center;
        gap: 15px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .avatar-sm { width: 40px; height: 40px; background: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }

    .palette-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin: 20px 0;
    }
    .stat-item { font-size: 0.85rem; display: flex; align-items: center; gap: 8px; }
    .badge-status { width: 24px; height: 24px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; color: white; }
    .answered { background: #10b981; }
    .marked { background: #8b5cf6; }
    .current { background: #3b82f6; }
    .not-visited { background: #cbd5e1; color: #475569; }

    .palette-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-top: 15px;
        max-height: 300px;
        overflow-y: auto;
        padding-right: 5px;
    }
    .palette-btn {
        aspect-ratio: 1;
        border: none;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .sidebar-footer { margin-top: auto; padding-top: 20px; }

    /* NAT styles */
    .nat-input { width: 100%; max-width: 200px; padding: 12px; border: 2px solid #3b82f6; border-radius: 6px; font-size: 1.2rem; }
</style>

<script>
    const questions = @json($questions);
    const totalTime = {{ $section->duration_minutes * 60 }};
    let timeRemaining = totalTime;
    let currentIndex = 0;
    let responses = {}; // { q_id: { type: 'mcq', value: opt_id } }

    function initTest() {
        renderQuestion(0);
        startTimer();
    }

    function renderQuestion(index) {
        currentIndex = index;
        const q = questions[index];
        document.getElementById('current-q-num').innerText = index + 1;
        
        let html = `<div class="question-text">${q.question_text}</div>`;
        
        if (q.type === 'mcq' || q.type === 'msq') {
            html += `<div class="options-list">`;
            q.options.forEach(opt => {
                const isChecked = isSelected(q.id, opt.id);
                html += `
                    <label class="option-item ${isChecked ? 'selected' : ''}">
                        <input type="${q.type === 'mcq' ? 'radio' : 'checkbox'}" 
                               name="q_opt" 
                               value="${opt.id}" 
                               ${isChecked ? 'checked' : ''}
                               onchange="handleOptionChange(${q.id}, ${opt.id}, '${q.type}')">
                        <span>${opt.option_text}</span>
                    </label>
                `;
            });
            html += `</div>`;
        } else if (q.type === 'nat') {
            const val = responses[q.id] ? responses[q.id].value : '';
            html += `
                <div class="mt-4">
                    <p class="text-sm mb-2">Enter your numerical answer:</p>
                    <input type="number" step="any" class="nat-input" value="${val}" 
                           oninput="handleNatChange(${q.id}, this.value)">
                </div>
            `;
        }

        document.getElementById('question-content').innerHTML = html;
        updatePaletteUI();
    }

    function handleOptionChange(qId, optId, type) {
        if (type === 'mcq') {
            responses[qId] = { type: 'mcq', value: optId };
        } else {
            if (!responses[qId]) responses[qId] = { type: 'msq', value: [] };
            const index = responses[qId].value.indexOf(optId);
            if (index > -1) responses[qId].value.splice(index, 1);
            else responses[qId].value.push(optId);
        }
        renderQuestion(currentIndex);
    }

    function handleNatChange(qId, val) {
        responses[qId] = { type: 'nat', value: val };
    }

    function isSelected(qId, optId) {
        if (!responses[qId]) return false;
        if (responses[qId].type === 'mcq') return responses[qId].value == optId;
        return responses[qId].value.includes(optId);
    }

    function nextQuestion() {
        if (currentIndex < questions.length - 1) {
            renderQuestion(currentIndex + 1);
        }
    }

    function prevQuestion() {
        if (currentIndex > 0) {
            renderQuestion(currentIndex - 1);
        }
    }

    function jumpToQuestion(index) {
        renderQuestion(index);
    }

    function clearResponse() {
        delete responses[questions[currentIndex].id];
        renderQuestion(currentIndex);
    }

    function startTimer() {
        const timerEl = document.getElementById('timer');
        const interval = setInterval(() => {
            timeRemaining--;
            if (timeRemaining <= 0) {
                clearInterval(interval);
                autoSubmit();
            }
            const hrs = Math.floor(timeRemaining / 3600);
            const mins = Math.floor((timeRemaining % 3600) / 60);
            const secs = timeRemaining % 60;
            timerEl.innerText = `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }, 1000);
    }

    function updatePaletteUI() {
        questions.forEach((q, i) => {
            const btn = document.getElementById(`p-btn-${i}`);
            btn.className = 'palette-btn';
            if (i === currentIndex) btn.classList.add('current');
            else if (responses[q.id]) btn.classList.add('answered');
            else btn.classList.add('not-visited');
        });
    }

    function confirmSubmit() {
        if (confirm("Are you sure you want to submit the test?")) {
            autoSubmit();
        }
    }

    function autoSubmit() {
        document.getElementById('responses-input').value = JSON.stringify(responses);
        document.getElementById('time-spent-input').value = totalTime - timeRemaining;
        document.getElementById('submit-test-form').submit();
    }

    window.onload = initTest;
</script>
@endsection
