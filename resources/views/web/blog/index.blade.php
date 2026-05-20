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
                @include('components.sections.blog_list', ['posts' => $posts])
            </div>
            @include('components.partials.pagination', ['items' => $posts])
        </div>
    </section>
@endsection
