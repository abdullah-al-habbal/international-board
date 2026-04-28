<!-- resources\views\components\sections\about_section.blade.php -->
<section class="about-2 section" id="about">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="title text-center">
                    <h2>{{ $aboutPage?->title ?? __('web.pages.about.title') }}</h2>
                    <p>{{ __('web.pages.about.subtitle') }}</p>
                    <div class="border"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">
                @if ($aboutPage?->image)
                    <img loading="lazy" src="{{ Storage::url($aboutPage->image) }}" class="img-fluid"
                        alt="{{ $aboutPage->title }}">
                @else
                    <img loading="lazy" src="{{ asset('assets/website/images/about/about-2.png') }}" class="img-fluid"
                        alt="">
                @endif
            </div>
            <div class="col-md-6">
                <ul class="checklist">
                    @foreach (__('web.pages.about.checklist') as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('web.pages.show', 'about-us') }}" class="btn btn-main mt-20">
                    {{ __('web.buttons.learn_more') }}
                </a>
            </div>
        </div>
    </div>
</section>