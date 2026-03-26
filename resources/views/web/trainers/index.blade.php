@extends('layouts.master')

@section('title', __('web.pages.trainers.title'))

@section('content')
    <section class="page-title bg-2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <h1>{{ __('web.pages.trainers.title') }}</h1>
                        <p>{{ __('web.pages.trainers.subtitle') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <form action="{{ route('web.trainers.index') }}" method="GET" class="mb-5">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search trainers..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-main btn-block">Search</button>
                    </div>
                </div>
            </form>

            @include('components.sections.trainer_list', ['trainers' => $trainers])

            <div class="mt-4">
                {{ $trainers->links() }}
            </div>
        </div>
    </section>
@endsection
