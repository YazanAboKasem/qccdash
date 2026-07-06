@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('header_title', 'System Dashboard')

@section('content')
    <!-- Metrics Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center space-x-4">
            <div class="p-4 rounded-xl bg-blue-50 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Active Campaigns</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['active_campaigns'] }} / {{ $stats['total_campaigns'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center space-x-4">
            <div class="p-4 rounded-xl bg-violet-50 text-violet-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Total Surveys</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_surveys'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center space-x-4">
            <div class="p-4 rounded-xl bg-emerald-50 text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Total Responses</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_responses'] }}</h3>
                <span class="text-xs text-emerald-600 font-semibold">{{ $stats['today_responses'] }} received today</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center space-x-4">
            <div class="p-4 rounded-xl bg-amber-50 text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Kiosks Online</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['online_devices'] }} / {{ $stats['active_devices'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Campaigns list -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6">Active Campaigns & Surveys</h3>
                
                <div class="space-y-6">
                    @foreach($campaigns as $campaign)
                        <div class="border border-slate-100 rounded-xl p-5 hover:border-slate-200 transition">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="font-bold text-slate-800 text-lg">{{ $campaign->title['en'] }}</h4>
                                    <p class="text-sm text-slate-500">{{ $campaign->description['en'] ?? '' }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $campaign->status == 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </div>
                            
                            <div class="pl-4 border-l-2 border-slate-100 space-y-3 mt-3">
                                @forelse($campaign->surveys as $survey)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                            <a href="{{ route('surveys.show', $survey) }}" class="font-semibold text-blue-600 hover:underline text-sm">
                                                {{ $survey->title['en'] }}
                                            </a>
                                            <span class="text-xs text-slate-400">(v{{ $survey->version }})</span>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="text-xs text-slate-500 font-semibold">{{ $survey->responses_count }} Responses</span>
                                            <a href="{{ route('reports.show', $survey) }}" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                                                Reports
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 italic">No surveys added to this campaign yet.</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Activity logs -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Recent Activity Logs</h3>
            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse($activity as $index => $log)
                        <li>
                            <div class="relative pb-8">
                                @if($index < count($activity) - 1)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-700">
                                            L
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-1.5">
                                        <p class="text-xs text-slate-500">
                                            <span class="font-bold text-slate-800">{{ $log->user_name ?? 'System' }}</span>
                                            performed <span class="font-semibold text-blue-600">{{ $log->action }}</span>
                                        </p>
                                        <span class="text-[10px] text-slate-400 block mt-0.5">
                                            {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <p class="text-sm text-slate-400 italic text-center py-8">No recent activities logged.</p>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
