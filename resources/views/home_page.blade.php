@extends('layouts.master')

@section('title', __('web.pages.home.title'))

@section('content')
    @include('components.sections.hero_section')
    @include('components.sections.features_section')
    @include('components.sections.about_section')
    @include('components.sections.cta_section')
    @include('components.sections.statistics_section')
    @include('components.sections.testimonials_section')
@endsection