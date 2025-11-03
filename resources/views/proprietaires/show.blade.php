@extends('layouts.app')

@section('title', $proprietaire->nom_complet)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <div class="flex items-center text-sm text-gray-600 mb-2">
                <a href="{{ route('proprietaires.index') }}" class="hover:text-blue-600">Propriétaires</a>
                <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>{{ $proprietaire->nom_complet }}</span>
            </div>
            <div class="flex items-center">
                <h1 class="text-3xl font-bold text-gray-800 mr-3">{{ $proprietaire->nom_complet }}</h1>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $proprietaire->type === 'particulier' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                    {{ ucfirst($proprietaire->type) }}
                </span>
                @if($proprietaire->mandat_actif)
                <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                    ✓ Mandat actif
                </span>
                @endif
            </div>
            @if($proprietaire->type === 'societe' && $proprietaire->siret)
            <p class="text-gray-600 mt-1">SIRET: {{ $proprietaire->siret }}</p>
            @endif
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('proprietaires.edit', $proprietaire) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
            <form action="{{ route('proprietaires.toggle-mandat', $proprietaire) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="{{ $proprietaire->mandat_actif ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    {{ $proprietaire->mandat_actif ? 'Désactiver le mandat' : 'Activer le mandat' }}
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
            <div class="text-sm text-gray-600 mb-1">Total biens</div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['total_biens'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Biens loués</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['biens_loues'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Disponibles</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['biens_disponibles'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Contrats actifs</div>
            <div class="text-2xl font-bold text-purple-600">{{ $stats['contrats_actifs'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Revenus/mois</div>
            <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['revenus_mensuels'], 0, ',', ' ') }} €</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Coordonnées -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Coordonnées</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-600">Email</div>
                        <div class="text-gray-800">
                            <a href="mailto:{{ $proprietaire->email }}" class="text-blue-600 hover:underline">{{ $proprietaire->email }}</a>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Téléphone</div>
                        <div class="text-gray-800">
                            <a href="tel:{{ $proprietaire->telephone }}" class="text-blue-600 hover:underline">{{ $proprietaire->telephone }}</a>
                        </div>
                    </div>
                    @if($proprietaire->telephone_secondaire)
                    <div>
                        <div class="text-sm text-gray-600">Téléphone secondaire</div>
                        <div class="text-gray-800">
                            <a href="tel:{{ $proprietaire->telephone_secondaire }}" class="text-blue-600 hover:underline">{{ $proprietaire->telephone_secondaire }}</a>
                        </div>
                    </div>
                    @endif
                    <div>
                        <div class="text-sm text-gray-600">Adresse</div>
                        <div class="text-gray-800">
                            {{ $proprietaire->adresse }}<br>
                            {{ $proprietaire->code_postal }} {{ $proprietaire->ville }}<br>
                            {{ $proprietaire->pays }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations bancaires -->
            @if($proprietaire->iban || $proprietaire->bic)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Informations bancaires</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($proprietaire->iban)
                    <div>
                        <div class="text-sm text-gray-600">IBAN</div>
                        <div class="text-gray-800 font-mono">{{ $proprietaire->iban }}</div>
                    </div>
                    @endif
                    @if($proprietaire->bic)
                    <div>
                        <div class="text-sm text-gray-600">BIC</div>
                        <div class="text-gray-800 font-mono">{{ $proprietaire->bic }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Mandat de gestion -->
            @if($proprietaire->mandat_actif)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Mandat de gestion</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($proprietaire->date_debut_mandat)
                    <div>
                        <div class="text-sm text-gray-600">Date de début</div>
                        <div class="text-gray-800">{{ $proprietaire->date_debut_mandat->format('d/m/Y') }}</div>
                    </div>
                    @endif
                    @if($proprietaire->date_fin_mandat)
                    <div>
                        <div class="text-sm text-gray-600">Date de fin</div>
                        <div class="text-gray-800">{{ $proprietaire->date_fin_mandat->format('d/m/Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Biens -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Biens ({{ $biens->count() }})</h2>
                    <a href="{{ route('biens.create', ['proprietaire' => $proprietaire->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        + Ajouter un bien
                    </a>
                </div>
                @if($biens->count() > 0)
                <div class="space-y-3">
                    @foreach($biens as $bien)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h3 class="font-semibold text-gray-800">{{ $bien->reference }}</h3>
                                    <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full {{ $bien->statut === 'loue' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($bien->statut) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $bien->adresse_complete }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $bien->type_libelle }} • {{ $bien->surface }} m² • {{ $bien->nombre_pieces }} pièce(s)
                                </p>
                            </div>
                            <a href="{{ route('biens.show', $bien) }}" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4">Aucun bien enregistré</p>
                @endif
            </div>

            <!-- Derniers contrats -->
            @if($contrats->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Derniers contrats ({{ $contrats->count() }})</h2>
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
                                    Locataire(s): {{ $contrat->locataires->pluck('nom_complet')->join(', ') }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    Loyer: {{ number_format($contrat->loyer_cc, 2, ',', ' ') }} € • Du {{ $contrat->date_debut->format('d/m/Y') }} au {{ $contrat->date_fin->format('d/m/Y') }}
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
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Notes -->
            @if($proprietaire->notes)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Notes internes</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $proprietaire->notes }}</p>
            </div>
            @endif

            <!-- Actions rapides -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Actions rapides</h2>
                <div class="space-y-2">
                    <a href="{{ route('biens.create', ['proprietaire' => $proprietaire->id]) }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Ajouter un bien
                    </a>
                    <a href="{{ route('contrats.create', ['proprietaire' => $proprietaire->id]) }}" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Créer un contrat
                    </a>
                    <a href="{{ route('documents.create', ['proprietaire' => $proprietaire->id]) }}" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Générer un document
                    </a>
                </div>
            </div>

            <!-- Informations système -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Informations système</h3>
                <div class="text-xs text-gray-600 space-y-1">
                    <div>Créé le: {{ $proprietaire->created_at->format('d/m/Y H:i') }}</div>
                    <div>Modifié le: {{ $proprietaire->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection