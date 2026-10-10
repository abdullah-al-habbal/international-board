{{-- resources/views/web/certifications/_search_section.blade.php --}}
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h3 class="mb-4">{{ __('web.pages.certifications.search_title') }}</h3>
                <form action="{{ route('web.certifications.search') }}" method="GET">
                    <div class="input-group input-group-lg">
                        <input
                            type="text"
                            name="accreditation_number"
                            class="form-control"
                            placeholder="{{ __('web.pages.certifications.search_placeholder') }}"
                            value="{{ $accreditationNumber ?? '' }}"
                            autocomplete="off"
                            maxlength="100"
                        >
                        <button type="submit" class="btn btn-main">
                            {{ __('web.buttons.verify') }}
                        </button>
                    </div>
                    @if(isset($notFound) && $notFound)
                        <div class="mt-3 text-danger small">
                            <i class="tf-ion-ios-close-outline"></i>
                            {{ __('web.pages.certifications.not_found_message', ['accreditation_number' => e($accreditationNumber)]) }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</section>
