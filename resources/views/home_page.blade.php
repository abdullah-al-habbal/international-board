<!-- resources\views\home_page.blade.php -->
@extends('layouts.master')

@section('title', __('web.pages.home.title'))

@section('content')
    @include('components.sections.hero_section')
    @include('components.sections.features_section')
    @include('components.sections.about_section')

    @if(isset($servicesPage) && $servicesPage)
        <section class="section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        {!! $servicesPage->content !!}
                    </div>
                </div>
            </div>
        </section>
    @endif

    @include('components.sections.cta_section')
    @include('components.sections.statistics_section')
    @include('components.sections.testimonials_section')
    @include('components.sections.blog_section')
@endsection