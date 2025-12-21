@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-8">
        <div class="flex items-center mb-6">
            @if($trainer->avatar)
            <img src="{{ asset('storage/'.$trainer->avatar) }}" alt="{{ $trainer->name }}"
                class="w-24 h-24 rounded-full mr-6">
            @else
            <div class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center mr-6">
                <span class="text-4xl">{{ substr($trainer->name, 0, 1) }}</span>
            </div>
            @endif
            <div>
                <h1 class="text-3xl font-bold">{{ $trainer->name }}</h1>
                @if($trainer->country)
                <p class="text-gray-600 mt-2">{{ $trainer->country->name }}</p>
                @endif
            </div>
        </div>

        @if($trainer->bio)
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">{{ __('app.biography') }}</h2>
            <p class="text-gray-700">{{ $trainer->bio }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            @if($trainer->email)
            <div>
                <h3 class="font-semibold mb-1">{{ __('app.email') }}</h3>
                <p class="text-gray-700">{{ $trainer->email }}</p>
            </div>
            @endif
            @if($trainer->phone)
            <div>
                <h3 class="font-semibold mb-1">{{ __('app.phone') }}</h3>
                <p class="text-gray-700">{{ $trainer->phone }}</p>
            </div>
            @endif
        </div>

        @if($trainer->specializations)
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">{{ __('app.specializations') }}</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($trainer->specializations as $spec)
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">{{ $spec }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if($trainer->certifications->count() > 0)
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">{{ __('app.certifications') }}</h2>
            <div class="space-y-2">
                @foreach($trainer->certifications->take(10) as $certification)
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <p class="font-medium">{{ $certification->trainee_name }}</p>
                    <p class="text-sm text-gray-600">{{ $certification->documentType?->name }}</p>
                    <p class="text-xs text-gray-500">{{ $certification->accreditation_date?->format('Y-m-d') }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('trainers.index') }}" class="text-blue-600 hover:text-blue-800">
                ← {{ __('app.back_to_trainers') }}
            </a>
        </div>
    </div>
</div>
@endsection
