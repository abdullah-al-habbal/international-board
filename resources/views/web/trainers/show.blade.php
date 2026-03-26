@extends('layouts.master')

@section('title', $trainer->name)

@section('content')
    <section class="page-title bg-2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <h1>{{ $trainer->name }}</h1>
                        <p>{{ $trainer->country?->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <img src="{{ $trainer->avatar_url ?? asset('assets/website/images/about/member.jpg') }}" class="img-fluid" alt="{{ $trainer->name }}">
                </div>
                <div class="col-md-8">
                    <h3>Trainer Profile</h3>
                    <p>{{ $trainer->bio }}</p>

                    <h4 class="mt-4">Certifications</h4>
                    <ul>
                        @foreach ($trainer->certifications as $cert)
                            <li>{{ $cert->title }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
