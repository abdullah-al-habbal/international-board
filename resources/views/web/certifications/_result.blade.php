{{-- resources/views/web/certifications/_result.blade.php --}}
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
                                    <th class="w-35">{{ __('web.labels.document_type') }}</th>
                                    <td>
                                        @if($certification->documentType?->name)
                                            <span class="badge bg-primary px-3 py-2">
                                                <i class="tf-ion-ios-paper-outline mr-1"></i>
                                                {{ $certification->documentType->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">{{ __('web.labels.not_assigned') }}</span>
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
                                    <th>{{ __('web.labels.trainer') }}</th>
                                    <td>
                                        @if($certification->trainer?->name)
                                            <span class="badge bg-info text-dark px-3 py-2">
                                                <i class="tf-ion-ios-people-outline mr-1"></i>
                                                {{ $certification->trainer->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                {{ __('web.labels.no_trainer') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.center') }}</th>
                                    <td>
                                        @if($certification->certifiedCenter?->name)
                                            <span class="badge bg-dark px-3 py-2">
                                                <i class="tf-ion-ios-location-outline mr-1"></i>
                                                {{ $certification->certifiedCenter->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary opacity-75 px-3 py-2">
                                                {{ __('web.labels.not_assigned') }}
                                            </span>
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
                                <tr>
                                    <th>{{ __('web.labels.nationality') }}</th>
                                    <td>
                                        @if($certification->country?->nationality)
                                            <span class="badge bg-dark">
                                                {{ $certification->country->nationality }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary opacity-75">
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
