<!-- resources\views\web\centers\index.blade.php -->
@extends('layouts.master')

@section('title', __('web.pages.centers.title'))

@section('content')
    @include('components.partials.page_header', [
        'title'    => __('web.pages.centers.title'),
        'subtitle' => __('web.pages.centers.subtitle'),
    ])

    <section class="section">
        <div class="container">
            @include('web.centers._filters')
            <div class="row">
                @include('components.sections.center_list')
            </div>
            @include('components.partials.pagination', ['items' => $centers])
        </div>
    </section>
@endsection
