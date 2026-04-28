<!-- resources\views\web\centers\_filters.blade.php -->
<form action="{{ route('web.centers.index') }}" method="GET" class="mb-5">
    <div class="row align-items-end">
        <div class="col-md-8">
            <input type="text" name="search" class="form-control"
                placeholder="{{ __('web.labels.search_placeholder') }}" value="{{ request('search') }}">
        </div>
        <div class="col-md-2 mt-2 mt-md-0">
            <button type="submit" class="btn btn-main btn-block">
                {{ __('web.buttons.search') }}
            </button>
        </div>
        <div class="col-md-2 mt-2 mt-md-0">
            @if (request()->hasAny(['search', 'country_id']))
                <a href="{{ route('web.centers.index') }}" class="btn btn-outline-secondary btn-block">
                    {{ __('web.buttons.clear') }}
                </a>
            @endif
        </div>
    </div>
</form>