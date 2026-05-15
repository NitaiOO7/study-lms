@extends('layouts.app')

@section('content')
<div class="container py-5 animate-in">
    <div class="page-header mb-5">
        <div>
            <h1 class="page-title">Performance Analytics</h1>
            <p class="page-subtitle">Track your progress and identify areas for improvement.</p>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid-4 mb-5">
        <div class="stat-card">
            <div class="stat-label">Overall Accuracy</div>
            <div class="stat-value text-primary">{{ $overview['accuracy'] }}%</div>
            <div class="progress-bar mt-2"><div class="progress" style="width: {{ $overview['accuracy'] }}%"></div></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tests Attempted</div>
            <div class="stat-value">{{ $overview['total_tests'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Average Score</div>
            <div class="stat-value text-success">{{ round($overview['avg_score'], 1) }}%</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Global Percentile</div>
            <div class="stat-value text-accent">{{ $avgPercentile }}%</div>
        </div>
    </div>

    <div class="grid-2">
        <!-- Score Trend Chart -->
        <div class="card p-4">
            <h3 class="mb-4">Score Trends</h3>
            <canvas id="scoreTrendChart" height="250"></canvas>
        </div>

        <!-- Section Wise Performance (Radar) -->
        <div class="card p-4">
            <h3 class="mb-4">Topic-wise Proficiency</h3>
            <canvas id="topicRadarChart" height="250"></canvas>
        </div>
    </div>

    <!-- Detailed Topic Breakdown -->
    <div class="card mt-5">
        <h3 class="mb-4">Detailed Topic Analysis</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Topic</th>
                        <th>Questions Attempted</th>
                        <th>Accuracy</th>
                        <th>Strength</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topicAnalysis as $topic)
                    <tr>
                        <td><strong>{{ $topic->topic }}</strong></td>
                        <td>{{ $topic->total }}</td>
                        <td>{{ round(($topic->correct / $topic->total) * 100, 1) }}%</td>
                        <td>
                            @php $acc = ($topic->correct / $topic->total) * 100; @endphp
                            @if($acc >= 80)
                                <span class="badge badge-success">Strong</span>
                            @elseif($acc >= 50)
                                <span class="badge badge-warning">Average</span>
                            @else
                                <span class="badge badge-danger">Weak</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Score Trend Chart
    const trendCtx = document.getElementById('scoreTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Score %',
                data: @json($trendData),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });

    // Topic Radar Chart
    const radarCtx = document.getElementById('topicRadarChart').getContext('2d');
    new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: @json($topicAnalysis->pluck('topic')),
            datasets: [{
                label: 'Your Accuracy',
                data: @json($topicAnalysis->map(fn($t) => ($t->correct / $t->total) * 100)),
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                borderColor: '#6366f1',
                pointBackgroundColor: '#6366f1'
            }]
        },
        options: {
            scales: { r: { beginAtZero: true, max: 100 } }
        }
    });
</script>
@endpush
@endsection
