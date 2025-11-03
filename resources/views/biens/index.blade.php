@extends('layouts.app')

@section('title', 'Biens immobiliers')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Biens immobiliers</h1>
            <p class="text-gray-600 mt-1">Gérez votre patrimoine immobilier</p>
        </div>
        <a href="{{ route('biens.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
            <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nouveau bien
        </a>
    </div>

    <!-- Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r" role="alert">
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r" role="alert">
        <p class="font-medium">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('biens.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Réf, adresse, ville..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les types</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" id="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    @foreach($statuts as $key => $label)
                        <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="proprietaire_id" class="block text-sm font-medium text-gray-700 mb-1">Propriétaire</label>
                <select name="proprietaire_id" id="proprietaire_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les propriétaires</option>
                    @foreach($proprietaires as $proprietaire)
                        <option value="{{ $proprietaire->id }}" {{ request('proprietaire_id') == $proprietaire->id ? 'selected' : '' }}>
                            {{ $proprietaire->nom_complet }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    Filtrer
                </button>
                <a href="{{ route('biens.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200">
                    ↻
                </a>
            </div>
        </form>
    </div>

    <!-- Grille des biens -->
    @if($biens->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        @foreach($biens as $bien)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
            <!-- Photo -->
            <div class="relative h-48 bg-gray-200">
                @if($bien->photo_principale)
                    <img src="{{ Storage::url($bien->photo_principale) }}" alt="{{ $bien->reference }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100">
                        <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                @endif
                
                <!-- Badges -->
                <div class="absolute top-2 right-2 flex flex-col space-y-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $bien->statut_color }}">
                        {{ $bien->statut_libelle }}
                    </span>
                    @if($bien->dpe)
                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $bien->dpe_color }}">
                        DPE: {{ $bien->dpe }}
                    </span>
                    @endif
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-lg text-gray-800">{{ $bien->reference }}</h3>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                        {{ $bien->type_libelle }}
                    </span>
                </div>

                <p class="text-sm text-gray-600 mb-2 flex items-start">
                    <svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="line-clamp-2">{{ $bien->adresse }}, {{ $bien->ville }}</span>
                </p>

                <div class="flex items-center text-sm text-gray-500 mb-3">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ $bien->proprietaire->nom_complet }}
                </div>

                <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                    <span>{{ $bien->surface }} m²</span>
                    @if($bien->nombre_pieces)
                    <span>{{ $bien->nombre_pieces }} pièce(s)</span>
                    @endif
                    @if($bien->etage !== null)
                    <span>Étage {{ $bien->etage }}</span>
                    @endif
                </div>

                <div class="flex space-x-2">
                    <a href="{{ route('biens.show', $bien) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded text-sm font-medium transition duration-200">
                        Voir
                    </a>
                    <a href="{{ route('biens.edit', $bien) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded text-sm font-medium transition duration-200">
                        Modifier
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination (uniquement si nécessaire) -->
    @if($biens->hasPages())
    <div class="bg-white rounded-lg shadow-md p-4">
        {{ $biens->links() }}
    </div>
    @endif
    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun bien</h3>
        <p class="text-gray-500 mb-6">Commencez par ajouter votre premier bien immobilier.</p>
        <a href="{{ route('biens.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau bien
        </a>
    </div>
    @endif
</div>
@endsection