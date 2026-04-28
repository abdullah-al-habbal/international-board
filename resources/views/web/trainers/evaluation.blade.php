@extends('layouts.master')

@section('title', __('web.pages.trainers.evaluation_title'))

@section('content')
    @include('components.partials.page_header', [
        'title'    => __('web.pages.trainers.evaluation_title'),
        'subtitle' => __('web.default_title'),
    ])

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="content">
                        {!! $evaluationText !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
