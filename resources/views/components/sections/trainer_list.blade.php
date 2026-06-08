<!-- resources\views\components\sections\trainer_list.blade.php -->
@forelse ($trainers as $trainer)
    <div class="col-lg-4 col-md-6 col-sm-6 mb-5">
        <div class="card card-glow h-100 text-center p-4">
            <div class="img-square-wrap mb-4">
                <img loading="lazy" src="{{ $trainer->avatar_url ?? asset('assets/website/images/avatar.png') }}"
                    class="img-square" alt="{{ $trainer->name }}">
            </div>
            <div class="card-body p-0">
                <h4 class="card-title mb-2">{{ $trainer->name }}</h4>
                @if ($trainer->country)
                    <p class="text-muted mb-3">
                        <i class="tf-ion-ios-location-outline mr-1"></i>
                        {{ $trainer->country->name }}
                    </p>
                @endif

                <a href="{{ route('web.trainers.show', $trainer->id) }}" class="btn btn-main btn-sm">
                    {{ __('web.buttons.view_details') }}
                </a>
            </div>
        </div>
    </div>
@empty
    @include('components.partials.empty_state')
@endforelse