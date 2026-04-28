<!-- resources\views\components\sections\hero_section.blade.php -->
<div class="hero-slider">

    <div class="slider-item th-fullpage hero-area"
         style="background-image: url({{ asset('assets/website/images/slider/slider-bg-1.jpg') }});">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1 data-duration-in=".3" data-animation-in="fadeInUp" data-delay-in=".1">
                        {{ __('web.pages.home.hero_title') }}
                    </h1>
                    <p data-duration-in=".3" data-animation-in="fadeInUp" data-delay-in=".5">
                        {{ __('web.pages.home.hero_text') }}
                    </p>
                    <a data-duration-in=".3" data-animation-in="fadeInUp" data-delay-in=".8"
                       class="btn btn-main"
                       href="{{ route('web.certifications.index') }}">
                        {{ __('web.buttons.verify_now') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="slider-item th-fullpage hero-area"
         style="background-image: url({{ asset('assets/website/images/slider/slider-bg-2.jpg') }});">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1 data-duration-in=".3" data-animation-in="fadeInDown" data-delay-in=".1">
                        {{ __('web.pages.home.hero_title_2') }}
                    </h1>
                    <p data-duration-in=".3" data-animation-in="fadeInDown" data-delay-in=".5">
                        {{ __('web.pages.home.hero_text_2') }}
                    </p>
                    <a data-duration-in=".3" data-animation-in="fadeInDown" data-delay-in=".8"
                       class="btn btn-main"
                       href="{{ route('web.trainers.index') }}">
                        {{ __('web.buttons.explore_us') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
