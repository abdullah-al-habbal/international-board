{{-- resources/views/web/certifications/_result.blade.php --}}
@php

    use App\Models\CertifiedCenter;
    use App\Models\Trainer;
    use App\Models\User;

    $creatorLabel = match ($certification->creator_type) {
        User::class => __('web.labels.board'),
        CertifiedCenter::class => __('web.labels.center'),
        Trainer::class => __('web.labels.trainer'),
        default => __('web.labels.unknown'),
    };

    $documentable = $certification->documentable;
    $documentableNames = $documentable ? $documentable->getTranslations('name') : [];
    $trainingType = $documentableNames[app()->getLocale()] ?? $documentableNames['en'] ?? '—';

    $trainerName = $certification->assignedTrainer?->name
        ?? ($certification->creator_type === Trainer::class ? $certification->creator?->name : null);

    $accreditationDate = $certification->accreditation_date
        ? \Carbon\Carbon::parse($certification->accreditation_date)
        : null;
@endphp

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/website/css/certificate.css') }}">
@endpush

@include('web.certifications.partials.certificate')
