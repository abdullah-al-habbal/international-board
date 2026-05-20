@php
    $posts = $posts ?? $blogPosts ?? collect();
@endphp
@forelse ($posts as $post)
    <div class="col-lg-4 col-md-6 col-sm-6 mb-5">
        <div class="card card-glow h-100 text-center p-4">
            <div class="img-square-wrap mb-4">
                <img loading="lazy"
                    src="{{ $post->image_url ?? asset('assets/website/images/blog/blog-post-1.jpg') }}"
                    class="img-square" alt="{{ $post->title }}">
            </div>
            <div class="card-body p-0">
                <h4 class="card-title mb-2">
                    <a href="{{ route('web.blog.show', $post->slug) }}">{{ $post->title }}</a>
                </h4>
                @if ($post->published_at)
                    <p class="text-muted small mb-2">{{ $post->published_at->format('M d, Y') }}</p>
                @endif
                <p class="text-muted mb-3">{{ Str::limit(strip_tags((string) ($post->excerpt ?? $post->content)), 100) }}</p>
                <a href="{{ route('web.blog.show', $post->slug) }}" class="btn btn-main btn-sm">
                    {{ __('web.buttons.read_more') }}
                </a>
            </div>
        </div>
    </div>
@empty
    @include('components.partials.empty_state')
@endforelse
