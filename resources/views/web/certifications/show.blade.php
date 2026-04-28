<!-- resources\views\web\certifications\show.blade.php -->
@extends('layouts.master')

@section('title', __('web.pages.certifications.title'))

@section('content')
    @include('components.partials.page_header', [
        'title'    => __('web.pages.certifications.title'),
        'subtitle' => $certification->accredited_serial_number,
    ])

    @include('web.certifications._result')
@endsection
