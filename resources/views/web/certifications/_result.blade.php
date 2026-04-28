<!-- resources\views\web\certifications\_result.blade.php -->
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <i class="tf-ion-ios-checkmark-outline"></i>
                        {{ __('web.pages.certifications.result_valid') }}
                    </div>
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            {{ __('web.labels.trainee') }}: {{ $certification->trainee?->name }}
                        </h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>{{ __('web.labels.document_type') }}</th>
                                    <td>{{ $certification->documentType?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.serial_number') }}</th>
                                    <td>{{ $certification->accredited_serial_number }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.document_code') }}</th>
                                    <td>{{ $certification->document_code }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.accreditation_number') }}</th>
                                    <td>{{ $certification->accreditation_number }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.issue_date') }}</th>
                                    <td>{{ $certification->accreditation_date?->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.trainer') }}</th>
                                    <td>{{ $certification->trainer?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.center') }}</th>
                                    <td>{{ $certification->certifiedCenter?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.country') }}</th>
                                    <td>{{ $certification->country?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('web.labels.nationality') }}</th>
                                    <td>{{ $certification->nationality }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
