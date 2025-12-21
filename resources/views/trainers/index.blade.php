@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('app.trainers') }}</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('trainers.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">{{ __('app.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full rounded-md border-gray-300"
                    placeholder="{{ __('app.search') }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">{{ __('app.country') }}</label>
                <select name="country_id" class="w-full rounded-md border-gray-300">
                    <option value="">{{ __('app.all_countries') }}</option>
                    @foreach(\App\Models\Country::where('is_active', true)->get() as $country)
                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">{{ __('app.specializations') }}</label>
                <input type="text" name="specialization" value="{{ request('specialization') }}"
                    class="w-full rounded-md border-gray-300"
                    placeholder="{{ __('app.specialization') }}">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    {{ __('app.filter') }}
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($trainers as $trainer)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                @if($trainer->avatar)
                <img src="{{ asset('storage/'.$trainer->avatar) }}" alt="{{ $trainer->name }}"
                    class="w-16 h-16 rounded-full mr-4">
                @else
                <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                    <span class="text-2xl">{{ substr($trainer->name, 0, 1) }}</span>
                </div>
                @endif
                <div>
                    <h3 class="text-xl font-semibold">{{ $trainer->name }}</h3>
                    @if($trainer->country)
                    <p class="text-gray-600">{{ $trainer->country->name }}</p>
                    @endif
                </div>
            </div>
            @if($trainer->bio)
            <p class="text-gray-700 mb-4 line-clamp-3">{{ $trainer->bio }}</p>
            @endif
            @if($trainer->specializations)
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach(array_slice($trainer->specializations, 0, 3) as $spec)
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $spec }}</span>
                @endforeach
            </div>
            @endif
            <a href="{{ route('trainers.show', $trainer) }}"
                class="text-blue-600 hover:text-blue-800 font-medium">
                {{ __('app.view_details') }} →
            </a>
        </div>
        @empty
        <div class="col-span-3 text-center py-12">
            <p class="text-gray-600">{{ __('app.no_trainers_found') }}</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $trainers->links() }}
    </div>
</div>
@endsection
