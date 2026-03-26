@extends('layouts.master')

@section('title', 'Certification Verification')

@section('content')
    <section class="page-title bg-2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <h1>{{ __('web.pages.certifications.title') }}</h1>
                        <p>Verification Success</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            Valid Certification
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Awarded to: {{ $certification->student_name }}</h5>
                            <p class="card-text">
                                <strong>Course:</strong> {{ $certification->course_name }}<br>
                                <strong>Date:</strong> {{ $certification->issue_date }}<br>
                                <strong>Serial:</strong> {{ $certification->serial_number }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
