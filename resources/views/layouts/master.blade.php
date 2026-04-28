<!-- resources\views\layouts\master.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <title>@yield('title', __('web.default_title'))</title>
    @yield('seo_meta')
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/website/images/favicon.png') }}" />

    <link rel="stylesheet" href="{{ asset('assets/website/plugins/themefisher-font/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/website/plugins/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/website/plugins/lightbox2/css/lightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/website/plugins/animate/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/website/plugins/slick/slick.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/website/css/style.css') }}">

    @stack('css')
</head>

<body id="body">
    <div id="preloader">
        <div class='preloader'>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    @include('components.header.primary_nav')

    <main>
        @yield('content')
    </main>

    @include('components.footer.main_footer')

    <script src="{{ asset('assets/website/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/website/plugins/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/website/plugins/parallax/jquery.parallax-1.1.3.js') }}"></script>
    <script src="{{ asset('assets/website/plugins/lightbox2/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('assets/website/plugins/slick/slick.min.js') }}"></script>
    <script src="{{ asset('assets/website/plugins/filterizr/jquery.filterizr.min.js') }}"></script>
    <script src="{{ asset('assets/website/plugins/smooth-scroll/smooth-scroll.min.js') }}"></script>
    <script src="{{ asset('assets/website/js/script.js') }}"></script>

    @stack('scripts')
</body>

</html>