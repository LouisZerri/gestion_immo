@extends('layouts.app')

@section('title', 'Documents générés')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Documents</h1>
            <p class="text-gray-600 mt-2">{{ $documents->total() }} document(s) trouvé(s)</p>
        </div>
        <div class="flex space-x-3">
            @if(auth()->user()->canManage())
            <a href="{{ route('documents.upload.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Uploader un document
            </a>
            <a href="{{ route('documents.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Générer un document
            </a>
            @endif
        </div>
    </div>

    <!-- Filtres avancés -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200">
            <button 
                type="button" 
                onclick="document.getElementById('filtres').classList.toggle('hidden')"
                class="flex items-center text-gray-700 hover:text-blue-600 font-medium"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filtres avancés
            </button>
        </div>

        <form method="GET" action="{{ route('documents.index') }}" id="filtres" class="{{ request()->hasAny(['search', 'type', 'format', 'bien_id', 'locataire_id', 'proprietaire_id', 'contrat_id', 'date_debut', 'date_fin', 'partage']) ? '' : 'hidden' }} p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Recherche -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                    <input 
                        type="text" 
                        name="search" 
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Nom, notes..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Format -->
                <div>
                    <label for="format" class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <select name="format" id="format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les formats</option>
                        @foreach($formats as $key => $label)
                            <option value="{{ $key }}" {{ request('format') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Partage -->
                <div>
                    <label for="partage" class="block text-sm font-medium text-gray-700 mb-2">Partage</label>
                    <select name="partage" id="partage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous</option>
                        <option value="oui" {{ request('partage') === 'oui' ? 'selected' : '' }}>Partagés</option>
                        <option value="non" {{ request('partage') === 'non' ? 'selected' : '' }}>Non partagés</option>
                    </select>
                </div>

                <!-- Bien -->
                <div>
                    <label for="bien_id" class="block text-sm font-medium text-gray-700 mb-2">Bien</label>
                    <select name="bien_id" id="bien_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les biens</option>
                        @foreach($biens as $bien)
                            <option value="{{ $bien->id }}" {{ request('bien_id') == $bien->id ? 'selected' : '' }}>
                                {{ $bien->adresse }} - {{ $bien->ville }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Locataire -->
                <div>
                    <label for="locataire_id" class="block text-sm font-medium text-gray-700 mb-2">Locataire</label>
                    <select name="locataire_id" id="locataire_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les locataires</option>
                        @foreach($locataires as $locataire)
                            <option value="{{ $locataire->id }}" {{ request('locataire_id') == $locataire->id ? 'selected' : '' }}>
                                {{ $locataire->nom_complet }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Propriétaire -->
                <div>
                    <label for="proprietaire_id" class="block text-sm font-medium text-gray-700 mb-2">Propriétaire</label>
                    <select name="proprietaire_id" id="proprietaire_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les propriétaires</option>
                        @foreach($proprietaires as $proprietaire)
                            <option value="{{ $proprietaire->id }}" {{ request('proprietaire_id') == $proprietaire->id ? 'selected' : '' }}>
                                {{ $proprietaire->nom_complet }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Contrat -->
                <div>
                    <label for="contrat_id" class="block text-sm font-medium text-gray-700 mb-2">Contrat</label>
                    <select name="contrat_id" id="contrat_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les contrats</option>
                        @foreach($contrats as $contrat)
                            <option value="{{ $contrat->id }}" {{ request('contrat_id') == $contrat->id ? 'selected' : '' }}>
                                {{ $contrat->reference }} - {{ $contrat->bien->adresse }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date début -->
                <div>
                    <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                    <input 
                        type="date" 
                        name="date_debut" 
                        id="date_debut"
                        value="{{ request('date_debut') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <!-- Date fin -->
                <div>
                    <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                    <input 
                        type="date" 
                        name="date_fin" 
                        id="date_fin"
                        value="{{ request('date_fin') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('documents.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">
                    Réinitialiser les filtres
                </a>
                <button 
                    type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors"
                >
                    🔍 Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des documents -->
    @if($documents->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bien / Contrat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($documents as $document)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg
                                    {{ $document->format === 'pdf' ? 'bg-red-100' : 'bg-blue-100' }}">
                                    @if($document->format === 'pdf')
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $document->nom }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $document->format_libelle }} • {{ $document->file_size_formatted }}
                                        @if($document->is_uploaded)
                                            <span class="ml-2 text-green-600">📤 Uploadé</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                {{ $document->type_libelle }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($document->contrat)
                                <div class="text-sm font-medium">{{ $document->contrat->reference }}</div>
                                <div class="text-xs text-gray-500">{{ $document->contrat->bien->adresse }}</div>
                            @elseif($document->bien)
                                <div class="text-sm">{{ $document->bien->adresse }}</div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $document->created_at->format('d/m/Y') }}
                            <div class="text-xs text-gray-400">{{ $document->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($document->is_shared)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    🔗 Partagé ({{ count($document->shared_with ?? []) }})
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                    🔒 Privé
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('documents.show', $document) }}" class="text-blue-600 hover:text-blue-900" title="Voir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('documents.download', $document) }}" class="text-green-600 hover:text-green-900" title="Télécharger">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                                @if(auth()->user()->canManage())
                                <a href="{{ route('documents.share.create', $document) }}" class="text-purple-600 hover:text-purple-900" title="Partager">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('documents.destroy', $document) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun document</h3>
            <p class="mt-1 text-sm text-gray-500">Commencez par générer ou uploader un document.</p>
            @if(auth()->user()->canManage())
            <div class="mt-6 flex justify-center space-x-3">
                <a href="{{ route('documents.upload.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    📤 Uploader un document
                </a>
                <a href="{{ route('documents.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    ➕ Générer un document
                </a>
            </div>
            @endif
        </div>
    @endif
</div>
@endsection