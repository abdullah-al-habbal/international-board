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
    $trainingType = $documentable
        ? ($documentable->name[app()->getLocale()] ?? $documentable->name['en'] ?? '—')
        : '—';

    $trainerName = $certification->assignedTrainer?->name
        ?? ($certification->creator_type === Trainer::class ? $certification->creator?->name : null);
@endphp

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card card-glow border-success">
                    <div class="card-header bg-success text-white d-flex align-items-center gap-2">
                        <i class="tf-ion-ios-checkmark-outline fs-5"></i>
                        <span>{{ __('web.pages.certifications.result_valid') }}</span>
                    </div>
                    <div class="card-body">

                        <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                            <span class="fw-semibold fs-5">{{ __('web.labels.trainee') }}:</span>
                            <span class="badge bg-warning text-dark fs-6 d-inline-flex align-items-center gap-2 px-3 py-2 border border-dark rounded-pill">
                                <i class="tf-ion-ios-person-outline fs-5"></i>
                                <span>{{ $certification->trainee?->name ?? __('web.labels.not_assigned') }}</span>
                            </span>
                        </div>

                        <table class="table table-bordered align-middle">
                            <tbody>
                                <tr>
                                    <th class="w-35">{{ __('web.labels.training_type') }}</th>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 d-inline-flex align-items-center gap-2">
                                            <i class="tf-ion-ios-ribbon-outline"></i>
                                            <span>{{ $trainingType }}</span>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.training_info') }}</th>
                                    <td>{!! filled($certification->notes) ? nl2br(e($certification->notes)) : '<span class="text-muted">—</span>' !!}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.assigned_trainer') }}</th>
                                    <td>
                                        @if ($trainerName)
                                            <span class="badge bg-warning text-dark px-3 py-2 d-inline-flex align-items-center gap-2 rounded-pill">
                                                <i class="tf-ion-ios-person-outline"></i>
                                                <span>{{ $trainerName }}</span>
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.issued_by') }}</th>
                                    <td>
                                        @if ($certification->creator)
                                            <span class="badge border border-primary text-primary bg-primary bg-opacity-10 px-3 py-2 d-inline-flex align-items-center gap-2">
                                                <i class="tf-ion-ios-location-outline"></i>
                                                <span>{{ $creatorLabel }}: {{ $certification->creator->name }}</span>
                                            </span>
                                        @else
                                            <span class="text-muted">{{ __('web.labels.not_assigned') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.issue_date') }}</th>
                                    <td>
                                        @if ($certification->accreditation_date)
                                            @php $accreditationDate = \Carbon\Carbon::parse($certification->accreditation_date); @endphp
                                            <span class="d-block fw-semibold">{{ $accreditationDate->format('Y-m-d') }}</span>
                                            <small class="text-muted">{{ $accreditationDate->locale(app()->getLocale())->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.country') }}</th>
                                    <td>
                                        @if ($certification->country?->name)
                                            <span class="badge bg-light text-dark border px-3 py-2 d-inline-flex align-items-center gap-2 rounded-pill">
                                                <i class="tf-ion-ios-globe-outline"></i>
                                                <span>{{ $certification->country->name }}</span>
                                            </span>
                                        @else
                                            <span class="text-muted">{{ __('web.labels.not_assigned') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
