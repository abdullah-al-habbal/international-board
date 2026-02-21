@if ($isBlocked)
    <div class="rounded-xl border border-warning-200 bg-warning-50 p-4 dark:border-warning-700 dark:bg-warning-900/20">
        <div class="flex items-start gap-3">
            <x-heroicon-o-exclamation-triangle class="h-5 w-5 shrink-0 text-warning-600 dark:text-warning-400" />
            <div>
                <p class="text-sm font-semibold text-warning-800 dark:text-warning-300">
                    {{ __('accreditation.banner.title') }}
                </p>
                <p class="mt-1 text-sm text-warning-700 dark:text-warning-400">
                    {{ $blockReason }}
                </p>
            </div>
        </div>
    </div>
@endif