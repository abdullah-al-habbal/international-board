<!-- resources\views\components\sections\statistics_section.blade.php -->
<section class="counter-wrapper section-sm">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 text-center">
                <div class="title">
                    <h2>{{ __('web.statistics.title') }}</h2>
                    <p>{{ __('web.statistics.subtitle') }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-6 text-center">
                <div class="counters-item">
                    <i class="tf-ion-ios-ribbon-outline"></i>
                    <div>
                        <span class="counter" data-count="{{ $statistics['certifications'] ?? 0 }}">0</span>
                    </div>
                    <h3>{{ __('web.statistics.certifications') }}</h3>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 text-center">
                <div class="counters-item">
                    <i class="tf-ion-ios-people-outline"></i>
                    <div>
                        <span class="counter" data-count="{{ $statistics['trainers'] ?? 0 }}">0</span>
                    </div>
                    <h3>{{ __('web.statistics.trainers') }}</h3>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 text-center">
                <div class="counters-item kill-border">
                    <i class="tf-ion-ios-location-outline"></i>
                    <div>
                        <span class="counter" data-count="{{ $statistics['centers'] ?? 0 }}">0</span>
                    </div>
                    <h3>{{ __('web.statistics.centers') }}</h3>
                </div>
            </div>
        </div>
    </div>
</section>