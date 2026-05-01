@extends('layouts.master')

@section('title', __('web.pages.blog.title'))

@section('content')
    @include('components.partials.page_header', [
        'title'    => __('web.pages.blog.title'),
        'subtitle' => __('web.pages.blog.subtitle'),
    ])

    <section class="section">
        <div class="container">
            <div class="row">
                @forelse ($posts as $post)
                    <div class="col-md-4 col-sm-6 mb-4">
                        <article class="post-item">
                            <div class="post-thumb">
                                <a href="{{ route('web.blog.show', $post->slug) }}">
                                    <img src="{{ $post->image_url ?? asset('assets/website/images/blog/blog-post-1.jpg') }}" 
                                         alt="{{ $post->title }}" class="img-fluid">
                                </a>
                            </div>
                            <div class="post-content mt-3">
                                <h3 class="h4"><a href="{{ route('web.blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
                                <p class="text-muted">{{ $post->published_at?->format('M d, Y') }}</p>
                                <p>{{ $post->excerpt }}</p>
                                <a href="{{ route('web.blog.show', $post->slug) }}" class="btn btn-main btn-sm">
                                    {{ __('web.buttons.read_more') }}
                                </a>
                            </div>
                        </article>
                    </div>
                @empty
                    @include('components.partials.empty_state')
                @endforelse
            </div>

            @include('components.partials.pagination', ['items' => $posts])
        </div>
    </section>
@endsection
