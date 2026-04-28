<!-- resources\views\web\trainers\_profile.blade.php -->
<h3>{{ $trainer->name }}</h3>

@if ($trainer->country)
    <p><i class="tf-ion-ios-world-outline"></i> {{ $trainer->country->name }}</p>
@endif

@if ($trainer->bio)
    <p class="mt-3">{{ $trainer->bio }}</p>
@endif

@if (!empty($trainer->specializations))
    <h5 class="mt-4">{{ __('web.labels.specializations') }}</h5>
    <ul>
        @foreach ((array) $trainer->specializations as $spec)
            <li>{{ $spec }}</li>
        @endforeach
    </ul>
@endif

@if ($trainer->certifications->isNotEmpty())
    <p class="mt-3">
        <strong>{{ __('web.labels.certifications_count') }}:</strong>
        {{ $trainer->certifications->count() }}
    </p>
@endif

<a href="{{ route('web.trainers.index') }}" class="btn btn-outline-secondary mt-4">
    {{ __('web.buttons.back') }}
</a>