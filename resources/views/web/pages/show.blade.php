<!-- resources\views\web\pages\show.blade.php -->
@extends('layouts.master')

@section('title', $page->title)

@section('content')
    @include('components.partials.page_header', [
        'title' => $page->title,
        'subtitle' => __('web.default_title'),
    ])

            @include('web.pages._content')
@endsection
