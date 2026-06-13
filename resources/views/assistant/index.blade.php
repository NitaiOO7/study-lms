@extends('layouts.app')

@section('title', 'AI Assistant - EduVerse LMS')

@push('styles')
<style>
    .assistant-shell {
        min-height: calc(100vh - 70px);
        padding: 24px;
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 20px;
    }

    .assistant-panel {
        background: var(--dark-card);
        border: 1px solid var(--dark-border);
        border-radius: 16px;
        overflow: hidden;
    }

    .assistant-sidebar {
        padding: 20px;
        align-self: start;
        position: sticky;
        top: 94px;
    }

    .assistant-kicker {
        color: var(--primary-light);
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .assistant-heading {
        font-size: 1.45rem;
        line-height: 1.25;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .assistant-copy {
        color: var(--text-secondary);
        font-size: 0.92rem;
        line-height: 1.6;
    }

    .suggestion-list {
        display: grid;
        gap: 10px;
        margin-top: 20px;
    }

    .model-picker {
        margin-top: 20px;
    }

    .model-picker label {
        display: block;
        color: var(--text-secondary);
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .suggestion-btn {
        width: 100%;
        padding: 12px 14px;
        background: var(--dark-surface);
        border: 1px solid var(--dark-border);
        border-radius: 10px;
        color: var(--text-primary);
        text-align: left;
        cursor: pointer;
        font: inherit;
        font-size: 0.88rem;
        line-height: 1.35;
        transition: all 0.2s ease;
    }

    .suggestion-btn:hover {
        border-color: var(--primary);
        background: rgba(99, 102, 241, 0.12);
    }

    .chat-panel {
        min-height: calc(100vh - 118px);
        display: grid;
        grid-template-rows: auto minmax(0, 1fr) auto;
    }

    .chat-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--dark-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .chat-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 800;
        font-size: 1.05rem;
    }

    .chat-title-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: var(--gradient-primary);
        display: grid;
        place-items: center;
        color: #fff;
    }

    .chat-status {
        color: var(--success);
        font-size: 0.8rem;
        font-weight: 700;
    }

    .chat-messages {
        padding: 22px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .message-row {
        display: flex;
        gap: 10px;
        max-width: 84%;
    }

    .message-row.user {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .message-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: var(--dark-surface);
        border: 1px solid var(--dark-border);
        color: var(--primary-light);
        flex: 0 0 34px;
    }

    .message-row.user .message-avatar {
        background: var(--gradient-primary);
        color: #fff;
        border: 0;
    }

    .message-bubble {
        padding: 13px 15px;
        border-radius: 14px;
        background: var(--dark-surface);
        border: 1px solid var(--dark-border);
        color: var(--text-primary);
        line-height: 1.6;
        font-size: 0.94rem;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .message-row.user .message-bubble {
        background: rgba(99, 102, 241, 0.18);
        border-color: rgba(99, 102, 241, 0.35);
    }

    .typing {
        display: inline-flex;
        gap: 5px;
        align-items: center;
    }

    .typing span {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--text-secondary);
        animation: typingPulse 1s infinite ease-in-out;
    }

    .typing span:nth-child(2) { animation-delay: 0.15s; }
    .typing span:nth-child(3) { animation-delay: 0.3s; }

    @keyframes typingPulse {
        0%, 80%, 100% { opacity: 0.35; transform: translateY(0); }
        40% { opacity: 1; transform: translateY(-3px); }
    }

    .chat-composer {
        padding: 18px;
        border-top: 1px solid var(--dark-border);
        background: rgba(10, 10, 26, 0.55);
    }

    .composer-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: end;
    }

    .composer-input {
        min-height: 52px;
        max-height: 150px;
        resize: vertical;
    }

    .composer-actions {
        display: flex;
        gap: 8px;
    }

    .icon-btn {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        justify-content: center;
        padding: 0;
    }

    .error-note {
        margin-top: 10px;
        color: var(--danger);
        font-size: 0.86rem;
        display: none;
    }

    .error-note.visible {
        display: block;
    }

    @media (max-width: 900px) {
        .assistant-shell {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .assistant-sidebar {
            position: static;
        }

        .chat-panel {
            min-height: 70vh;
        }

        .message-row {
            max-width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="assistant-shell">
    <aside class="assistant-panel assistant-sidebar">
        <div class="assistant-kicker">Role aware help</div>
        <h1 class="assistant-heading">AI Assistant</h1>
        <p class="assistant-copy">
            Ask about courses, tests, lessons, platform workflows, or admin tasks. The assistant uses your current role to answer with the right context.
        </p>

        <div class="suggestion-list" aria-label="Suggested questions">
            @foreach($suggestions as $suggestion)
                <button type="button" class="suggestion-btn" data-suggestion="{{ $suggestion }}">
                    {{ $suggestion }}
                </button>
            @endforeach
        </div>

        <div class="model-picker">
            <label for="model-select">Free model preference</label>
            <select class="form-select" id="model-select">
                <option value="auto">Auto fallback</option>
                @foreach($models as $model)
                    <option value="{{ $model }}">{{ $model }}</option>
                @endforeach
            </select>
        </div>
    </aside>

    <section class="assistant-panel chat-panel" aria-label="AI assistant chat">
        <header class="chat-header">
            <div class="chat-title">
                <span class="chat-title-icon"><i class="fas fa-robot"></i></span>
                <span>EduBot</span>
            </div>
            <div class="chat-status" id="assistant-status">Ready</div>
        </header>

        <div class="chat-messages" id="chat-messages">
            <div class="message-row assistant">
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-bubble">Hi {{ auth()->user()->name }}. Ask me anything about EduVerse LMS, and I will help you test the assistant flow.</div>
            </div>
        </div>

        <div class="chat-composer">
            <form class="composer-form" id="assistant-form">
                @csrf
                <textarea class="form-textarea composer-input" id="assistant-input" name="message" rows="2" placeholder="Type your question..." required></textarea>
                <div class="composer-actions">
                    <button type="submit" class="btn btn-primary icon-btn" title="Send message" aria-label="Send message">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
            <div class="error-note" id="assistant-error"></div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const form = document.getElementById('assistant-form');
        const input = document.getElementById('assistant-input');
        const messages = document.getElementById('chat-messages');
        const status = document.getElementById('assistant-status');
        const error = document.getElementById('assistant-error');
        const modelSelect = document.getElementById('model-select');
        const csrf = document.querySelector('input[name="_token"]').value;
        let sessionToken = null;
        let isSending = false;

        const scrollToBottom = () => {
            messages.scrollTop = messages.scrollHeight;
        };

        const setError = (text = '') => {
            error.textContent = text;
            error.classList.toggle('visible', Boolean(text));
        };

        const addMessage = (role, content) => {
            const row = document.createElement('div');
            row.className = `message-row ${role}`;

            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.innerHTML = role === 'user' ? '<i class="fas fa-user"></i>' : '<i class="fas fa-robot"></i>';

            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            bubble.textContent = content;

            row.appendChild(avatar);
            row.appendChild(bubble);
            messages.appendChild(row);
            scrollToBottom();

            return row;
        };

        const addTyping = () => {
            const row = document.createElement('div');
            row.className = 'message-row assistant';
            row.id = 'assistant-typing';
            row.innerHTML = `
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-bubble">
                    <span class="typing" aria-label="Assistant is typing">
                        <span></span><span></span><span></span>
                    </span>
                </div>
            `;
            messages.appendChild(row);
            scrollToBottom();
        };

        const removeTyping = () => {
            document.getElementById('assistant-typing')?.remove();
        };

        const sendMessage = async (message) => {
            if (!message || isSending) return;

            isSending = true;
            setError();
            status.textContent = 'Thinking';
            addMessage('user', message);
            addTyping();
            input.value = '';

            try {
                const response = await fetch('{{ route('assistant.message') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        message,
                        session_token: sessionToken,
                        preferred_model: modelSelect?.value || null,
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'The assistant request failed.');
                }

                sessionToken = data.session_token;
                removeTyping();
                addMessage('assistant', data.response || 'No response returned.');
                status.textContent = data.model ? `Ready - ${data.model}` : 'Ready';
            } catch (exception) {
                removeTyping();
                setError(exception.message || 'Something went wrong while contacting the assistant.');
            } finally {
                isSending = false;
                if (status.textContent === 'Thinking') {
                    status.textContent = 'Ready';
                }
                input.focus();
            }
        };

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            sendMessage(input.value.trim());
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form.requestSubmit();
            }
        });

        document.querySelectorAll('[data-suggestion]').forEach((button) => {
            button.addEventListener('click', () => {
                sendMessage(button.dataset.suggestion.trim());
            });
        });

        scrollToBottom();
    })();
</script>
@endpush
