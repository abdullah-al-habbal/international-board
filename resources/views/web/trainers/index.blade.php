<!-- resources\views\web\trainers\index.blade.php -->
@extends('layouts.master')

@section('title', __('web.pages.trainers.title'))

@section('content')
    @include('components.partials.page_header', [
        'title'    => __('web.pages.trainers.title'),
        'subtitle' => __('web.pages.trainers.subtitle'),
    ])

    <div class="container">
        <p class="lead">{{ __('web.pages.trainers.intro_text') }}</p>
        <div class="d-flex justify-content-start gap-3 mb-4">
            <a href="{{ route('web.memberships.index') }}" class="btn btn-main">
                {{ __('web.buttons.apply_membership') }}
            </a>
            @if(!empty($whatsappNumber))
                <a href="https://wa.me/{{ $whatsappNumber }}" class="btn btn-success" target="_blank">
                    {{ __('web.buttons.whatsapp_contact') }}
                </a>
            @endif
        </div>
    </div>

    <section class="section">
        <div class="container">
            @include('web.trainers._filters')
            <div class="row">
                @include('components.sections.trainer_list')
            </div>
            @include('components.partials.pagination', ['items' => $trainers])
        </div>
    </section>
@endsection
