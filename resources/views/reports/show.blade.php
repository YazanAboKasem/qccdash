@extends('layouts.admin')

@section('title', 'Survey Reports - ' . $survey->title['en'])
@section('header_title', 'MY SEPHORA Quiz Analytics')

@section('content')
    <div class="space-y-8 animate-fade-in">
        <!-- Header Card -->
        <div class="bg-slate-950 rounded-2xl shadow-lg border border-slate-800 p-8 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-red-600/10 via-transparent to-transparent"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <a href="{{ route('surveys.show', $survey) }}" class="text-xs font-bold text-red-400 hover:text-red-300 transition">&larr; Back to Kiosk Quiz Details</a>
                    <div class="flex items-center space-x-3 mt-3">
                        <span class="text-xs font-bold bg-red-600 text-white px-3 py-1 rounded-full uppercase tracking-widest">SEPHORA Loyalty</span>
                        <span class="text-xs font-semibold text-slate-400">Version {{ $survey->version }}</span>
                    </div>
                    <h2 class="text-3xl font-black tracking-tight mt-2 text-white">{{ $survey->title['en'] }}</h2>
                    <p class="text-sm text-slate-400 mt-2">{{ $survey->description['en'] ?? '' }}</p>
                </div>
                
                <div class="flex items-center space-x-3 mt-6 md:mt-0">
                    <a href="{{ route('reports.export', $survey) }}" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-red-600/20 transition cursor-pointer">
                        Export Report (CSV)
                    </a>
                </div>
            </div>
        </div>

        <!-- Metrics Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Total Participants -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Participants</h4>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-4xl font-black text-slate-900">{{ $stats['total'] }}</span>
                    <span class="text-xs text-red-600 font-bold bg-red-50 px-2 py-0.5 rounded-full">+{{ $stats['today'] }} today</span>
                </div>
            </div>

            <!-- Average Quiz Score -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Average Quiz Score</h4>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-4xl font-black text-slate-900">{{ $stats['quiz']['avg_score'] }}</span>
                    <span class="text-sm text-slate-400 font-medium">/ 13 correct</span>
                </div>
            </div>

            <!-- Pass Rate -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Quiz Pass Rate</h4>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-4xl font-black text-emerald-600">{{ $stats['quiz']['pass_rate'] }}%</span>
                    <span class="text-xs text-slate-400">score &ge; 7 / 13</span>
                </div>
            </div>

            <!-- Avg Duration -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Avg Session Time</h4>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-4xl font-black text-slate-900">
                        {{ $stats['avg_duration'] ? round($stats['avg_duration']) . 's' : 'N/A' }}
                    </span>
                    <span class="text-xs text-slate-400">seconds to complete</span>
                </div>
            </div>
        </div>

        <!-- Correct Answers per Question -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="border-b border-slate-100 pb-4 mb-6">
                <h3 class="text-lg font-bold text-slate-800">Question Accuracy & Correct Rate</h3>
                <p class="text-xs text-slate-400 mt-1">Detailed statistics showing the percentage of participants who answered each question correctly.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($correctRates as $rate)
                    <div class="border border-slate-100 rounded-xl p-5 hover:border-slate-200 transition">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] font-black text-red-600 uppercase tracking-wider bg-red-50 px-2 py-0.5 rounded">Q{{ $loop->iteration }}</span>
                            <span class="text-xs font-bold text-slate-500">Correct: {{ $rate->correct_count }} / {{ $rate->total_count }}</span>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm mb-4">{{ $rate->text['en'] }}</h4>
                        
                        <div>
                            <div class="flex justify-between text-xs font-bold text-slate-700 mb-1">
                                <span>Correct Answer Rate</span>
                                <span class="{{ $rate->correct_rate >= 70 ? 'text-emerald-600' : ($rate->correct_rate >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $rate->correct_rate }}%
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $rate->correct_rate >= 70 ? 'bg-emerald-500' : ($rate->correct_rate >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                     style="width: {{ $rate->correct_rate }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Score Distribution -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:col-span-1">
                <h3 class="text-md font-bold text-slate-800 mb-4">Quiz Score Distribution</h3>
                <div class="space-y-3">
                    @for($scoreIdx = 13; $scoreIdx >= 0; $scoreIdx--)
                        @php
                            $scoreCount = $stats['quiz']['score_distribution'][$scoreIdx] ?? 0;
                            $scorePercent = $stats['total'] > 0 ? round(($scoreCount / $stats['total']) * 100) : 0;
                        @endphp
                        <div class="flex items-center space-x-3 text-xs">
                            <span class="w-14 font-bold text-slate-600 text-right">{{ $scoreIdx }} / 13</span>
                            <div class="flex-1 bg-slate-100 h-3 rounded-full overflow-hidden">
                                <div class="bg-red-600 h-full rounded-full" style="width: {{ $scorePercent }}%"></div>
                            </div>
                            <span class="w-10 font-semibold text-slate-500 text-right">{{ $scoreCount }} ({{ $scorePercent }}%)</span>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Activity Trend & Hours -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:col-span-2 space-y-6">
                <h3 class="text-md font-bold text-slate-800">Response Trend & Activity Hourly</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Last 30 Days Trend</span>
                        <div class="h-48">
                            <canvas id="dailyTrendChart"></canvas>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Hourly Peaks</span>
                        <div class="h-48">
                            <canvas id="hourlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Quiz Submission Logs</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold border-b border-slate-100">
                            <th class="py-3 px-4">Participant UUID</th>
                            <th class="py-3 px-4">Score</th>
                            <th class="py-3 px-4">Result</th>
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
                                <td class="py-3.5 px-4 text-sm font-bold text-slate-900">{{ $response->score }} / 13</td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $response->score >= 7 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                        {{ $response->score >= 7 ? 'Pass' : 'Fail' }}
                                    </span>
                                </td>
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
                                <td colspan="7" class="py-8 px-4 text-slate-400 italic text-center text-sm">No submissions recorded for this survey yet.</td>
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
                    label: 'Submissions',
                    data: Object.values(dailyData),
                    borderColor: '#E2001A',
                    backgroundColor: 'rgba(226, 0, 26, 0.05)',
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
        
        const hourlyLabels = Array.from({ length: 24 }, (_, i) => `${i}:00`);
        const hourlyValues = Array.from({ length: 24 }, (_, i) => hourlyData[i] || 0);

        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: hourlyLabels,
                datasets: [{
                    data: hourlyValues,
                    backgroundColor: '#111827',
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
