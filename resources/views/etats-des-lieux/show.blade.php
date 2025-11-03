@extends('layouts.app')

@section('title', 'Consulter l\'état des lieux')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <div class="flex items-center text-sm text-gray-600 mb-2">
                <a href="{{ route('etats-des-lieux.index') }}" class="hover:text-blue-600">États des lieux</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">Consulter</span>
            </div>
            <div class="flex items-center">
                <h1 class="text-3xl font-bold text-gray-800 mr-3">{{ $etatDesLieux->type_libelle }}</h1>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $etatDesLieux->statut_color }}">
                    {{ $etatDesLieux->statut_libelle }}
                </span>
            </div>
            @if($etatDesLieux->bien)
            <p class="text-gray-600 mt-1">
                <strong>{{ $etatDesLieux->bien->reference }}</strong> - {{ $etatDesLieux->bien->adresse }}, {{ $etatDesLieux->bien->ville }}
            </p>
            @endif
        </div>
        <div class="flex space-x-2">
            @if($etatDesLieux->isBrouillon())
                <a href="{{ route('etats-des-lieux.edit', $etatDesLieux) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </a>
            @endif
            
            <a href="{{ route('etats-des-lieux.pdf', $etatDesLieux) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Télécharger PDF
            </a>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contenu principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations générales -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">📋 Informations générales</h2>
                
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-40 text-gray-600 font-medium">Date :</span>
                        <span class="text-gray-900">{{ $etatDesLieux->date_etat->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-40 text-gray-600 font-medium">Type :</span>
                        <span class="text-gray-900">{{ $etatDesLieux->type_libelle }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-40 text-gray-600 font-medium">Statut :</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $etatDesLieux->statut_color }}">
                            {{ $etatDesLieux->statut_libelle }}
                        </span>
                    </div>
                    @if($etatDesLieux->observations_generales)
                    <div>
                        <span class="text-gray-600 font-medium">Observations générales :</span>
                        <p class="mt-2 text-gray-700 whitespace-pre-line bg-gray-50 p-3 rounded">{{ $etatDesLieux->observations_generales }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Compteurs -->
            @if($etatDesLieux->compteurs_eau || $etatDesLieux->compteurs_gaz || $etatDesLieux->compteurs_electricite)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">⚡ Relevés des compteurs</h2>
                
                <div class="space-y-3">
                    @if($etatDesLieux->compteurs_eau)
                    <div class="border-l-4 border-blue-500 pl-4 py-2 bg-blue-50">
                        <p class="font-semibold text-gray-800">💧 Eau</p>
                        <p class="text-sm text-gray-600">
                            N° {{ $etatDesLieux->compteurs_eau['numero_serie'] ?? 'N/A' }} • 
                            {{ $etatDesLieux->compteurs_eau['m3'] ?? 'N/A' }} m³ • 
                            {{ $etatDesLieux->compteurs_eau['fonctionnement'] ?? 'N/A' }}
                        </p>
                    </div>
                    @endif

                    @if($etatDesLieux->compteurs_gaz)
                    <div class="border-l-4 border-orange-500 pl-4 py-2 bg-orange-50">
                        <p class="font-semibold text-gray-800">🔥 Gaz</p>
                        <p class="text-sm text-gray-600">
                            N° {{ $etatDesLieux->compteurs_gaz['numero_serie'] ?? 'N/A' }} • 
                            {{ $etatDesLieux->compteurs_gaz['m3'] ?? 'N/A' }} m³ • 
                            {{ $etatDesLieux->compteurs_gaz['fonctionnement'] ?? 'N/A' }}
                        </p>
                    </div>
                    @endif

                    @if($etatDesLieux->compteurs_electricite)
                    <div class="border-l-4 border-yellow-500 pl-4 py-2 bg-yellow-50">
                        <p class="font-semibold text-gray-800">⚡ Électricité</p>
                        <p class="text-sm text-gray-600">
                            N° {{ $etatDesLieux->compteurs_electricite['numero_serie'] ?? 'N/A' }} • 
                            {{ $etatDesLieux->compteurs_electricite['kwh'] ?? 'N/A' }} kWh • 
                            {{ $etatDesLieux->compteurs_electricite['fonctionnement'] ?? 'N/A' }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Pièces -->
            @foreach($etatDesLieux->pieces as $piece)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">🚪 {{ $piece->nom_piece }}</h2>
                    @if($piece->hasPhotos())
                        <span class="text-sm text-gray-600">📷 {{ $piece->getPhotosCount() }} photo(s)</span>
                    @endif
                </div>

                @if($piece->commentaires_piece)
                <div class="mb-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                    <p class="text-sm text-gray-700">{{ $piece->commentaires_piece }}</p>
                </div>
                @endif

                <!-- Tableau des éléments -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Élément</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nature</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">État</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fonctionnement</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Commentaires</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($piece->elements as $element)
                            <tr>
                                <td class="px-3 py-2 text-sm font-medium text-gray-900">{{ $element->element }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700">{{ $element->nature ?: '-' }}</td>
                                <td class="px-3 py-2 text-sm">
                                    @if($element->etat_usure)
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $element->etat_usure === 'Neuf' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $element->etat_usure === 'Bon état' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $element->etat_usure === 'État moyen' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $element->etat_usure === 'Mauvais état' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $element->etat_usure === 'Vétuste' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $element->etat_usure }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-sm text-gray-700">{{ $element->fonctionnement ?: '-' }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700">{{ $element->commentaires ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Photos -->
                @if($piece->hasPhotos())
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-md font-semibold text-gray-700 mb-3">📷 Photos</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($piece->photos as $photo)
                            <img src="{{ Storage::url($photo) }}" alt="Photo" class="w-full h-32 object-cover rounded border cursor-pointer hover:opacity-90 transition" onclick="openImageModal('{{ Storage::url($photo) }}')">
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Actions</h2>
                <div class="space-y-2">
                    @if($etatDesLieux->isBrouillon())
                        <a href="{{ route('etats-des-lieux.edit', $etatDesLieux) }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition">
                            ✏️ Modifier
                        </a>
                    @endif
                    
                    <a href="{{ route('etats-des-lieux.pdf', $etatDesLieux) }}" target="_blank" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition">
                        📄 Télécharger PDF
                    </a>

                    @if($etatDesLieux->bien)
                        <a href="{{ route('biens.show', $etatDesLieux->bien) }}" class="block w-full text-center bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition">
                            🏠 Voir le bien
                        </a>
                    @endif

                    @if($etatDesLieux->contrat)
                        <a href="{{ route('contrats.show', $etatDesLieux->contrat) }}" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition">
                            📋 Voir le contrat
                        </a>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistiques</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pièces :</span>
                        <span class="font-semibold">{{ $etatDesLieux->pieces->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Photos :</span>
                        <span class="font-semibold">{{ $etatDesLieux->getTotalPhotos() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Créé le :</span>
                        <span class="text-sm">{{ $etatDesLieux->created_at->format('d/m/Y') }}</span>
                    </div>
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

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endsection