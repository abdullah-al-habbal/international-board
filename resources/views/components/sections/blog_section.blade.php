@if ($blogPosts->isNotEmpty())
<section class="blog section" id="blog">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="title text-center">
                    <h2>{{ __('web.pages.blog.latest_news') ?? 'Latest News' }}</h2>
                    <p>{{ __('web.pages.blog.latest_news_subtitle') ?? 'Stay updated with our latest news and articles.' }}</p>
                    <div class="border"></div>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach ($blogPosts as $post)
            <div class="col-lg-4 col-md-6 mb-4">
                <article class="card h-100 border-0 shadow-sm">
                    @if($post->image)
                    <img loading="lazy" src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" class="card-img-top">
                    @endif
                    <div class="card-body">
                        <h4 class="card-title"><a href="{{ route('web.blog.show', $post->slug) }}">{{ $post->title }}</a></h4>
                        <p class="card-text">{{ Str::limit(strip_tags((string) $post->excerpt ?? $post->content), 100) }}</p>
                        <a href="{{ route('web.blog.show', $post->slug) }}" class="btn btn-main btn-sm">{{ __('web.buttons.read_more') ?? 'Read More' }}</a>
                    </div>
                </article>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
