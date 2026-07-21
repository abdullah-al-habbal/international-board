@php
    use App\Models\CertifiedCenter;
    use App\Models\Trainer;
    use App\Models\User;
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

                        <h5 class="card-title mb-4">
                            {{ __('web.labels.trainee') }}:
                            <span class="badge bg-warning text-dark fs-5 p-2 px-3 border border-dark rounded-pill">
                                <i class="tf-ion-ios-person-outline mr-1"></i>
                                {{ $certification->trainee?->name ?? __('web.labels.not_assigned') }}
                            </span>
                        </h5>

                        <table class="table table-bordered align-middle">
                            <tbody>
                                <tr>
                                    <th class="w-35">{{ __('web.labels.issued_by') }}</th>
                                    <td>
                                        @php
                                            $creatorLabel = match ($certification->creator_type) {
                                                User::class => __('web.labels.board'),
                                                CertifiedCenter::class => __('web.labels.center'),
                                                Trainer::class => __('web.labels.tra    iner'),
                                                default => __('web.labels.unknown'),
                                            };
                                        @endphp
                                        @if($certification->creator)
                                            <span class="badge bg-dark px-3 py-2">
                                                <i class="tf-ion-ios-location-outline mr-1"></i>
                                                {{ $creatorLabel }}: {{ $certification->creator->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                {{ __('web.labels.not_assigned') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.assigned_trainer') }}</th>
                                    <td>
                                        @if($certification->assignedTrainer)
                                            <span class="badge bg-warning text-dark px-3 py-2">
                                                <i class="tf-ion-ios-person-outline mr-1"></i>
                                                {{ $certification->assignedTrainer->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.serial_number') }}</th>
                                    <td>
                                        <code>{{ $certification->accredited_serial_number ?? '—' }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.document_code') }}</th>
                                    <td>{{ $certification->document_code }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.accreditation_number') }}</th>
                                    <td>{{ $certification->accreditation_number ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.issue_date') }}</th>
                                    <td>
                                        @if($certification->accreditation_date)
                                            @php
                                                $accreditationDate = \Carbon\Carbon::parse($certification->accreditation_date);
                                            @endphp
                                            <span class="d-block fw-semibold">
                                                {{ $accreditationDate->format('Y-m-d') }}
                                            </span>
                                            <small class="text-muted">
                                                {{ $accreditationDate->locale(app()->getLocale())->diffForHumans() }}
                                            </small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.country') }}</th>
                                    <td>
                                        @if($certification->country?->name)
                                            <span class="badge bg-light text-dark border px-3 py-2">
                                                {{ $certification->country->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary opacity-75 px-3 py-2">
                                                {{ __('web.labels.not_assigned') }}
                                            </span>
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
