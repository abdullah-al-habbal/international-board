@extends('layouts.master')

@section('title', $center->name)

@section('content')
    <section class="page-title bg-2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <h1>{{ $center->name }}</h1>
                        <p>{{ $center->address }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <img src="{{ $center->logo_url ?? asset('assets/website/images/about/member.jpg') }}" class="img-fluid" alt="{{ $center->name }}">
                </div>
                <div class="col-md-8">
                    <h3>About the Center</h3>
                    <p>{{ $center->description }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
