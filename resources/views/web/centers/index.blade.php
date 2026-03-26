@extends('layouts.master')

@section('title', __('web.pages.centers.title'))

@section('content')
    <section class="page-title bg-2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <h1>{{ __('web.pages.centers.title') }}</h1>
                        <p>{{ __('web.pages.centers.subtitle') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            @include('components.sections.center_list', ['centers' => $centers])
        </div>
    </section>
@endsection
