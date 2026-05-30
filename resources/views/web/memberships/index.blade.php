@extends('layouts.master')

@section('title', __('web.pages.memberships.title'))

@section('content')
    @include('components.partials.page_header', [
        'title'    => __('web.pages.memberships.title'),
        'subtitle' => __('web.pages.memberships.subtitle'),
    ])

    <section class="section">
        <div class="container">
            @if(!empty($intro))
                <div class="row">
                    <div class="col-12">
                        <div class="content mb-5">
                            {!! $intro !!}
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                @forelse($memberships as $membership)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">{{ $membership->title }}</h4>
                                <a href="{{ route('web.memberships.show', $membership->id) }}"
                                    class="btn btn-main btn-sm">
                                    {{ __('web.buttons.view_details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    @include('components.partials.empty_state')
                @endforelse
            </div>
        </div>
    </section>
@endsection
