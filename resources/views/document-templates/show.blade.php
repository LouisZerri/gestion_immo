@extends('layouts.app')

@section('title', $documentTemplate->nom)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-2">
            <a href="{{ route('document-templates.index') }}" class="hover:text-blue-600">Modèles de documents</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900">{{ $documentTemplate->nom }}</span>
        </div>
        
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $documentTemplate->nom }}</h1>
                <div class="flex items-center mt-2 space-x-3">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $documentTemplate->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $documentTemplate->actif ? 'Actif' : 'Inactif' }}
                    </span>
                    @if($documentTemplate->is_default)
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Modèle par défaut
                        </span>
                    @endif
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $documentTemplate->type_libelle }}
                    </span>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('document-templates.preview', $documentTemplate) }}" target="_blank"
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Prévisualiser
                </a>
                <a href="{{ route('document-templates.edit', $documentTemplate) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </a>
            </div>
        </div>
    </div>

    <!-- Messages de succès -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r" role="alert">
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contenu du modèle -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Contenu du modèle</h2>
                <div class="prose max-w-none border border-gray-200 rounded p-4 bg-gray-50 overflow-x-auto">
                    {!! $documentTemplate->contenu !!}
                </div>
            </div>

            <!-- Personnalisation -->
            @if($documentTemplate->logo_path || $documentTemplate->signature_path || $documentTemplate->footer_text)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Personnalisation</h2>
                
                <div class="space-y-4">
                    @if($documentTemplate->logo_path)
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Logo</h3>
                        <img src="{{ Storage::url($documentTemplate->logo_path) }}" alt="Logo" class="h-20 border border-gray-300 rounded">
                    </div>
                    @endif

                    @if($documentTemplate->signature_path)
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Signature</h3>
                        <img src="{{ Storage::url($documentTemplate->signature_path) }}" alt="Signature" class="h-20 border border-gray-300 rounded">
                    </div>
                    @endif

                    @if($documentTemplate->footer_text)
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Pied de page</h3>
                        <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded border border-gray-200">
                            {{ $documentTemplate->footer_text }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Colonne latérale -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Informations -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations</h2>
                
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type de document</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $documentTemplate->type_libelle }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $documentTemplate->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $documentTemplate->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Modèle par défaut</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $documentTemplate->is_default ? 'Oui' : 'Non' }}</dd>
                    </div>

                    @if($documentTemplate->biens_concernes && count($documentTemplate->biens_concernes) > 0)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Types de biens concernés</dt>
                        <dd class="mt-1">
                            @foreach($documentTemplate->biens_concernes as $bienType)
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded mr-1 mb-1">
                                    {{ ucfirst(str_replace('_', ' ', $bienType)) }}
                                </span>
                            @endforeach
                        </dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Créé le</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $documentTemplate->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dernière modification</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $documentTemplate->updated_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Statistiques -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Utilisation</h2>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Documents générés</span>
                        <span class="text-lg font-semibold text-blue-600">{{ $documentTemplate->documents->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Quittances automatisées</span>
                        <span class="text-lg font-semibold text-blue-600">{{ $documentTemplate->quittancesAutomatisees->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Actions</h2>
                
                <div class="space-y-2">
                    <form action="{{ route('document-templates.toggle-active', $documentTemplate) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded transition duration-200">
                            {{ $documentTemplate->actif ? 'Désactiver' : 'Activer' }} le modèle
                        </button>
                    </form>

                    <form action="{{ route('document-templates.duplicate', $documentTemplate) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium py-2 px-4 rounded transition duration-200">
                            Dupliquer le modèle
                        </button>
                    </form>

                    <form action="{{ route('document-templates.destroy', $documentTemplate) }}" method="POST" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce modèle ? Cette action est irréversible.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-100 hover:bg-red-200 text-red-700 font-medium py-2 px-4 rounded transition duration-200">
                            Supprimer le modèle
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection