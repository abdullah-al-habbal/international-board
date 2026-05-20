@extends('layouts.master')

@section('title', $post->title)

@section('content')
    @include('components.partials.page_header', [
        'title'    => $post->title,
        'subtitle' => $post->published_at?->format('M d, Y') ?? __('web.pages.blog.title'),
    ])

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    @if ($post->image_url)
                        <div class="post-image mb-4">
                            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="img-fluid rounded">
                        </div>
                    @endif
                    
                    <div class="post-content">
                        {!! $post->content !!}
                    </div>

                    <div class="mt-5">
                        <a href="{{ route('web.blog.index') }}" class="btn btn-main">
                            {{ __('web.buttons.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
