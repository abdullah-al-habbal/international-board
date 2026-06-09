{{-- resources/views/filament/center/widgets/accreditation-status-banner.blade.php --}}
@php
    $center = $this->getCenter();
    $isAccredited = $center->canPerformActions();
    $reason = $center->accreditationBlockReason();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-4">
            <div @class([
                'flex h-12 w-12 items-center justify-center rounded-full',
                'bg-success-500/10 text-success-500' => $isAccredited,
                'bg-danger-500/10 text-danger-500' => !$isAccredited,
            ])>
                <x-filament::icon :icon="$isAccredited ? 'heroicon-o-check-badge' : 'heroicon-o-exclamation-triangle'"
                    class="h-7 w-7" />
            </div>

            <div class="flex-1">
                <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    {{ __('accreditation.banner.title') }}
                </h3>

                <p @class([
                    'text-sm font-medium',
                    'text-success-600 dark:text-success-400' => $isAccredited,
                    'text-danger-600 dark:text-danger-400' => !$isAccredited,
                ])>
                    {{ $isAccredited ? __('accreditation.banner.active') : __('accreditation.banner.blocked') }}
                </p>

                @if(!$isAccredited && $reason)
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ $reason }}
                    </p>
                @endif
            </div>

            @if(!$isAccredited)
                <div>
                    <x-filament::button :href="route('filament.center.resources.center-accreditation-requests.index')" tag="a"
                        color="primary" size="sm">
                        {{ __('accreditation.banner.action') }}
                    </x-filament::button>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>