{{-- resources/views/web/certifications/partials/certificate_qr.blade.php --}}
@php
    $verificationUrl = route('web.certifications.show', $certification->accreditation_number);
@endphp

<div class="certificate-qr">
    <div class="certificate-qr-code" role="img" aria-label="{{ __('web.certificate.qr_aria') }}">
        {!! $qrSvg !!}
    </div>
    <div class="certificate-qr-info">
        <p class="certificate-qr-title">{{ __('web.certificate.verify_authenticity') }}</p>
        <a class="certificate-qr-link" href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
    </div>
</div>
