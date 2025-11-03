@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tableau de bord</h1>
        <p class="text-gray-600 mt-2">Bienvenue {{ auth()->user()->name }} !</p>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @if(auth()->user()->canManage())
        <!-- Card Biens -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Biens</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['biens'] ?? 0 }}</p>
                    <div class="flex items-center space-x-4 mt-2 text-xs">
                        <span class="text-green-600">● {{ $stats['biens_loues'] ?? 0 }} loués</span>
                        <span class="text-gray-400">● {{ $stats['biens_disponibles'] ?? 0 }} libres</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card Contrats -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Contrats actifs</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['contrats'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card Locataires -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Locataires</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['locataires'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card Documents -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Documents</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['documents'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $stats['documents_mois'] ?? 0 }} ce mois-ci</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
        @else
        <!-- Statistiques simplifiées pour propriétaires/locataires -->
        <div class="bg-white rounded-lg shadow p-6 col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Mes contrats</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['contrats'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Mes documents</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['documents'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if(auth()->user()->canManage())
    <!-- Actions rapides -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Actions rapides</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('contrats.create') }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors">
                <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <div>
                    <p class="font-semibold text-gray-900">Nouveau contrat</p>
                    <p class="text-sm text-gray-600">Créer un bail</p>
                </div>
            </a>

            <a href="{{ route('documents.create') }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors">
                <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <div>
                    <p class="font-semibold text-gray-900">Générer un document</p>
                    <p class="text-sm text-gray-600">Quittance, bail, etc.</p>
                </div>
            </a>

            <a href="{{ route('biens.create') }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors">
                <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <div>
                    <p class="font-semibold text-gray-900">Ajouter un bien</p>
                    <p class="text-sm text-gray-600">Nouveau logement</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Contrats à renouveler -->
    @if(isset($contratsARenouveler) && count($contratsARenouveler) > 0)
    <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-8">
        <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h2 class="text-xl font-bold text-orange-900">⚠️ Contrats à renouveler (2 prochains mois)</h2>
        </div>
        <div class="space-y-3">
            @foreach($contratsARenouveler as $contrat)
            <div class="flex items-center justify-between p-4 bg-white border border-orange-200 rounded-lg">
                <div>
                    <p class="font-medium text-gray-900">{{ $contrat->bien->adresse_complete }}</p>
                    <p class="text-sm text-gray-600">
                        Locataire(s) : {{ $contrat->locataires->pluck('nom_complet')->join(', ') }}
                    </p>
                    <p class="text-xs text-orange-600 font-medium mt-1">
                        Fin : {{ $contrat->date_fin?->format('d/m/Y') }} ({{ $contrat->date_fin?->diffForHumans() }})
                    </p>
                </div>
                <a href="{{ route('contrats.show', $contrat) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Voir le contrat →
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif

    <!-- Activité récente -->
    @if(isset($recentDocuments) && count($recentDocuments) > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Documents récents</h2>
        <div class="space-y-4">
            @foreach($recentDocuments as $document)
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $document->nom }}</p>
                        <p class="text-sm text-gray-600">
                            {{ $document->contrat?->bien->adresse ?? 'Sans contrat' }} • 
                            {{ $document->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('documents.show', $document) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Voir →
                </a>
            </div>
            @endforeach
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('documents.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Voir tous les documents →
            </a>
        </div>
    </div>
    @endif
</div>
@endsection