{{-- resources/views/locataires/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouveau locataire')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-2">
            <a href="{{ route('locataires.index') }}" class="hover:text-blue-600">Locataires</a>
            <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span>Nouveau</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Créer un locataire</h1>
        <p class="text-gray-600 mt-1">Ajoutez un nouveau locataire avec ses garants éventuels</p>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="{{ route('locataires.store') }}">
        @csrf
        @include('locataires._form', ['locataire' => new \App\Models\Locataire()])
    </form>
</div>
@endsection