<!-- resources\views\web\trainers\_profile.blade.php -->
<div class="card card-glow p-4">
    <div class="card-body p-0">
        <h3 class="mb-4">{{ $trainer->name }}</h3>
        
        <div class="row mb-3">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.country') }}:</div>
            <div class="col-sm-8">
                @if ($trainer->country)
                    <span class="badge bg-light text-dark border">{{ $trainer->country->name }}</span>
                @else
                    <span class="text-muted">{{ __('web.labels.not_assigned') }}</span>
                @endif
            </div>
        </div>

        @if ($trainer->bio)
            <div class="mt-4">
                <h5 class="mb-2">{{ __('filament.labels.bio') ?? 'Biography' }}</h5>
                <p class="text-muted">{{ $trainer->bio }}</p>
            </div>
        @endif

        @if ($trainer->specializations->isNotEmpty())
            <h5 class="mt-4 mb-3">{{ __('web.labels.specializations') }}</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($trainer->specializations as $spec)
                    <span class="badge bg-info text-dark px-3 py-2">{{ $spec->getTranslation('name', app()->getLocale()) }}</span>
                @endforeach
            </div>
        @endif

        @if ($trainer->certifications->isNotEmpty())
            <div class="mt-4 p-3 bg-light rounded d-flex align-items-center justify-content-between">
                <span class="fw-bold">{{ __('web.labels.certifications_count') }}:</span>
                <span class="badge bg-success fs-6">{{ $trainer->certifications->count() }}</span>
            </div>
        @endif

        <div class="mt-5">
            <a href="{{ route('web.trainers.index') }}" class="btn btn-main">
                <i class="tf-ion-ios-arrow-back mr-1"></i> {{ __('web.buttons.back') }}
            </a>
        </div>
    </div>
</div>