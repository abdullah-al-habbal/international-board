@extends('layouts.master')

@section('title', 'Trainer Evaluation')

@section('content')
    <section class="page-title bg-2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <h1>Trainer Evaluation</h1>
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
                        {!! $evaluationText !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
