<!-- resources\views\web\trainers\_profile.blade.php -->
<div class="card card-glow p-4">
    <div class="card-body p-0">
        <h3 class="mb-4">{{ $trainer->name }}</h3>

        {{-- Basic info grid --}}
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.email') }}:</div>
            <div class="col-sm-8">{{ $trainer->email ?? '—' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.phone') }}:</div>
            <div class="col-sm-8">{{ $trainer->phone ?? '—' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.address') }}:</div>
            <div class="col-sm-8">
                @if(is_array($trainer->address))
                    {{ implode(', ', array_filter($trainer->address)) ?: '—' }}
                @else
                    {{ $trainer->address ?? '—' }}
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.country') }}:</div>
            <div class="col-sm-8">
                @if ($trainer->country)
                    <span class="badge bg-light text-dark border">{{ $trainer->country->name }}</span>
                @else
                    <span class="text-muted">{{ __('web.labels.not_assigned') }}</span>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.center') }}:</div>
            <div class="col-sm-8">
                @if ($trainer->center)
                    <span class="badge bg-light text-dark border">{{ $trainer->center->name }}</span>
                @else
                    <span class="text-muted">{{ __('web.labels.not_assigned') }}</span>
                @endif
            </div>
        </div>
        @if ($trainer->trainerRole)
            <div class="row mb-2">
                <div class="col-sm-4 fw-bold">{{ __('web.labels.trainer_role') }}:</div>
                <div class="col-sm-8">
                    <span class="badge bg-light text-dark border">{{ $trainer->trainerRole->name }}</span>
                </div>
            </div>
        @endif

        {{-- Accreditation details --}}
        <hr>
        <h5 class="mb-3">{{ __('web.labels.accreditation_details') }}</h5>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.accreditation_number') }}:</div>
            <div class="col-sm-8">{{ $trainer->accreditation_number ?? '—' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.accreditation_period') }}:</div>
            <div class="col-sm-8">
                @if($trainer->accreditation_period_start && $trainer->accreditation_period_end)
                    {{ $trainer->accreditation_period_start->format('Y-m-d') }}
                    <span class="mx-1">&rarr;</span>
                    {{ $trainer->accreditation_period_end->format('Y-m-d') }}
                    @if($trainer->isAccreditationActive())
                        <span class="badge bg-success ms-2">{{ __('web.labels.active') }}</span>
                    @else
                        <span class="badge bg-danger ms-2">{{ __('web.labels.expired') }}</span>
                    @endif
                @else
                    <span class="text-muted">&mdash;</span>
                @endif
            </div>
        </div>

        {{-- Biography --}}
        @if ($trainer->bio)
            <hr>
            <h5 class="mb-2">{{ __('filament.labels.bio') }}</h5>
            <p class="text-muted">{{ $trainer->bio }}</p>
        @endif

        {{-- Specializations --}}
        @if ($trainer->specializations->isNotEmpty())
            <hr>
            <h5 class="mb-3">{{ __('web.labels.specializations') }}</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($trainer->specializations as $spec)
                    <span class="badge bg-info text-dark px-3 py-2">{{ $spec->getTranslation('name', app()->getLocale()) }}</span>
                @endforeach
            </div>
        @endif

        {{-- Counts --}}
        <hr>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded text-center">
                    <div class="fw-bold">{{ __('web.labels.certifications_count') }}</div>
                    <span class="badge bg-success fs-6">{{ $trainer->certifications->count() }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded text-center">
                    <div class="fw-bold">{{ __('web.labels.document_types_count') }}</div>
                    <span class="badge bg-primary fs-6">{{ $trainer->documentTypes->count() }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded text-center">
                    <div class="fw-bold">{{ __('web.labels.accreditation_requests_count') }}</div>
                    <span class="badge bg-warning text-dark fs-6">{{ $trainer->accreditationRequests->count() }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded text-center">
                    <div class="fw-bold">{{ __('web.labels.specializations_count') }}</div>
                    <span class="badge bg-info fs-6">{{ $trainer->specializations->count() }}</span>
                </div>
            </div>
        </div>

        {{-- Back button --}}
        <div class="mt-5">
            <a href="{{ route('web.trainers.index') }}" class="btn btn-main">
                <i class="tf-ion-ios-arrow-back mr-1"></i> {{ __('web.buttons.back') }}
            </a>
        </div>
    </div>
</div>
