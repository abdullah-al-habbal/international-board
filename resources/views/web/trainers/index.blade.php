<!-- resources\views\web\trainers\index.blade.php -->
@extends('layouts.master')

@section('title', __('web.pages.trainers.title'))

@section('content')
    @include('components.partials.page_header', [
        'title'    => __('web.pages.trainers.title'),
        'subtitle' => __('web.pages.trainers.subtitle'),
    ])

    <section class="section">
        <div class="container">
            @include('web.trainers._filters')
            <div class="row">
                @include('components.sections.trainer_list')
            </div>
            @include('components.partials.pagination', ['items' => $trainers])
        </div>
    </section>
@endsection
