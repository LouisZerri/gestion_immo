@extends('layouts.app')

@section('title', 'Créer un état des lieux')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-2">
            <a href="{{ route('etats-des-lieux.index') }}" class="hover:text-blue-600">États des lieux</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900">Créer</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Créer un état des lieux</h1>
        <p class="text-gray-600 mt-1">Commencez par sélectionner le bien et le type d'état des lieux</p>
    </div>

    <!-- Messages -->
    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r" role="alert">
        <p class="font-medium">{{ session('error') }}</p>
    </div>
    @endif

    @if(session('info'))
    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-r" role="alert">
        <p class="font-medium">{{ session('info') }}</p>
    </div>
    @endif

    <form action="{{ route('etats-des-lieux.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulaire principal -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
                    
                    <!-- Sélection du bien -->
                    <div>
                        <label for="bien_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Bien immobilier *
                        </label>
                        <select name="bien_id" id="bien_id" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                {{ $selectedBien ? 'readonly' : '' }}>
                            @if(!$selectedBien)
                                <option value="">Sélectionner un bien</option>
                            @endif
                            @foreach($biens as $bien)
                                <option value="{{ $bien->id }}" 
                                    {{ ($selectedBien && $selectedBien->id === $bien->id) ? 'selected' : '' }}>
                                    {{ $bien->reference }} - {{ $bien->adresse }}, {{ $bien->ville }}
                                </option>
                            @endforeach
                        </select>
                        @error('bien_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type d'état des lieux -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Type d'état des lieux *
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition {{ request('type') === 'entree' || (!request('type')) ? 'border-blue-500 bg-blue-50' : '' }}">
                                <input type="radio" name="type" value="entree" 
                                       {{ request('type') === 'entree' || (!request('type')) ? 'checked' : '' }} 
                                       required class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">État des lieux d'entrée</span>
                                    <span class="block text-xs text-gray-500">À la prise de possession</span>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition {{ request('type') === 'sortie' ? 'border-blue-500 bg-blue-50' : '' }}">
                                <input type="radio" name="type" value="sortie" 
                                       {{ request('type') === 'sortie' ? 'checked' : '' }} 
                                       required class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">État des lieux de sortie</span>
                                    <span class="block text-xs text-gray-500">À la fin du bail</span>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contrat associé (optionnel) -->
                    <div>
                        <label for="contrat_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Contrat associé (optionnel)
                        </label>
                        <select name="contrat_id" id="contrat_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                {{ $selectedContrat ? 'readonly' : '' }}>
                            <option value="">Aucun contrat</option>
                            @foreach($contrats as $contrat)
                                <option value="{{ $contrat->id }}" 
                                    {{ ($selectedContrat && $selectedContrat->id === $contrat->id) ? 'selected' : '' }}>
                                    {{ $contrat->reference }} - {{ $contrat->bien->adresse }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Vous pouvez associer cet état des lieux à un contrat spécifique
                        </p>
                        @error('contrat_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date de l'état des lieux -->
                    <div>
                        <label for="date_etat" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de l'état des lieux *
                        </label>
                        <input type="date" name="date_etat" id="date_etat" required 
                               value="{{ old('date_etat', now()->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('date_etat')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Sidebar - Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Actions</h2>
                    
                    <div class="space-y-3">
                        <!-- Créer -->
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Créer et remplir
                        </button>

                        <!-- Annuler -->
                        @if($selectedBien)
                            <a href="{{ route('biens.show', $selectedBien) }}" 
                               class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                Retour au bien
                            </a>
                        @else
                            <a href="{{ route('etats-des-lieux.index') }}" 
                               class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                Annuler
                            </a>
                        @endif
                    </div>

                    <!-- Aide -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="text-sm font-semibold text-blue-800 mb-2">💡 Comment ça marche ?</h3>
                        <ol class="text-xs text-blue-700 space-y-2 list-decimal list-inside">
                            <li>Sélectionnez le bien concerné</li>
                            <li>Choisissez le type (entrée ou sortie)</li>
                            <li>Cliquez sur "Créer et remplir"</li>
                            <li>Remplissez pièce par pièce</li>
                            <li>Ajoutez des photos si nécessaire</li>
                            <li>Générez le PDF final</li>
                        </ol>
                    </div>

                    <!-- Info -->
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-600">
                        <strong>Note :</strong> Les pièces par défaut (Entrée, Séjour, Cuisine, Chambre, SDB, WC) seront créées automatiquement. Vous pourrez en ajouter d'autres ensuite.
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection