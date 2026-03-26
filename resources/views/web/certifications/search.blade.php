@extends('layouts.master')

@section('title', __('web.pages.certifications.title'))

@section('content')
    <section class="page-title bg-2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <h1>{{ __('web.pages.certifications.title') }}</h1>
                        <p>{{ __('web.pages.certifications.subtitle') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container text-center">
            <h3>Search for Certification</h3>
            <form action="{{ route('web.certifications.search') }}" method="GET" class="mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <input type="text" name="serial" class="form-control" placeholder="Enter certification serial number...">
                        <button type="submit" class="btn btn-main mt-3">Verify Certification</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
