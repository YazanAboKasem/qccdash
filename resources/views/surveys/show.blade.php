@extends('layouts.admin')

@section('title', $survey->title['en'])
@section('header_title', 'Survey Details')

@section('content')
    <div class="space-y-8">
        <!-- Survey Header & Settings Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-slate-100 pb-6 mb-6">
                <div>
                    <span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Campaign: {{ $survey->campaign->title['en'] }}</span>
                    <h2 class="text-2xl font-extrabold text-slate-800 mt-2">{{ $survey->title['en'] }}</h2>
                    <p class="text-sm text-slate-500 mt-1">{{ $survey->description['en'] ?? '' }}</p>
                </div>
                <div class="flex items-center space-x-3 mt-4 md:mt-0">
                    <a href="{{ route('reports.show', $survey) }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md transition">
                        View Analytics Report
                    </a>
                    <form action="{{ route('surveys.duplicate', $survey) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-sm font-bold transition cursor-pointer">
                            Duplicate Survey
                        </button>
                    </form>
                </div>
            </div>

            <!-- Settings Update Form -->
            <form action="{{ route('surveys.update', $survey) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">English Title</label>
                    <input type="text" name="title[en]" value="{{ $survey->title['en'] }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Arabic Title</label>
                    <input type="text" name="title[ar]" value="{{ $survey->title['ar'] }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600">
                </div>
                
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex flex-col items-center justify-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50">
                        <input type="checkbox" name="settings[show_progress]" value="1" {{ ($survey->settings['show_progress'] ?? true) ? 'checked' : '' }} class="rounded text-blue-600">
                        <span class="text-[10px] font-semibold text-slate-600 mt-1 text-center">Progress</span>
                    </label>
                    <label class="flex flex-col items-center justify-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50">
                        <input type="checkbox" name="settings[allow_back]" value="1" {{ ($survey->settings['allow_back'] ?? true) ? 'checked' : '' }} class="rounded text-blue-600">
                        <span class="text-[10px] font-semibold text-slate-600 mt-1 text-center">Allow Back</span>
                    </label>
                    <label class="flex flex-col items-center justify-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50">
                        <input type="checkbox" name="settings[require_all]" value="1" {{ ($survey->settings['require_all'] ?? true) ? 'checked' : '' }} class="rounded text-blue-600">
                        <span class="text-[10px] font-semibold text-slate-600 mt-1 text-center">Require All</span>
                    </label>
                </div>

                <div class="flex items-center space-x-2">
                    <select name="status" class="px-4 py-2.5 rounded-lg border border-slate-300 bg-white">
                        <option value="active" {{ $survey->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="draft" {{ $survey->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ $survey->status == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-sm font-bold transition cursor-pointer">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Questions List Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Survey Questions</h3>
                    <p class="text-xs text-slate-400">Drag and drop rows to reorder questions.</p>
                </div>
                <button onclick="document.getElementById('addQuestionModal').classList.remove('hidden')" 
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-md transition cursor-pointer">
                    + Add Question
                </button>
            </div>

            <!-- Questions Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold border-b border-slate-100">
                            <th class="py-3.5 px-4 w-12"></th>
                            <th class="py-3.5 px-4 w-20">Code</th>
                            <th class="py-3.5 px-4 w-48">Section / Axis</th>
                            <th class="py-3.5 px-4">Question Text (EN / AR)</th>
                            <th class="py-3.5 px-4">Type</th>
                            <th class="py-3.5 px-4">Required</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="questionTableBody">
                        @forelse($survey->questions as $question)
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition cursor-move animate-fade-in" 
                                draggable="true" 
                                data-id="{{ $question->id }}"
                                ondragstart="dragStart(event)" 
                                ondragover="dragOver(event)" 
                                ondrop="drop(event)">
                                <td class="py-4 px-4 text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                </td>
                                <td class="py-4 px-4 font-mono text-xs font-bold text-slate-700">
                                    {{ $question->settings['code'] ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-4 text-xs text-slate-600">
                                    <div class="font-semibold">{{ $question->settings['section'] ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $question->settings['section_ar'] ?? '' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-800 text-sm">{{ $question->text['en'] }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $question->text['ar'] }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-600 uppercase">
                                        {{ str_replace('_', ' ', $question->type) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="text-xs font-semibold {{ $question->is_required ? 'text-blue-600' : 'text-slate-400' }}">
                                        {{ $question->is_required ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <form action="{{ route('questions.toggle', $question) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-full cursor-pointer {{ $question->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-4 px-4 text-right space-x-2">
                                    <a href="{{ route('questions.edit', $question) }}" class="inline-block px-3 py-1.5 border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-lg text-xs font-bold transition">
                                        Edit / Options
                                    </a>
                                    <form action="{{ route('questions.destroy', $question) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this question?')" 
                                                class="px-3 py-1.5 border border-red-200 hover:bg-red-50 text-red-600 rounded-lg text-xs font-bold transition cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 px-4 text-slate-400 italic text-center text-sm">No questions added yet. Click "+ Add Question" to start.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Question Modal -->
    <div id="addQuestionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-6 hidden z-50 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl border border-slate-100 p-6 my-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <h3 class="text-lg font-bold text-slate-800">Add New Question</h3>
                <button onclick="document.getElementById('addQuestionModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('questions.store', $survey) }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">English Question Text</label>
                        <input type="text" name="text[en]" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Arabic Question Text</label>
                        <input type="text" name="text[ar]" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">English Description / Instruction (Optional)</label>
                        <input type="text" name="description[en]" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Arabic Description / Instruction (Optional)</label>
                        <input type="text" name="description[ar]" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Question Code (e.g., D1, Q1)</label>
                        <input type="text" name="settings[code]" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Section / Axis (English)</label>
                        <input type="text" name="settings[section]" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Section / Axis (Arabic)</label>
                        <input type="text" name="settings[section_ar]" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Type</label>
                        <select name="type" id="modalQuestionType" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option value="single_choice">Single Choice</option>
                            <option value="rating">Rating (Stars/Emoji)</option>
                            <option value="yes_no">Yes / No</option>
                            <option value="text">Open Text Feedback</option>
                        </select>
                    </div>
                    <div class="flex items-center pt-8">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="is_required" value="1" checked class="rounded text-blue-600">
                            <span class="text-sm text-slate-700 font-semibold">Is Required</span>
                        </label>
                    </div>
                </div>

                <!-- Modal Answer Options Management -->
                <div id="modalOptionsContainer" class="border-t border-slate-100 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-md font-bold text-slate-800">Answer Options Configuration</h4>
                        <button type="button" onclick="addModalOptionRow()" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition cursor-pointer">
                            + Add Option Row
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-slate-100 rounded-xl shadow-inner max-h-60 overflow-y-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold border-b border-slate-100">
                                    <th class="py-3 px-4">Label (EN)</th>
                                    <th class="py-3 px-4">Label (AR)</th>
                                    <th class="py-3 px-4 w-24">Value</th>
                                    <th class="py-3 px-4 w-20">Score</th>
                                    <th class="py-3 px-4 w-20">Icon (Emoji)</th>
                                    <th class="py-3 px-4 w-24">Color (Hex)</th>
                                    <th class="py-3 px-4 w-12 text-right"></th>
                                </tr>
                            </thead>
                            <tbody id="modalOptionsTableBody">
                                <!-- Dynamic rows added here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6 flex space-x-4">
                    <button type="button" onclick="document.getElementById('addQuestionModal').classList.add('hidden')" 
                            class="flex-1 py-3 border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50 transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md transition cursor-pointer">
                        Add Question
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let dragSrcEl = null;

        function dragStart(e) {
            dragSrcEl = e.currentTarget;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', dragSrcEl.innerHTML);
            dragSrcEl.classList.add('opacity-40');
        }

        function dragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            return false;
        }

        function drop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }

            const target = e.currentTarget;
            if (dragSrcEl !== target) {
                const tempHTML = target.innerHTML;
                const tempId = target.getAttribute('data-id');

                target.innerHTML = dragSrcEl.innerHTML;
                target.setAttribute('data-id', dragSrcEl.getAttribute('data-id'));

                dragSrcEl.innerHTML = tempHTML;
                dragSrcEl.setAttribute('data-id', tempId);

                saveOrder();
            }
            return false;
        }

        function dragEnd(e) {
            document.querySelectorAll('#questionTableBody tr').forEach(row => {
                row.classList.remove('opacity-40');
            });
        }

        document.getElementById('questionTableBody').addEventListener('dragend', dragEnd);

        function saveOrder() {
            const rows = document.querySelectorAll('#questionTableBody tr');
            const order = [];
            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                if (id) order.push(parseInt(id));
            });

            fetch('{{ route("questions.reorder", $survey) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ order })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log('Order updated successfully!');
                }
            });
        }

        // Modal Option Builder Logic
        const modalTypeSelect = document.getElementById('modalQuestionType');
        const modalOptionsContainer = document.getElementById('modalOptionsContainer');
        const modalOptionsTbody = document.getElementById('modalOptionsTableBody');

        modalTypeSelect.addEventListener('change', function() {
            if (this.value === 'text') {
                modalOptionsContainer.classList.add('hidden');
            } else {
                modalOptionsContainer.classList.remove('hidden');
                if (modalOptionsTbody.children.length === 0) {
                    addModalOptionRow();
                }
            }
        });

        function addModalOptionRow() {
            const index = modalOptionsTbody.children.length;
            const tr = document.createElement('tr');
            tr.className = 'border-b border-slate-100 hover:bg-slate-50/20 transition';
            tr.innerHTML = `
                <td class="py-3 px-4">
                    <input type="text" name="options[${index}][label][en]" required class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300">
                </td>
                <td class="py-3 px-4">
                    <input type="text" name="options[${index}][label][ar]" required class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300">
                </td>
                <td class="py-3 px-4">
                    <input type="text" name="options[${index}][value]" required class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300">
                </td>
                <td class="py-3 px-4">
                    <input type="number" name="options[${index}][score]" required class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300">
                </td>
                <td class="py-3 px-4">
                    <input type="text" name="options[${index}][icon]" class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300 text-center font-emoji">
                </td>
                <td class="py-3 px-4">
                    <input type="text" name="options[${index}][color]" class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300 font-mono">
                </td>
                <td class="py-3 px-4 text-right">
                    <button type="button" onclick="removeModalOptionRow(this)" class="text-red-500 hover:text-red-700 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </td>
            `;
            modalOptionsTbody.appendChild(tr);
        }

        function removeModalOptionRow(btn) {
            const tr = btn.closest('tr');
            tr.remove();
            
            const rows = modalOptionsTbody.querySelectorAll('tr');
            rows.forEach((row, rIndex) => {
                row.querySelectorAll('input').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/options\[\d+\]/, `options[${rIndex}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }

        // Initialize with one row on modal display if not 'text'
        document.addEventListener('DOMContentLoaded', () => {
            if (modalTypeSelect.value !== 'text' && modalOptionsTbody.children.length === 0) {
                addModalOptionRow();
            }
        });
    </script>
@endsection
