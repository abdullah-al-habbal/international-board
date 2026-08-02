{{-- resources/views/web/certifications/partials/certificate_body.blade.php --}}
<div class="certificate-body">
    <img
        class="certificate-logo"
        src="{{ asset($appSettings['site_logo_primary'] ?? 'assets/website/images/logo.png') }}"
        alt="{{ __('web.site_logo') }}"
        loading="lazy"
    >

    <div class="certificate-title">{{ __('web.certificate.title') }}</div>

    <p class="certificate-certifies">{{ __('web.certificate.certifies_that') }}</p>

    <p class="certificate-trainee">{{ $certification->trainee?->name ?? __('web.labels.not_assigned') }}</p>

    <p class="certificate-completed">{{ __('web.certificate.successfully_completed') }}</p>

    <p class="certificate-training-type">{{ $trainingType }}</p>

    @if ($certification->accreditation_number)
        <p class="certificate-number">{{ $certification->accreditation_number }}</p>
    @endif
</div>
