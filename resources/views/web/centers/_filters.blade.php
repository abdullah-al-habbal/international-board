<!-- resources\views\web\centers\_filters.blade.php -->
<form action="{{ route('web.centers.index') }}" method="GET" class="mb-5">
    <div class="row align-items-end g-2">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control"
                placeholder="{{ __('web.labels.search_placeholder') }}" value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="country_id" class="form-control">
                <option value="">{{ __('web.labels.all') }}</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}" @selected((string) request('country_id') === (string) $country->id)>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-main btn-block w-100">
                {{ __('web.buttons.search') }}
            </button>
        </div>
        <div class="col-md-2">
            @if (request()->hasAny(['search', 'country_id']))
                <a href="{{ route('web.centers.index') }}" class="btn btn-outline-secondary btn-block w-100">
                    {{ __('web.buttons.clear') }}
                </a>
            @endif
        </div>
    </div>
</form>
