<!-- resources\views\components\footer\main_footer.blade.php -->
<footer id="footer" class="bg-one">
    <div class="top-footer">
        <div class="container">
            <div class="row justify-content-around">
                <div class="col-lg-4 col-md-6 mb-5 mb-lg-0">
                    <h3>{{ __('web.pages.home.title') }}</h3>
                    <p>{{ __('web.footer.description') }}</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-5 mb-lg-0">
                    <ul>
                        <li>
                            <h3>{{ __('web.features.title') }}</h3>
                        </li>
                        <li><a href="{{ route('web.certifications.index') }}">{{ __('web.pages.certifications.title') }}</a></li>
                        <li><a href="{{ route('web.centers.index') }}">{{ __('web.pages.centers.title') }}</a></li>
                        <li><a href="{{ route('web.trainers.index') }}">{{ __('web.pages.trainers.title') }}</a></li>
                        <li><a href="{{ route('web.blog.index') }}">{{ __('web.pages.blog.title') }}</a></li>
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
                        <li><h3>{{ __('filament.labels.social_links') }}</h3></li>
                        @forelse($socialLinks as $link)
                            @if(!empty($link['url']))
                                <li>
                                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer">
                                        {{ $link['label'] ?? $link['platform'] ?? $loop->index + 1 }}
                                    </a>
                                </li>
                            @endif
                        @empty
                            <li class="text-muted"><small>{{ __('web.labels.no_results') }}</small></li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom py-4 text-center border-top">
        <p class="mb-0 text-muted">{{ __('web.footer.copyright', ['year' => date('Y'), 'title' => __('web.default_title')]) }}</p>
    </div>
</footer>