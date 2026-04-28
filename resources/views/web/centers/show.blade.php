<!-- resources\views\web\centers\show.blade.php -->
@extends('layouts.master')

@section('title', $center->name)

@section('content')
    @include('components.partials.page_header', [
        'title'    => $center->name,
        'subtitle' => $center->country?->name,
    ])

    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    @include('web.centers._logo')
                </div>
                <div class="col-md-8">
                    @include('web.centers._details')
                </div>
            </div>
        </div>
    </section>
@endsection
