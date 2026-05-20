@if ($blogPosts->isNotEmpty())
<section class="blog section" id="blog">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="title text-center">
                    <h2>{{ __('web.pages.blog.title') }}</h2>
                    <p>{{ __('web.pages.blog.subtitle') }}</p>
                    <div class="border"></div>
                </div>
            </div>
        </div>
        <div class="row">
            @include('components.sections.blog_list', ['blogPosts' => $blogPosts])
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('web.blog.index') }}" class="btn btn-main">{{ __('web.buttons.read_more') }}</a>
        </div>
    </div>
</section>
@endif
