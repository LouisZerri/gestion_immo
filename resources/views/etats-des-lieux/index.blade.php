@extends('layouts.app')

@section('title', 'États des lieux')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">États des lieux</h1>
            <p class="text-gray-600 mt-1">Gérez vos états des lieux d'entrée et de sortie</p>
        </div>
        <a href="{{ route('etats-des-lieux.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
            <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nouvel état des lieux
        </a>
    </div>

    <!-- Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r" role="alert">
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('etats-des-lieux.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="bien_id" class="block text-sm font-medium text-gray-700 mb-1">Bien</label>
                <select name="bien_id" id="bien_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les biens</option>
                    @foreach($biens as $bien)
                        <option value="{{ $bien->id }}" {{ request('bien_id') == $bien->id ? 'selected' : '' }}>
                            {{ $bien->reference }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les types</option>
                    <option value="entree" {{ request('type') == 'entree' ? 'selected' : '' }}>Entrée</option>
                    <option value="sortie" {{ request('type') == 'sortie' ? 'selected' : '' }}>Sortie</option>
                </select>
            </div>

            <div>
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" id="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    Filtrer
                </button>
                <a href="{{ route('etats-des-lieux.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200">
                    ↻
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des états des lieux -->
    @if($etatsDesLieux->count() > 0)
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bien</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photos</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($etatsDesLieux as $edl)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $edl->bien->reference }}</div>
                        <div class="text-sm text-gray-500">{{ $edl->bien->adresse }}, {{ $edl->bien->ville }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $edl->type === 'entree' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                            {{ $edl->type_libelle }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $edl->date_etat->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $edl->statut_color }}">
                            {{ $edl->statut_libelle }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        📷 {{ $edl->getTotalPhotos() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        @if($edl->isBrouillon())
                            <a href="{{ route('etats-des-lieux.edit', $edl) }}" class="text-blue-600 hover:text-blue-900">
                                Remplir
                            </a>
                        @else
                            <a href="{{ route('etats-des-lieux.show', $edl) }}" class="text-blue-600 hover:text-blue-900">
                                Voir
                            </a>
                        @endif
                        <a href="{{ route('etats-des-lieux.pdf', $edl) }}" target="_blank" class="text-red-600 hover:text-red-900">
                            PDF
                        </a>
                        <form action="{{ route('etats-des-lieux.destroy', $edl) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet état des lieux ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($etatsDesLieux->hasPages())
    <div class="mt-6">
        {{ $etatsDesLieux->links() }}
    </div>
    @endif
    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun état des lieux</h3>
        <p class="text-gray-500 mb-6">Commencez par créer votre premier état des lieux.</p>
        <a href="{{ route('etats-des-lieux.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Créer un état des lieux
        </a>
    </div>
    @endif
</div>
@endsection