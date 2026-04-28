<!-- resources\views\web\centers\_details.blade.php -->
<h3>{{ $center->name }}</h3>

@if ($center->address)
    <p><i class="tf-ion-ios-location-outline"></i> {{ $center->address }}</p>
@endif

@if ($center->phone)
    <p><i class="tf-ion-ios-telephone-outline"></i> {{ $center->phone }}</p>
@endif

@if ($center->country)
    <p><i class="tf-ion-ios-world-outline"></i> {{ $center->country->name }}</p>
@endif

@if ($center->approvedDocumentTypes->isNotEmpty())
    <h5 class="mt-4">{{ __('web.labels.document_types') }}</h5>
    <ul>
        @foreach ($center->approvedDocumentTypes as $pivot)
            @if ($pivot->is_published && $pivot->documentType)
                <li>{{ $pivot->documentType->name }}</li>
            @endif
        @endforeach
    </ul>
@endif

<a href="{{ route('web.centers.index') }}" class="btn btn-outline-secondary mt-4">
    {{ __('web.buttons.back') }}
</a>