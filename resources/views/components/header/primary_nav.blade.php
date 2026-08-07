<!-- filePath: resources\views\components\header\primary_nav.blade.php -->
<header class="navigation fixed-top @if (Route::currentRouteName() !== 'web.home') nav-internal @endif">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light px-0">
            <a class="navbar-brand logo" href="{{ route('web.home') }}">
                <img loading="lazy" class="logo-default" src="{{ asset($appSettings['site_logo_primary'] ?? 'assets/website/images/logo.webp') }}"
                    alt="{{ __('web.site_logo') }}" />
                <img loading="lazy" class="logo-white" src="{{ asset($appSettings['site_logo_white'] ?? 'assets/website/images/w-logo.webp') }}"
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
                            {{ __('web.pages.home.title') }}
                        </a>
                    </li>

                    <li class="nav-item @if (request()->routeIs('web.memberships.*')) active @endif">
                        <a class="nav-link" href="{{ route('web.memberships.index') }}">
                            {{ __('web.pages.memberships.title') }}
                        </a>
                    </li>

                    @foreach ($navigationPages as $page)
                        <li class="nav-item @if (request()->is('web/pages/' . $page['slug'])) active @endif">
                            <a class="nav-link" href="{{ route('web.pages.show', $page['slug']) }}">
                                {{ $page['title'] }}
                            </a>
                        </li>
                    @endforeach

                    <li class="nav-item @if (request()->routeIs('web.certifications.*')) active @endif">
                        <a class="nav-link" href="{{ route('web.certifications.index') }}">
                            {{ __('web.pages.certifications.title') }}
                        </a>
                    </li>
                    <li class="nav-item @if (request()->routeIs('web.centers.*')) active @endif">
                        <a class="nav-link" href="{{ route('web.centers.index') }}">
                            {{ __('web.pages.centers.title') }}
                        </a>
                    </li>
                    <li class="nav-item @if (request()->routeIs('web.trainers.*')) active @endif">
                        <a class="nav-link" href="{{ route('web.trainers.index') }}">
                            {{ __('web.pages.trainers.title') }}
                        </a>
                    </li>
                    <li class="nav-item @if (request()->routeIs('web.blog.*')) active @endif">
                        <a class="nav-link" href="{{ route('web.blog.index') }}">
                            {{ __('web.pages.blog.title') }}
                        </a>
                    </li>

                    <li class="nav-item">
                        @if (app()->getLocale() === 'ar')
                            <a class="nav-link" href="{{ route('web.locale', 'en') }}">English</a>
                        @else
                            <a class="nav-link" href="{{ route('web.locale', 'ar') }}">العربية</a>
                        @endif
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>