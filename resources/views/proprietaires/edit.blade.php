@extends('layouts.app')

@section('title', 'Modifier le propriétaire')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-2">
            <a href="{{ route('proprietaires.index') }}" class="hover:text-blue-600">Propriétaires</a>
            <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <a href="{{ route('proprietaires.show', $proprietaire) }}" class="hover:text-blue-600">{{ $proprietaire->nom_complet }}</a>
            <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span>Modifier</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Modifier {{ $proprietaire->nom_complet }}</h1>
        <p class="text-gray-600 mt-1">Mettez à jour les informations du propriétaire</p>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="{{ route('proprietaires.update', $proprietaire) }}">
        @csrf
        @method('PUT')
        @include('proprietaires._form', ['proprietaire' => $proprietaire])
    </form>
</div>
@endsection