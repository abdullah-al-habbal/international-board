<!-- resources\views\web\trainers\show.blade.php -->
@extends('layouts.master')

@section('title', $trainer->name)

@section('content')
    @include('components.partials.page_header', [
        'title'    => $trainer->name,
        'subtitle' => $trainer->country?->name,
    ])

    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    @include('web.trainers._avatar')
                </div>
                <div class="col-md-8">
                    @include('web.trainers._profile')
                </div>
            </div>
        </div>
    </section>
@endsection
