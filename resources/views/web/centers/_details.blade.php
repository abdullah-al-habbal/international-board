<!-- resources\views\web\centers\_details.blade.php -->
<div class="card card-glow p-4">
    <div class="card-body p-0">
        <h3 class="mb-4">{{ $center->name }}</h3>

        {{-- Basic info grid --}}
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.email') }}:</div>
            <div class="col-sm-8">
                <a href="mailto:{{ $center->email }}" class="text-primary">{{ $center->email }}</a>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('app.phone') }}:</div>
            <div class="col-sm-8">{{ $center->phone ?? '—' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('app.address') }}:</div>
            <div class="col-sm-8">
                @if($center->address)
                    <i class="tf-ion-ios-location-outline mr-1 text-primary"></i> {{ $center->address }}
                @else
                    <span class="text-muted">&mdash;</span>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.manager_name') }}:</div>
            <div class="col-sm-8">{{ $center->manager_name ?? '—' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.country') }}:</div>
            <div class="col-sm-8">
                @if ($center->country)
                    <span class="badge bg-light text-dark border">{{ $center->country->name }}</span>
                @else
                    <span class="text-muted">{{ __('web.labels.not_assigned') }}</span>
                @endif
            </div>
        </div>

        {{-- Accreditation details --}}
        <hr>
        <h5 class="mb-3">{{ __('web.labels.accreditation_details') }}</h5>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.accreditation_number') }}:</div>
            <div class="col-sm-8">
                @if($center->accreditation_number)
                    <code>{{ $center->accreditation_number }}</code>
                @else
                    <span class="text-muted">&mdash;</span>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.accreditation_period') }}:</div>
            <div class="col-sm-8">
                @if($center->accreditation_period_start && $center->accreditation_period_end)
                    {{ $center->accreditation_period_start->format('M d, Y') }} &ndash; {{ $center->accreditation_period_end->format('M d, Y') }}
                    @if ($center->isAccreditationActive())
                        <span class="badge bg-success ms-2">{{ __('web.labels.active') }}</span>
                    @else
                        <span class="badge bg-danger ms-2">{{ __('web.labels.expired') }}</span>
                    @endif
                @else
                    <span class="text-muted">&mdash;</span>
                @endif
            </div>
        </div>

        {{-- Approved Document Types --}}
        @if($center->approvedDocumentTypes->isNotEmpty())
            <hr>
            <h5 class="mb-3">{{ __('web.labels.document_types') }}</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach($center->approvedDocumentTypes as $docType)
                    <span class="badge bg-primary px-3 py-2">{{ $docType->name }}</span>
                @endforeach
            </div>
        @endif

        {{-- Relation counts --}}
        <hr>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded text-center">
                    <div class="fw-bold">{{ __('web.labels.certifications_count') }}</div>
                    <span class="badge bg-success fs-6">{{ $center->certifications->count() }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded text-center">
                    <div class="fw-bold">{{ __('web.labels.trainers_count') }}</div>
                    <span class="badge bg-primary fs-6">{{ $center->trainers->count() }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded text-center">
                    <div class="fw-bold">{{ __('web.labels.document_types_count') }}</div>
                    <span class="badge bg-warning text-dark fs-6">{{ $center->documentTypes->count() }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded text-center">
                    <div class="fw-bold">{{ __('web.labels.accreditation_requests_count') }}</div>
                    <span class="badge bg-info fs-6">{{ $center->accreditationRequests->count() }}</span>
                </div>
            </div>
            @if($center->financialRequests->isNotEmpty())
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded text-center">
                    <div class="fw-bold">{{ __('web.labels.financial_requests_count') }}</div>
                    <span class="badge bg-dark fs-6">{{ $center->financialRequests->count() }}</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Latest certifications --}}
        @if($center->certifications->isNotEmpty())
            <hr>
            <h5 class="mb-3">{{ __('web.labels.latest_certifications') }}</h5>
            <ul class="list-group list-group-flush">
                @foreach($center->certifications->take(5) as $cert)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ __('web.labels.certification') }} #{{ $cert->id }}</span>
                        @if($cert->document_code)
                            <span class="badge bg-secondary">{{ $cert->document_code }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- Back button --}}
        <div class="mt-5">
            <a href="{{ route('web.centers.index') }}" class="btn btn-main">
                <i class="tf-ion-ios-arrow-back mr-1"></i> {{ __('web.buttons.back') }}
            </a>
        </div>
    </div>
</div>
