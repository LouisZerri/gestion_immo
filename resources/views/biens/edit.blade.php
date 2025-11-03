@extends('layouts.app')

@section('title', 'Modifier le bien')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-2">
            <a href="{{ route('biens.index') }}" class="hover:text-blue-600">Biens</a>
            <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <a href="{{ route('biens.show', $bien) }}" class="hover:text-blue-600">{{ $bien->reference }}</a>
            <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span>Modifier</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Modifier {{ $bien->reference }}</h1>
        <p class="text-gray-600 mt-1">Mettez à jour les informations du bien</p>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="{{ route('biens.update', $bien) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('biens._form', ['bien' => $bien, 'photos' => $photos])
    </form>
</div>
@endsection