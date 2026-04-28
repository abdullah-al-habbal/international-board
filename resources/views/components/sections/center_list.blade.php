<!-- resources\views\components\sections\center_list.blade.php -->
@forelse ($centers as $center)
    <div class="col-md-4 col-sm-6 mb-4">
        <div class="team-item text-center">
            <div class="team-img">
                <img loading="lazy" src="{{ $center->logo_url ?? asset('assets/website/images/about/member.jpg') }}"
                    class="img-fluid" alt="{{ $center->name }}">
            </div>
            <div class="team-info">
                <h4>{{ $center->name }}</h4>
                @if ($center->country)
                    <span>{{ $center->country->name }}</span>
                @endif

                <a href="{{ route('web.centers.show', $center->id) }}" class="btn btn-main btn-sm mt-2">
                    {{ __('web.buttons.view_details') }}
                </a>
            </div>
        </div>
    </div>
@empty
    @include('components.partials.empty_state')
@endforelse