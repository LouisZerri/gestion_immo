@extends('layouts.app')

@section('title', $bien->reference)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <div class="flex items-center text-sm text-gray-600 mb-2">
                <a href="{{ route('biens.index') }}" class="hover:text-blue-600">Biens</a>
                <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>{{ $bien->reference }}</span>
            </div>
            <div class="flex items-center">
                <h1 class="text-3xl font-bold text-gray-800 mr-3">{{ $bien->reference }}</h1>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $bien->statut_color }}">
                    {{ $bien->statut_libelle }}
                </span>
                <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                    {{ $bien->type_libelle }}
                </span>
                @if($bien->dpe)
                <span class="ml-2 px-3 py-1 text-sm font-semibold rounded {{ $bien->dpe_color }}">
                    DPE: {{ $bien->dpe }}
                </span>
                @endif
            </div>
            <p class="text-gray-600 mt-1">{{ $bien->adresse_complete }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('biens.edit', $bien) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
            <form action="{{ route('biens.destroy', $bien) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce bien ?');">
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Contrats total</div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['contrats_total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Contrats actifs</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['contrats_actifs'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Loyer actuel</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['loyer_actuel'], 0, ',', ' ') }} €</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-1">Documents</div>
            <div class="text-2xl font-bold text-purple-600">{{ $stats['documents_total'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Photos -->
            @if(count($photos) > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Photos ({{ count($photos) }})</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($photos as $photo)
                    <div class="relative group">
                        <img src="{{ Storage::url($photo) }}" alt="Photo du bien" class="w-full h-40 object-cover rounded-lg cursor-pointer hover:opacity-90 transition" onclick="openImageModal('{{ Storage::url($photo) }}')">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Caractéristiques -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Caractéristiques</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-600">Surface</div>
                        <div class="text-gray-800 font-semibold">{{ $bien->surface }} m²</div>
                    </div>
                    @if($bien->nombre_pieces)
                    <div>
                        <div class="text-sm text-gray-600">Nombre de pièces</div>
                        <div class="text-gray-800 font-semibold">{{ $bien->nombre_pieces }}</div>
                    </div>
                    @endif
                    @if($bien->etage !== null)
                    <div>
                        <div class="text-sm text-gray-600">Étage</div>
                        <div class="text-gray-800 font-semibold">{{ $bien->etage }}</div>
                    </div>
                    @endif
                    @if($bien->dpe)
                    <div>
                        <div class="text-sm text-gray-600">DPE</div>
                        <div class="text-gray-800 font-semibold">{{ $bien->dpe }}</div>
                    </div>
                    @endif
                    @if($bien->rentabilite)
                    <div>
                        <div class="text-sm text-gray-600">Rentabilité</div>
                        <div class="text-gray-800 font-semibold">{{ $bien->rentabilite }} %</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            @if($bien->description)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Description</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $bien->description }}</p>
            </div>
            @endif

            <!-- Historique des contrats -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Historique des contrats ({{ $contrats->count() }})</h2>
                    <a href="{{ route('contrats.create', ['bien' => $bien->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
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
                                <p class="text-sm text-gray-600 mt-1">
                                    Locataire(s): {{ $contrat->locataires->pluck('nom_complet')->join(', ') }}
                                </p>
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
                <p class="text-gray-500 text-center py-4">Aucun contrat pour ce bien</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Propriétaire -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Propriétaire</h2>
                <div class="flex items-start">
                    <div class="flex-shrink-0 h-12 w-12">
                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($bien->proprietaire->type === 'particulier')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                @endif
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="font-semibold text-gray-800">{{ $bien->proprietaire->nom_complet }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $bien->proprietaire->email }}</p>
                        <p class="text-sm text-gray-600">{{ $bien->proprietaire->telephone }}</p>
                        <a href="{{ route('proprietaires.show', $bien->proprietaire) }}" class="inline-block mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Voir la fiche →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Actions rapides</h2>
                <div class="space-y-2">
                    <a href="{{ route('contrats.create', ['bien' => $bien->id]) }}" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Créer un contrat
                    </a>
                    <a href="{{ route('documents.create', ['bien' => $bien->id]) }}" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Générer un document
                    </a>
                    <a href="{{ route('biens.edit', $bien) }}" class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Modifier le bien
                    </a>
                </div>
            </div>

            <!-- Informations système -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Informations système</h3>
                <div class="text-xs text-gray-600 space-y-1">
                    <div>Créé le: {{ $bien->created_at->format('d/m/Y H:i') }}</div>
                    <div>Modifié le: {{ $bien->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour afficher les images en grand -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
    <img id="modalImage" src="" alt="Image" class="max-w-full max-h-full rounded-lg">
</div>

<script>
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Fermer avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endsection