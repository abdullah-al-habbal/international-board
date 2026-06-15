<!-- resources\views\web\certifications\index.blade.php -->
@extends('layouts.master')

@section('title', __('web.pages.certifications.title'))

@section('content')
    @include('components.partials.page_header', [
        'title' => __('web.pages.certifications.title'),
        'subtitle' => __('web.pages.certifications.subtitle'),
    ])

    @include('web.certifications._statistics')
    @include('web.certifications._search_section')
@endsection
