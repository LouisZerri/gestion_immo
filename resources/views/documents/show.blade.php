@extends('layouts.app')

@section('title', $document->nom)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="{{ route('documents.index') }}" class="hover:text-blue-600">Documents</a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900">{{ $document->nom }}</span>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $document->nom }}</h1>
                <div class="flex items-center mt-2 space-x-3">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{-- ✅ CORRECTION : Gérer le cas où template est null --}}
                        @if($document->template)
                            {{ $document->template->type_libelle }}
                        @else
                            {{ ucfirst(str_replace('_', ' ', $document->type)) }}
                        @endif
                    </span>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $document->format === 'pdf' ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800' }}">
                        {{ strtoupper($document->format) }}
                    </span>
                    @if($document->statut)
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $document->statut === 'genere' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $document->statut === 'brouillon' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $document->statut === 'envoye' ? 'bg-blue-100 text-blue-800' : '' }}">
                        {{ ucfirst($document->statut) }}
                    </span>
                    @endif
                </div>
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('documents.download', $document) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Télécharger
                </a>

                @can('delete', $document)
                <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce document ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations du document -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Informations</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Type</p>
                        <p class="font-semibold text-gray-800">
                            @if($document->template)
                                {{ $document->template->type_libelle }}
                            @else
                                {{ ucfirst(str_replace('_', ' ', $document->type)) }}
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Format</p>
                        <p class="font-semibold text-gray-800">{{ strtoupper($document->format) }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Taille</p>
                        <p class="font-semibold text-gray-800">{{ number_format($document->file_size / 1024, 2) }} Ko</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Date de création</p>
                        <p class="font-semibold text-gray-800">{{ $document->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($document->template)
                    <div>
                        <p class="text-sm text-gray-600">Modèle utilisé</p>
                        <p class="font-semibold text-gray-800">{{ $document->template->nom }}</p>
                    </div>
                    @endif

                    @if($document->is_uploaded)
                    <div>
                        <p class="text-sm text-gray-600">Fichier original</p>
                        <p class="font-semibold text-gray-800">{{ $document->original_filename }}</p>
                    </div>
                    @endif
                </div>

                @if($document->notes)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Notes</p>
                    <p class="text-gray-800 whitespace-pre-line">{{ $document->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Liens avec le contrat et le bien -->
            @if($document->contrat)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Contrat associé</h2>
                
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $document->contrat->reference }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $document->contrat->bien->adresse }}, {{ $document->contrat->bien->ville }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                Locataire(s): {{ $document->contrat->locataires->pluck('nom_complet')->join(', ') }}
                            </p>
                        </div>
                        <a href="{{ route('contrats.show', $document->contrat) }}" class="text-blue-600 hover:text-blue-800">
                            Voir →
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Historique -->
            @if($document->logs->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Historique</h2>
                
                <div class="space-y-3">
                    @foreach($document->logs as $log)
                    <div class="flex items-start border-l-4 border-blue-500 pl-4 py-2">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">
                                @if($log->action === 'created')
                                    📄 Document créé
                                @elseif($log->action === 'downloaded')
                                    ⬇️ Téléchargé
                                @elseif($log->action === 'shared')
                                    🔗 Partagé
                                @else
                                    {{ ucfirst($log->action) }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                                @if($log->user)
                                    par {{ $log->user->name }}
                                @endif
                            </p>
                            @if($log->details)
                            <p class="text-xs text-gray-600 mt-1">{{ $log->details }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions rapides -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Actions</h2>
                
                <div class="space-y-2">
                    <a href="{{ route('documents.download', $document) }}" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition">
                        ⬇️ Télécharger
                    </a>

                    @if($document->contrat)
                    <a href="{{ route('contrats.show', $document->contrat) }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition">
                        📋 Voir le contrat
                    </a>
                    @endif

                    @if($document->bien)
                    <a href="{{ route('biens.show', $document->bien) }}" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition">
                        🏠 Voir le bien
                    </a>
                    @endif

                    @can('update', $document)
                    @if($document->template)
                    <form action="{{ route('documents.regenerate', $document) }}" method="POST" class="inline w-full">
                        @csrf
                        <button type="submit" class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-md transition">
                            🔄 Régénérer
                        </button>
                    </form>
                    @endif
                    @endcan
                </div>
            </div>

            <!-- Informations système -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Informations système</h3>
                <div class="text-xs text-gray-600 space-y-1">
                    <div>ID: {{ $document->id }}</div>
                    <div>Créé le: {{ $document->created_at->format('d/m/Y H:i') }}</div>
                    <div>Modifié le: {{ $document->updated_at->format('d/m/Y H:i') }}</div>
                    <div>Chemin: {{ $document->file_path }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection