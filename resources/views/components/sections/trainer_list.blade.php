<!-- resources\views\components\sections\trainer_list.blade.php -->
@forelse ($trainers as $trainer)
    <div class="col-md-4 col-sm-6 mb-4">
        <div class="team-item text-center">
            <div class="team-img">
                <img loading="lazy" src="{{ $trainer->avatar_url ?? asset('assets/website/images/about/member.jpg') }}"
                    class="img-fluid" alt="{{ $trainer->name }}">
            </div>
            <div class="team-info">
                <h4>{{ $trainer->name }}</h4>
                @if ($trainer->country)
                    <span>{{ $trainer->country->name }}</span>
                @endif

                <a href="{{ route('web.trainers.show', $trainer->id) }}" class="btn btn-main btn-sm mt-2">
                    {{ __('web.buttons.view_details') }}
                </a>
            </div>
        </div>
    </div>
@empty
    @include('components.partials.empty_state')
@endforelse