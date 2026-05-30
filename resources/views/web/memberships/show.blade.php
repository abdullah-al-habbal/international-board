@extends('layouts.master')

@section('title', $membership->title)

@section('content')
    @include('components.partials.page_header', [
        'title'    => $membership->title,
        'subtitle' => __('web.default_title'),
    ])

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="content">
                        {!! $membership->description !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
