@extends('layouts.master')

@section('title', __('web.pages.blog.title') ?? 'Blog')

@section('content')
    @include('components.partials.page_header', [
        'title'    => __('web.pages.blog.title') ?? 'Blog',
        'subtitle' => __('web.pages.blog.subtitle') ?? 'Read our latest updates',
    ])

    <section class="section">
        <div class="container">
            <div class="row">
                @foreach ($posts as $post)
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
            
            @include('components.partials.pagination', ['items' => $posts])
        </div>
    </section>
@endsection
