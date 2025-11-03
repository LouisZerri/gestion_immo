@extends('layouts.app')

@section('title', $locataire->nom_complet)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <div class="flex items-center text-sm text-gray-600 mb-2">
                <a href="{{ route('locataires.index') }}" class="hover:text-blue-600">Locataires</a>
                <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>{{ $locataire->nom_complet }}</span>
            </div>
            <div class="flex items-center">
                <h1 class="text-3xl font-bold text-gray-800 mr-3">{{ $locataire->nom_complet }}</h1>
                @if($locataire->age)
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                    {{ $locataire->age }} ans
                </span>
                @endif
            </div>
            <p class="text-gray-600 mt-1">{{ $locataire->adresse_complete }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('locataires.edit', $locataire) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
            <form action="{{ route('locataires.destroy', $locataire) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce locataire ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r" role="alert">
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Contrats total</div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['contrats_total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Contrats actifs</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['contrats_actifs'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Garants</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['garants_total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Loyer mensuel</div>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['loyer_mensuel'], 0, ',', ' ') }} €</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Documents</div>
            <div class="text-2xl font-bold text-indigo-600">{{ $stats['documents_total'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Coordonnées -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Coordonnées</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-600">Email</div>
                        <div class="text-gray-800">
                            <a href="mailto:{{ $locataire->email }}" class="text-blue-600 hover:underline">{{ $locataire->email }}</a>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Téléphone</div>
                        <div class="text-gray-800">
                            <a href="tel:{{ $locataire->telephone }}" class="text-blue-600 hover:underline">{{ $locataire->telephone }}</a>
                        </div>
                    </div>
                    @if($locataire->telephone_secondaire)
                    <div>
                        <div class="text-sm text-gray-600">Téléphone secondaire</div>
                        <div class="text-gray-800">
                            <a href="tel:{{ $locataire->telephone_secondaire }}" class="text-blue-600 hover:underline">{{ $locataire->telephone_secondaire }}</a>
                        </div>
                    </div>
                    @endif
                    <div>
                        <div class="text-sm text-gray-600">Date de naissance</div>
                        <div class="text-gray-800">
                            {{ $locataire->date_naissance->format('d/m/Y') }} ({{ $locataire->age }} ans)
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Lieu de naissance</div>
                        <div class="text-gray-800">{{ $locataire->lieu_naissance }}</div>
                    </div>
                </div>
            </div>

            <!-- Situation professionnelle -->
            @if($locataire->profession || $locataire->employeur || $locataire->revenus_mensuels)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Situation professionnelle</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($locataire->profession)
                    <div>
                        <div class="text-sm text-gray-600">Profession</div>
                        <div class="text-gray-800 font-semibold">{{ $locataire->profession }}</div>
                    </div>
                    @endif
                    @if($locataire->employeur)
                    <div>
                        <div class="text-sm text-gray-600">Employeur</div>
                        <div class="text-gray-800">{{ $locataire->employeur }}</div>
                    </div>
                    @endif
                    @if($locataire->revenus_mensuels)
                    <div>
                        <div class="text-sm text-gray-600">Revenus mensuels</div>
                        <div class="text-gray-800 font-semibold">{{ number_format($locataire->revenus_mensuels, 2, ',', ' ') }} €</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Garants -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Garants ({{ $locataire->garants->count() }})</h2>
                    <a href="{{ route('locataires.edit', $locataire) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        + Gérer les garants
                    </a>
                </div>
                @if($locataire->garants->count() > 0)
                <div class="space-y-4">
                    @foreach($locataire->garants as $garant)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="font-semibold text-gray-800">{{ $garant->nom_complet }}</h3>
                                @if($garant->lien_avec_locataire)
                                <p class="text-sm text-gray-500">{{ $garant->lien_avec_locataire }}</p>
                                @endif
                                <div class="mt-2 text-sm text-gray-600 space-y-1">
                                    @if($garant->email)
                                    <div>Email: <a href="mailto:{{ $garant->email }}" class="text-blue-600 hover:underline">{{ $garant->email }}</a></div>
                                    @endif
                                    @if($garant->telephone)
                                    <div>Tél: <a href="tel:{{ $garant->telephone }}" class="text-blue-600 hover:underline">{{ $garant->telephone }}</a></div>
                                    @endif
                                    @if($garant->profession)
                                    <div>Profession: {{ $garant->profession }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4">Aucun garant enregistré</p>
                @endif
            </div>

            <!-- Historique des contrats -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Historique des contrats ({{ $contrats->count() }})</h2>
                    <a href="{{ route('contrats.create', ['locataire' => $locataire->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        + Créer un contrat
                    </a>
                </div>
                @if($contrats->count() > 0)
                <div class="space-y-3">
                    @foreach($contrats as $contrat)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h3 class="font-semibold text-gray-800">{{ $contrat->reference }}</h3>
                                    <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full {{ $contrat->statut === 'actif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($contrat->statut) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $contrat->bien->adresse }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Loyer: {{ number_format($contrat->loyer_cc, 2, ',', ' ') }} € •
                                    Du {{ $contrat->date_debut->format('d/m/Y') }} au {{ $contrat->date_fin->format('d/m/Y') }}
                                </p>
                            </div>
                            <a href="{{ route('contrats.show', $contrat) }}" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4">Aucun contrat pour ce locataire</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Notes -->
            @if($locataire->notes)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Notes internes</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $locataire->notes }}</p>
            </div>
            @endif

            <!-- Actions rapides -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Actions rapides</h2>
                <div class="space-y-2">
                    <a href="{{ route('contrats.create', ['locataire' => $locataire->id]) }}" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Créer un contrat
                    </a>
                    <a href="{{ route('documents.create', ['locataire' => $locataire->id]) }}" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Générer un document
                    </a>
                    <a href="{{ route('locataires.edit', $locataire) }}" class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Modifier le locataire
                    </a>
                </div>
            </div>

            <!-- Informations système -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Informations système</h3>
                <div class="text-xs text-gray-600 space-y-1">
                    <div>Créé le: {{ $locataire->created_at->format('d/m/Y H:i') }}</div>
                    <div>Modifié le: {{ $locataire->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection