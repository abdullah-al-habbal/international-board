@extends('layouts.master')

@section('title', $page->title)

@section('content')
    <section class="page-title bg-2" style="background-image: url({{ asset('assets/website/images/about/about-2.png') }});">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <h1>{{ $page->title }}</h1>
                        <p>{{ __('web.default_title') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="content">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
