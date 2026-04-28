<!-- resources\views\components\sections\cta_section.blade.php -->
<section class="call-to-action section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 text-center">
                <h2>{{ __('web.cta.title') }}</h2>
                <p>{{ __('web.cta.text') }}</p>
                <a href="{{ route('web.certifications.index') }}" class="btn btn-main">
                    {{ __('web.cta.button') }}
                </a>
            </div>
        </div>
    </div>
</section>