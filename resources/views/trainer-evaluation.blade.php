@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-bold mb-6">{{ __('app.trainer_evaluation_mechanism') }}</h1>

        <div class="prose max-w-none">
            {!! nl2br(e($evaluationText)) !!}
        </div>

        <div class="mt-8">
            <a href="{{ route('trainers.index') }}" class="text-blue-600 hover:text-blue-800">
                {{ __('app.view_trainers') }} →
            </a>
        </div>
    </div>
</div>
@endsection
