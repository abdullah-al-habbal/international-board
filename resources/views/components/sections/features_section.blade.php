<!-- resources/views/components/sections/features_section.blade.php -->
<section class="service-2 section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="title text-center">
                    <h2>{{ __('web.features.title') }}</h2>
                    <p>{{ __('web.features.subtitle') }}</p>
                    <div class="border"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 text-center d-none d-md-block">
            </div>
            <div class="col-md-8">
                <div class="row text-center">
                    @foreach (__('web.features.items') as $feature)
                        <div class="col-md-6 col-sm-6">
                            <a href="{{ isset($feature['route']) ? route($feature['route']) : '#' }}"
                                class="text-decoration-none text-reset">
                                <div class="service-item">
                                    <i class="{{ $feature['icon'] }}"></i>
                                    <h4>{{ $feature['title'] }}</h4>
                                    <p>{{ $feature['description'] }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>