<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Import Instructions -->
        <x-filament::section>
            <x-slot name="heading">
                📋 Import Instructions
            </x-slot>
            <x-slot name="description">
                Follow these guidelines for successful data import
            </x-slot>

            <div class="prose dark:prose-invert max-w-none">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">📁 File Requirements</h4>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li>• Supported formats: .xlsx, .xls, .csv</li>
                            <li>• Maximum file size: 50MB</li>
                            <li>• First row must contain headers</li>
                            <li>• Arabic or English headers supported</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">🔧 Data Processing</h4>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li>• Automatic data cleaning and normalization</li>
                            <li>• Countries and trainers created automatically</li>
                            <li>• Duplicate entries handled gracefully</li>
                            <li>• Invalid dates and data skipped</li>
                        </ul>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Import Statistics -->
        <x-filament::section>
            <x-slot name="heading">
                📊 Database Statistics
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-filament::card>
                    <div class="text-center p-4">
                        <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2">
                            {{ \App\Models\Certification::count() }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Certifications</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ \App\Models\Certification::whereDate('created_at', today())->count() }} added today
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center p-4">
                        <div class="text-3xl font-bold text-success-600 dark:text-success-400 mb-2">
                            {{ \App\Models\Country::count() }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Countries</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ \App\Models\Country::where('is_active', true)->count() }} active
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center p-4">
                        <div class="text-3xl font-bold text-warning-600 dark:text-warning-400 mb-2">
                            {{ \App\Models\Trainer::count() }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Trainers</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ \App\Models\Trainer::where('is_active', true)->count() }} active
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center p-4">
                        <div class="text-3xl font-bold text-info-600 dark:text-info-400 mb-2">
                            {{ \App\Models\Certification::whereNotNull('country_id')->count() }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">With Countries</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ round((\App\Models\Certification::whereNotNull('country_id')->count() / max(\App\Models\Certification::count(), 1)) * 100, 1) }}% coverage
                        </div>
                    </div>
                </x-filament::card>
            </div>
        </x-filament::section>

        <!-- Recent Imports -->
        @if(\App\Models\Certification::latest()->limit(5)->exists())
        <x-filament::section>
            <x-slot name="heading">
                📋 Recent Certifications
            </x-slot>
            <x-slot name="description">
                Latest imported certifications
            </x-slot>

            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Trainee Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Document Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Serial Number
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Country
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Imported
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach(\App\Models\Certification::with(['country', 'trainer'])->latest()->limit(5)->get() as $certification)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $certification->trainee_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($certification->document_type)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $certification->document_type }}
                                </span>
                                @else
                                <span class="text-gray-400">Unknown</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">
                                {{ $certification->accredited_serial_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($certification->country)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ $certification->country->name }}
                                </span>
                                @else
                                <span class="text-gray-400">No country</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $certification->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
        @endif

        <!-- Data Quality Overview -->
        <x-filament::section>
            <x-slot name="heading">
                🔍 Data Quality Overview
            </x-slot>
            <x-slot name="description">
                Current data quality metrics
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Complete Records</p>
                            <p class="text-2xl font-semibold text-green-600 dark:text-green-400">
                                {{ \App\Models\Certification::whereNotNull('trainee_name')->whereNotNull('accredited_serial_number')->whereNotNull('accreditation_date')->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Missing Countries</p>
                            <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400">
                                {{ \App\Models\Certification::whereNull('country_id')->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Missing Trainers</p>
                            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">
                                {{ \App\Models\Certification::whereNull('trainer_id')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
