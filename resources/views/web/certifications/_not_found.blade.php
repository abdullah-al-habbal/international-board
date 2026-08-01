<!-- resources\views\web\certifications\_not_found.blade.php -->
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-glow border-danger">
                    <div class="card-header bg-danger text-white">
                        <i class="tf-ion-ios-close-outline"></i>
                        {{ __('web.pages.certifications.result_not_found') }}
                    </div>
                    <div class="card-body text-center py-4">
                        <p class="card-text text-muted">
                            {{ __('web.pages.certifications.not_found_message', ['accreditation_number' => $accreditationNumber]) }}
                        </p>
                        <a href="{{ route('web.certifications.index') }}" class="btn btn-main mt-2">
                            {{ __('web.buttons.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>