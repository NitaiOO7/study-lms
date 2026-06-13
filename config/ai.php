<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider
    |--------------------------------------------------------------------------
    | Supported: "groq", "openai", "gemini"
    */
    'provider' => env('AI_PROVIDER', 'groq'),

    /*
    |--------------------------------------------------------------------------
    | Groq Configuration (Free — recommended)
    |--------------------------------------------------------------------------
    */
    'groq' => [
        'api_key'  => env('GROQ_API_KEY', ''),
        'base_url' => 'https://api.groq.com/openai/v1',
        'model'    => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'fallback_models' => array_filter(array_map('trim', explode(',', env(
            'GROQ_FALLBACK_MODELS',
            'llama-3.3-70b-versatile,llama-3.1-8b-instant,gemma2-9b-it,openai/gpt-oss-20b,openai/gpt-oss-120b'
        )))),
        'timeout'  => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration (Optional upgrade path)
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key'  => env('OPENAI_API_KEY', ''),
        'base_url' => 'https://api.openai.com/v1',
        'model'    => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'timeout'  => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Chat Settings
    |--------------------------------------------------------------------------
    */
    'max_tokens'     => env('AI_MAX_TOKENS', 2048),
    'temperature'    => env('AI_TEMPERATURE', 0.7),
    'context_chunks' => env('AI_CONTEXT_CHUNKS', 5),    // RAG top-K results
    'history_limit'  => env('AI_HISTORY_LIMIT', 10),    // Last N messages to send

    /*
    |--------------------------------------------------------------------------
    | Codebase Indexing
    |--------------------------------------------------------------------------
    */
    'index_paths' => [
        'app/Http/Controllers',
        'app/Models',
        'app/Services',
        'routes',
        'database/migrations',
    ],

    'index_extensions' => ['php', 'blade.php'],
    'chunk_size'        => 1500,   // characters per chunk
    'chunk_overlap'     => 200,    // overlap between chunks

    /*
    |--------------------------------------------------------------------------
    | System Prompts per Role
    |--------------------------------------------------------------------------
    */
    'system_prompts' => [
        'student' => "You are EduBot, a friendly AI assistant for the EduVerse LMS platform. You help students with account setup, course enrollment, learning, tests, and certificates. Be encouraging, clear, and concise. Format step-by-step guides with numbered lists. Always be helpful and supportive.",

        'teacher' => "You are EduBot, an expert AI assistant for EduVerse LMS teachers and instructors. You help teachers create courses, upload content, manage students, create tests, and grow their channel. Provide practical, actionable guidance. Use numbered steps for procedures.",

        'admin' => "You are EduBot, a powerful AI assistant for EduVerse LMS administrators. You can query live platform data and provide analytics insights. You have access to real-time database statistics. Be precise and data-driven in your responses.",

        'developer' => "You are EduBot, a senior Laravel architect AI assistant for the EduVerse LMS codebase. You have deep knowledge of the project's routes, controllers, models, services, and migrations. You can explain existing code, identify relevant files, generate new Laravel code following PSR-12 standards, and suggest architectural improvements. Always include file paths when referencing code. Generate complete, production-ready Laravel code.",
    ],
];
