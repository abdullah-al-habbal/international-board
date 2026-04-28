@extends('layouts.master')

@section('title', $post->title)
@section('seo_meta')
    {!! app(\App\Services\Web\SeoService::class)
        ->setTitle($post->title)
        ->setDescription((string) $post->excerpt ?? $post->content)
        ->setImage($post->image ? Storage::url($post->image) : null)
        ->render() !!}
@endsection

@section('content')
    @include('components.partials.page_header', [
        'title'    => $post->title,
        'subtitle' => $post->published_at?->format('F d, Y'),
    ])

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    @if($post->image)
                    <img loading="lazy" src="{{ Storage::url($post->image) }}" class="img-fluid mb-4 rounded" alt="{{ $post->title }}">
                    @endif
                    <div class="content">
                        {!! $post->content !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
