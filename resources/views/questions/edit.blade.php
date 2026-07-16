@extends('layouts.admin')

@section('title', 'Edit Question')
@section('header_title', 'Edit Survey Question')

@section('content')
    <div class="max-w-4xl mx-auto space-y-8 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <h3 class="text-lg font-bold text-slate-800">Edit Question Details</h3>
                <a href="{{ route('surveys.show', $question->survey_id) }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800">
                    &larr; Back to Survey
                </a>
            </div>

            <form action="{{ route('questions.update', $question) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">English Question Text</label>
                        <input type="text" name="text[en]" value="{{ $question->text['en'] }}" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Arabic Question Text</label>
                        <input type="text" name="text[ar]" value="{{ $question->text['ar'] }}" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">English Description / Instruction (Optional)</label>
                        <input type="text" name="description[en]" value="{{ $question->description['en'] ?? '' }}" 
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Arabic Description / Instruction (Optional)</label>
                        <input type="text" name="description[ar]" value="{{ $question->description['ar'] ?? '' }}" 
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Question Code (e.g., D1, Q1)</label>
                        <input type="text" name="settings[code]" value="{{ $question->settings['code'] ?? '' }}" 
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Quiz Section (English)</label>
                        <input type="text" name="settings[section]" value="{{ $question->settings['section'] ?? '' }}" 
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Quiz Section (Arabic)</label>
                        <input type="text" name="settings[section_ar]" value="{{ $question->settings['section_ar'] ?? '' }}" 
                               class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Question Type</label>
                        <select name="type" id="questionType" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white">
                            <option value="single_choice" {{ $question->type == 'single_choice' ? 'selected' : '' }}>Single Choice</option>
                            <option value="rating" {{ $question->type == 'rating' ? 'selected' : '' }}>Rating (Stars/Emoji)</option>
                            <option value="yes_no" {{ $question->type == 'yes_no' ? 'selected' : '' }}>Yes / No</option>
                            <option value="text" {{ $question->type == 'text' ? 'selected' : '' }}>Open Text Feedback</option>
                        </select>
                    </div>
                    <div class="flex items-center pt-8">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="is_required" value="1" {{ $question->is_required ? 'checked' : '' }} class="rounded text-blue-600">
                            <span class="text-sm text-slate-700 font-semibold">Is Required (mandatory to answer)</span>
                        </label>
                    </div>
                </div>

                <!-- Answer Options Management -->
                <div id="optionsContainer" class="border-t border-slate-100 pt-6 {{ $question->type == 'text' ? 'hidden' : '' }}">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-md font-bold text-slate-800">Answer Options Configuration</h4>
                            <p class="text-xs text-slate-500 mt-1">Tick exactly one correct answer.</p>
                        </div>
                        <button type="button" onclick="addOptionRow()" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition cursor-pointer">
                            + Add Option Row
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-slate-100 rounded-xl shadow-inner">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold border-b border-slate-100">
                                    <th class="py-3 px-4">Label (EN)</th>
                                    <th class="py-3 px-4">Label (AR)</th>
                                    <th class="py-3 px-4 w-24">Value</th>
                                    <th class="py-3 px-4 w-20">Correct?</th>
                                    <th class="py-3 px-4 w-20">Icon (Emoji)</th>
                                    <th class="py-3 px-4 w-24">Color (Hex)</th>
                                    <th class="py-3 px-4 w-12 text-right"></th>
                                </tr>
                            </thead>
                            <tbody id="optionsTableBody">
                                @foreach($question->answerOptions as $index => $option)
                                    <tr class="border-b border-slate-100 hover:bg-slate-50/20 transition">
                                        <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option->id }}">
                                        <td class="py-3 px-4">
                                            <input type="text" name="options[{{ $index }}][label][en]" value="{{ $option->label['en'] }}" required 
                                                   class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300">
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="text" name="options[{{ $index }}][label][ar]" value="{{ $option->label['ar'] }}" required 
                                                   class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300">
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="text" name="options[{{ $index }}][value]" value="{{ $option->value }}" required 
                                                   class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300">
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="checkbox" name="options[{{ $index }}][is_correct]" value="1" {{ $option->is_correct ? 'checked' : '' }}
                                                   class="h-5 w-5 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="text" name="options[{{ $index }}][icon]" value="{{ $option->icon }}" 
                                                   class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300 text-center">
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="text" name="options[{{ $index }}][color]" value="{{ $option->color }}" 
                                                   class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300 font-mono">
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <button type="button" onclick="removeOptionRow(this)" class="text-red-500 hover:text-red-700 p-1 cursor-pointer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6 flex space-x-4">
                    <a href="{{ route('surveys.show', $question->survey_id) }}" 
                       class="flex-1 py-3 text-center border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md transition cursor-pointer">
                        Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const typeSelect = document.getElementById('questionType');
        const container = document.getElementById('optionsContainer');
        const tbody = document.getElementById('optionsTableBody');

        typeSelect.addEventListener('change', function() {
            if (this.value === 'text') {
                container.classList.add('hidden');
            } else {
                container.classList.remove('hidden');
                if (tbody.children.length === 0) {
                    addOptionRow();
                }
            }
        });

        function addOptionRow() {
            const index = tbody.children.length;
            const tr = document.createElement('tr');
            tr.className = 'border-b border-slate-100 hover:bg-slate-50/20 transition';
            tr.innerHTML = `
                <td class="py-3 px-4">
                    <input type="text" name="options[\${index}][label][en]" required class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300">
                </td>
                <td class="py-3 px-4">
                    <input type="text" name="options[\${index}][label][ar]" required class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300">
                </td>
                <td class="py-3 px-4">
                    <input type="text" name="options[\${index}][value]" required class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300">
                </td>
                <td class="py-3 px-4">
                    <input type="checkbox" name="options[\${index}][is_correct]" value="1" class="h-5 w-5 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                </td>
                <td class="py-3 px-4">
                    <input type="text" name="options[\${index}][icon]" class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300 text-center font-emoji">
                </td>
                <td class="py-3 px-4">
                    <input type="text" name="options[\${index}][color]" class="w-full px-2.5 py-1.5 text-sm rounded border border-slate-300 font-mono">
                </td>
                <td class="py-3 px-4 text-right">
                    <button type="button" onclick="removeOptionRow(this)" class="text-red-500 hover:text-red-700 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        }

        function removeOptionRow(btn) {
            const tr = btn.closest('tr');
            tr.remove();
            
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((row, rIndex) => {
                row.querySelectorAll('input').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/options\[\d+\]/, `options[\${rIndex}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }
    </script>
@endsection
