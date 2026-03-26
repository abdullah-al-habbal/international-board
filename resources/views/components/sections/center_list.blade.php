<div class="row">
    @foreach ($centers as $center)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="{{ $center->logo_url ?? asset('assets/website/images/about/member.jpg') }}" class="card-img-top" alt="{{ $center->name }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $center->name }}</h5>
                    <p class="card-text">{{ $center->address }}</p>
                    <a href="{{ route('web.centers.show', $center->id) }}" class="btn btn-main btn-sm">
                        {{ __('web.buttons.read_more') }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
