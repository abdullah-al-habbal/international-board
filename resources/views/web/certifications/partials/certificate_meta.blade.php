{{-- resources/views/web/certifications/partials/certificate_meta.blade.php --}}
<div class="certificate-meta">
    <dl class="certificate-meta-grid">
        <div class="certificate-meta-item">
            <dt>{{ __('web.labels.assigned_trainer') }}</dt>
            <dd>{{ $trainerName ?: __('web.labels.not_assigned') }}</dd>
        </div>
        <div class="certificate-meta-item">
            <dt>{{ __('web.labels.country') }}</dt>
            <dd>{{ $certification->country?->name ?? __('web.labels.not_assigned') }}</dd>
        </div>
        <div class="certificate-meta-item">
            <dt>{{ __('web.labels.issued_by') }}</dt>
            <dd>
                @if ($certification->creator)
                    {{ $creatorLabel }}: {{ $certification->creator->name }}
                @else
                    {{ __('web.labels.not_assigned') }}
                @endif
            </dd>
        </div>
        <div class="certificate-meta-item">
            <dt>{{ __('web.labels.issue_date') }}</dt>
            <dd>{{ $accreditationDate ? $accreditationDate->format('Y-m-d') : __('web.labels.not_assigned') }}</dd>
        </div>
    </dl>
</div>
