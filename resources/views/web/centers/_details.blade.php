<!-- resources\views\web\centers\_details.blade.php -->
<div class="card p-4">
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

        @if ($center->address)
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">{{ __('filament.labels.address') ?? 'Address' }}:</div>
                <div class="col-sm-8"><i class="tf-ion-ios-location-outline mr-2 text-primary"></i> {{ $center->address }}</div>
            </div>
        @endif

        @if ($center->phone)
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">{{ __('filament.labels.phone') ?? 'Phone' }}:</div>
                <div class="col-sm-8"><i class="tf-ion-ios-telephone-outline mr-2 text-primary"></i> {{ $center->phone }}</div>
            </div>
        @endif

        @if ($center->approvedDocumentTypes->isNotEmpty())
            <h5 class="mt-4 mb-3">{{ __('web.labels.document_types') }}</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($center->approvedDocumentTypes as $pivot)
                    @if ($pivot->is_published && $pivot->documentType)
                        <span class="badge bg-primary px-3 py-2">{{ $pivot->documentType->name }}</span>
                    @endif
                @endforeach
            </div>
        @endif

        <div class="mt-5">
            <a href="{{ route('web.centers.index') }}" class="btn btn-main">
                <i class="tf-ion-ios-arrow-back mr-1"></i> {{ __('web.buttons.back') }}
            </a>
        </div>
    </div>
</div>