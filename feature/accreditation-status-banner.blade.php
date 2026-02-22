{{-- filePath: resources/views/filament/center/widgets/accreditation-status-banner.blade.php --}}
@if ($blocked)
    <div class="rounded-xl border border-danger-200 bg-danger-50 p-4 dark:border-danger-700 dark:bg-danger-950">
        <div class="flex items-start gap-3">
            <x-heroicon-o-exclamation-triangle class="mt-0.5 h-5 w-5 shrink-0 text-danger-600 dark:text-danger-400" />
            <div class="flex-1">
                <p class="text-sm font-semibold text-danger-800 dark:text-danger-200">
                    {{ __('accreditation.blocked.title') }}
                </p>
                <p class="mt-1 text-sm text-danger-700 dark:text-danger-300">
                    {{ $blockReason }}
                </p>
                @unless ($hasPending)
                    <a href="{{ \App\Filament\Center\Resources\AccreditationRequests\AccreditationRequestResource::getUrl('create') }}"
                        class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-danger-700 underline hover:text-danger-900 dark:text-danger-300">
                        {{ __('accreditation.submit_new_request') }}
                        <x-heroicon-m-arrow-right class="h-3.5 w-3.5" />
                    </a>
                @else
                    <p class="mt-2 text-xs text-danger-600 dark:text-danger-400">
                        {{ __('accreditation.request_pending_note') }}
                    </p>
                @endunless
            </div>
        </div>
    </div>
@elseif ($hasActive)
    <div class="rounded-xl border border-success-200 bg-success-50 p-4 dark:border-success-700 dark:bg-success-950">
        <div class="flex items-center gap-3">
            <x-heroicon-o-check-badge class="h-5 w-5 shrink-0 text-success-600 dark:text-success-400" />
            <p class="text-sm font-medium text-success-800 dark:text-success-200">
                {{ __('accreditation.active_banner') }}
            </p>
        </div>
    </div>
@endif