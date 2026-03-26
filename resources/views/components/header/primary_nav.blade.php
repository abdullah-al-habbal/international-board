<header class="navigation fixed-top">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light px-0">
            <a class="navbar-brand logo" href="{{ route('web.home') }}">
                <img loading="lazy" class="logo-default" src="{{ asset('assets/website/images/logo.png') }}"
                    alt="{{ __('web.site_logo') }}" />
                <img loading="lazy" class="logo-white" src="{{ asset('assets/website/images/logo-white.png') }}"
                    alt="{{ __('web.site_logo') }}" />
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation"
                aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navigation">
                <ul class="navbar-nav ml-auto text-center">
                    <li class="nav-item @if (Route::currentRouteName() == 'web.home') active @endif">
                        <a class="nav-link" href="{{ route('web.home') }}">
                            {{ __('web.components.header.homepage') }}
                        </a>
                    </li>

                    @foreach ($staticPages as $page)
                        <li class="nav-item @if (request()->is('web/pages/' . $page->slug)) active @endif">
                            <a class="nav-link" href="{{ route('web.pages.show', $page->slug) }}">
                                {{ $page->title }}
                            </a>
                        </li>
                    @endforeach

                    <li class="nav-item">
                        <a class="nav-link" href="#">{{ __('web.pages.services.title') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">{{ __('web.pages.portfolio.title') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">{{ __('web.pages.team.title') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">{{ __('web.pages.pricing.title') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">{{ __('web.pages.contact.title') }}</a>
                    </li>

                    {{-- Language Switcher Placeholder --}}
                    <li class="nav-item">
                        @if (app()->getLocale() === 'ar')
                            <a class="nav-link" href="?lang=en">English</a>
                        @else
                            <a class="nav-link" href="?lang=ar">العربية</a>
                        @endif
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
