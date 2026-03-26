<div class="row">
    @foreach ($certifications as $cert)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $cert->title }}</h5>
                    <p class="card-text">{{ Str::limit($cert->description, 100) }}</p>
                    <a href="{{ route('web.certifications.show', $cert->code) }}" class="btn btn-main btn-sm">
                        {{ __('web.buttons.read_more') }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
