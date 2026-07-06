@extends('layouts.admin')

@section('title', 'Survey Reports - ' . $survey->title['en'])
@section('header_title', 'Survey Analytics & Reports')

@section('content')
    <div class="space-y-8 animate-fade-in">
        <!-- Header widget -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <a href="{{ route('surveys.show', $survey) }}" class="text-xs font-bold text-blue-600 hover:underline">&larr; Back to Survey Details</a>
                <h2 class="text-2xl font-extrabold text-slate-800 mt-2">{{ $survey->title['en'] }}</h2>
                <p class="text-sm text-slate-500 mt-1">Analytics overview and submission log reports.</p>
            </div>
            
            <div class="flex items-center space-x-3 mt-4 md:mt-0">
                <a href="{{ route('reports.export', $survey) }}" class="px-5 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold shadow-md transition">
                    Export Responses (CSV)
                </a>
            </div>
        </div>

        <!-- Metrics Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Total Feedbacks</h4>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-3xl font-extrabold text-slate-800">{{ $stats['total'] }}</span>
                    <span class="text-xs text-emerald-600 font-semibold">{{ $stats['today'] }} received today</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Average Completion Time</h4>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-3xl font-extrabold text-slate-800">
                        {{ $stats['avg_duration'] ? round($stats['avg_duration']) . 's' : 'N/A' }}
                    </span>
                    <span class="text-xs text-slate-400">average session duration</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Language Distribution</h4>
                <div class="flex items-center space-x-4 mt-3">
                    <div class="flex-1">
                        <div class="flex justify-between text-xs font-bold text-slate-600 mb-1">
                            <span>English</span>
                            <span>{{ $stats['total'] > 0 ? round((($stats['by_language']['en'] ?? 0) / $stats['total']) * 100) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-blue-600 h-full rounded-full" style="width: {{ $stats['total'] > 0 ? (($stats['by_language']['en'] ?? 0) / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between text-xs font-bold text-slate-600 mb-1">
                            <span>Arabic / العربية</span>
                            <span>{{ $stats['total'] > 0 ? round((($stats['by_language']['ar'] ?? 0) / $stats['total']) * 100) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-yellow-500 h-full rounded-full" style="width: {{ $stats['total'] > 0 ? (($stats['by_language']['ar'] ?? 0) / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-md font-bold text-slate-800 mb-4">Response Activity Trend (Last 30 Days)</h3>
                <div class="h-64">
                    <canvas id="dailyTrendChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-md font-bold text-slate-800 mb-4">Hourly Submission Distribution</h3>
                <div class="h-64">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Questions Distributions Cards -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Answer Option Frequency Distributions</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($survey->questions as $question)
                    @if($question->type != 'text')
                        <div class="border border-slate-100 rounded-xl p-5">
                            <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Question {{ $loop->iteration }}</span>
                            <h4 class="font-bold text-slate-800 text-sm mt-1 mb-4">{{ $question->text['en'] }}</h4>
                            
                            <div class="space-y-3">
                                @php 
                                    $qDist = $distribution[$question->id] ?? [];
                                    $qTotal = collect($qDist)->sum('count');
                                @endphp
                                @forelse($question->answerOptions as $option)
                                    @php 
                                        $optDist = collect($qDist)->firstWhere('answer_option_id', $option->id);
                                        $count = $optDist ? $optDist->count : 0;
                                        $percent = $qTotal > 0 ? round(($count / $qTotal) * 100) : 0;
                                        $color = $option->color ?: '#3b82f6';
                                    @endphp
                                    <div>
                                        <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                                            <span class="flex items-center space-x-1.5">
                                                <span>{{ $option->icon }}</span>
                                                <span class="font-bold">{{ $option->label['en'] }}</span>
                                            </span>
                                            <span>{{ $count }} ({{ $percent }}%)</span>
                                        </div>
                                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-500" style="background-color: {{ $color }}; width: {{ $percent }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 italic">No options defined.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Responses Log Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Recent Responses Log</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold border-b border-slate-100">
                            <th class="py-3 px-4">Response UUID</th>
                            <th class="py-3 px-4">Language</th>
                            <th class="py-3 px-4">Duration</th>
                            <th class="py-3 px-4">Kiosk Device</th>
                            <th class="py-3 px-4">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($responses as $response)
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono text-xs text-slate-700">{{ $response->uuid }}</td>
                                <td class="py-3.5 px-4 text-xs font-bold uppercase text-slate-600">{{ $response->language }}</td>
                                <td class="py-3.5 px-4 text-xs text-slate-600">
                                    @if($response->started_at && $response->completed_at)
                                        {{ \Carbon\Carbon::parse($response->started_at)->diffInSeconds(\Carbon\Carbon::parse($response->completed_at)) }}s
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-xs text-slate-700 font-semibold">{{ $response->device_name ?: 'Web / Unknown' }}</td>
                                <td class="py-3.5 px-4 text-xs text-slate-500">{{ \Carbon\Carbon::parse($response->created_at)->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 px-4 text-slate-400 italic text-center text-sm">No submissions recorded for this survey yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Daily Activity Trend Chart
        const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
        const dailyData = @json($dailyTrend);
        
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: Object.keys(dailyData),
                datasets: [{
                    label: 'Responses Count',
                    data: Object.values(dailyData),
                    borderColor: '#1e3a8a',
                    backgroundColor: 'rgba(30, 58, 138, 0.05)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 2.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // Hourly Submission Distribution Chart
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        const hourlyData = @json($hourlyDistribution);
        
        // Prepare hourly labels 0 to 23
        const hourlyLabels = Array.from({ length: 24 }, (_, i) => `${i}:00`);
        const hourlyValues = Array.from({ length: 24 }, (_, i) => hourlyData[i] || 0);

        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: hourlyLabels,
                datasets: [{
                    data: hourlyValues,
                    backgroundColor: '#d97706',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>
@endsection
