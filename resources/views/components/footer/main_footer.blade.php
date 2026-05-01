<!-- resources\views\components\footer\main_footer.blade.php -->
<footer id="footer" class="bg-one">
    <div class="top-footer">
        <div class="container">
            <div class="row justify-content-around">
                <div class="col-lg-4 col-md-6 mb-5 mb-lg-0">
                    <h3>{{ __('web.pages.home.title') }}</h3>
                    <p>Integer posuere erat a ante venenati dapibus posuere velit aliquet. Fusce
                        dapibus, tellus cursus commodo, tortor mauris sed posuere.</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-5 mb-lg-0">
                    <ul>
                        <li>
                            <h3>{{ __('web.features.title') }}</h3>
                        </li>
                        <li><a href="{{ route('web.certifications.index') }}">{{ __('web.pages.certifications.title') }}</a></li>
                        <li><a href="{{ route('web.centers.index') }}">{{ __('web.pages.centers.title') }}</a></li>
                        <li><a href="{{ route('web.trainers.index') }}">{{ __('web.pages.trainers.title') }}</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-5 mb-md-0">
                    <ul>
                        <li>
                            <h3>{{ __('web.buttons.learn_more') }}</h3>
                        </li>
                        <li><a href="{{ route('web.home') }}">{{ __('web.pages.home.title') }}</a></li>
                        @foreach ($navigationPages as $page)
                            <li><a href="{{ route('web.pages.show', $page['slug']) }}">{{ $page['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <ul>
                        <li>
                            <h3>{{ __('filament.labels.social_links') ?? 'Social Links' }}</h3>
                        </li>
                        @if(!empty($appSettings['facebook_url']))
                            <li><a href="{{ $appSettings['facebook_url'] }}" target="_blank">Facebook</a></li>
                        @endif
                        @if(!empty($appSettings['twitter_url']))
                            <li><a href="{{ $appSettings['twitter_url'] }}" target="_blank">Twitter</a></li>
                        @endif
                        @if(!empty($appSettings['linkedin_url']))
                            <li><a href="{{ $appSettings['linkedin_url'] }}" target="_blank">LinkedIn</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom py-4 text-center border-top">
        <p class="mb-0 text-muted">&copy; Copyright {{ date('Y') }}. {{ __('web.default_title') }}. All rights reserved.</p>
    </div>
</footer>