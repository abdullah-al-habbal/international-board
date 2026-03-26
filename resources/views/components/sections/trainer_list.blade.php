<div class="row">
    @foreach ($trainers as $trainer)
        <div class="col-md-3 mb-4">
            <div class="card h-100 text-center">
                <img src="{{ $trainer->avatar_url ?? asset('assets/website/images/about/member.jpg') }}" class="card-img-top" alt="{{ $trainer->name }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $trainer->name }}</h5>
                    <p class="card-text text-muted">{{ $trainer->country?->name }}</p>
                    <a href="{{ route('web.trainers.show', $trainer->id) }}" class="btn btn-main btn-sm">
                        {{ __('web.buttons.read_more') }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
