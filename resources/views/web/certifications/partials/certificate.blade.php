{{-- resources/views/web/certifications/partials/certificate.blade.php --}}
<section class="section certificate-section" aria-label="{{ __('web.certificate.certificate_label') }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-10">
                <article class="certificate-card">
                    @include('web.certifications.partials.certificate_header')
                    @include('web.certifications.partials.certificate_body')
                    @include('web.certifications.partials.certificate_meta')

                    @if (filled($certification->notes))
                        @include('web.certifications.partials.certificate_notes')
                    @endif

                    @if (filled($qrSvg ?? null))
                        @include('web.certifications.partials.certificate_qr')
                    @endif

                    @include('web.certifications.partials.certificate_actions')
                </article>
            </div>
        </div>
    </div>
</section>
