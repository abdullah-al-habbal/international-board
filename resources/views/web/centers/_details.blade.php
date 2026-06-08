<div class="card card-glow p-4">
    <div class="card-body p-0">
        <h3 class="mb-4">{{ $center->name }}</h3>

        <div class="row mb-3">
            <div class="col-sm-4 fw-bold">{{ __('web.labels.country') }}:</div>
            <div class="col-sm-8">
                @if ($center->country)
                    <span class="badge bg-light text-dark border">{{ $center->country->name }}</span>
                @else
                    <span class="text-muted">{{ __('web.labels.not_assigned') }}</span>
                @endif
            </div>
        </div>

        @if ($center->accreditation_number)
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">{{ __('web.labels.accreditation_number') }}:</div>
                <div class="col-sm-8"><code>{{ $center->accreditation_number }}</code></div>
            </div>
        @endif

        @if ($center->accreditation_period_start && $center->accreditation_period_end)
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">{{ __('web.labels.accreditation_period') }}:</div>
                <div class="col-sm-8">
                    {{ $center->accreditation_period_start->format('M d, Y') }} – {{ $center->accreditation_period_end->format('M d, Y') }}
                    @if ($center->isAccreditationActive())
                        <span class="badge bg-success ms-2">{{ __('web.labels.active') }}</span>
                    @else
                        <span class="badge bg-danger ms-2">{{ __('web.labels.expired') }}</span>
                    @endif
                </div>
            </div>
        @endif

        @if ($center->email)
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">{{ __('web.labels.email') }}:</div>
                <div class="col-sm-8">
                    <a href="mailto:{{ $center->email }}" class="text-primary">{{ $center->email }}</a>
                </div>
            </div>
        @endif

        @if ($center->manager_name)
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">{{ __('web.labels.manager_name') }}:</div>
                <div class="col-sm-8">{{ $center->manager_name }}</div>
            </div>
        @endif

        @if ($center->address)
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">{{ __('app.address') }}:</div>
                <div class="col-sm-8"><i class="tf-ion-ios-location-outline mr-2 text-primary"></i> {{ $center->address }}</div>
            </div>
        @endif

        @if ($center->phone)
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">{{ __('app.phone') }}:</div>
                <div class="col-sm-8"><i class="tf-ion-ios-telephone-outline mr-2 text-primary"></i> {{ $center->phone }}</div>
            </div>
        @endif

        @if ($center->approvedDocumentTypes->isNotEmpty())
            <h5 class="mt-4 mb-3">{{ __('web.labels.document_types') }}</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($center->approvedDocumentTypes as $docType)
                    <span class="badge bg-primary px-3 py-2">{{ $docType->name }}</span>
                @endforeach
            </div>
        @endif

        @if ($center->certifications->isNotEmpty())
            <h5 class="mt-4 mb-3">{{ __('web.labels.certifications') }}</h5>
            <ul class="list-group list-group-flush">
                @foreach ($center->certifications as $certification)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            @if ($certification->documentType)
                                {{ $certification->documentType->name }}
                            @else
                                {{ __('web.labels.certification') }} #{{ $certification->id }}
                            @endif
                        </span>
                        @if ($certification->document_code)
                            <span class="badge bg-secondary">{{ $certification->document_code }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="mt-5">
            <a href="{{ route('web.centers.index') }}" class="btn btn-main">
                <i class="tf-ion-ios-arrow-back mr-1"></i> {{ __('web.buttons.back') }}
            </a>
        </div>
    </div>
</div>
