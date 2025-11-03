@extends('layouts.app')

@section('title', 'Uploader un document')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-4">
            <a href="{{ route('documents.index') }}" class="hover:text-blue-600">Documents</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900">Upload</span>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900">Uploader un document externe</h1>
        <p class="text-gray-600 mt-2">Importez un document Word, Excel, PDF ou Image</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulaire -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('documents.upload.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Nom du document -->
                    <div class="mb-4">
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du document <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nom" 
                            id="nom" 
                            value="{{ old('nom') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ex: Bail signé Mr Dupont"
                            required
                        >
                        @error('nom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type de document -->
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de document <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="type" 
                            id="type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                        >
                            <option value="">-- Sélectionnez un type --</option>
                            <option value="bail_vide" {{ old('type') === 'bail_vide' ? 'selected' : '' }}>Bail vide</option>
                            <option value="bail_meuble" {{ old('type') === 'bail_meuble' ? 'selected' : '' }}>Bail meublé</option>
                            <option value="bail_commercial" {{ old('type') === 'bail_commercial' ? 'selected' : '' }}>Bail commercial</option>
                            <option value="bail_parking" {{ old('type') === 'bail_parking' ? 'selected' : '' }}>Bail parking</option>
                            <option value="etat_lieux_entree" {{ old('type') === 'etat_lieux_entree' ? 'selected' : '' }}>État des lieux entrée</option>
                            <option value="etat_lieux_sortie" {{ old('type') === 'etat_lieux_sortie' ? 'selected' : '' }}>État des lieux sortie</option>
                            <option value="quittance_loyer" {{ old('type') === 'quittance_loyer' ? 'selected' : '' }}>Quittance de loyer</option>
                            <option value="avis_echeance" {{ old('type') === 'avis_echeance' ? 'selected' : '' }}>Avis d'échéance</option>
                            <option value="mandat_gestion" {{ old('type') === 'mandat_gestion' ? 'selected' : '' }}>Mandat de gestion</option>
                            <option value="inventaire" {{ old('type') === 'inventaire' ? 'selected' : '' }}>Inventaire</option>
                            <option value="attestation_loyer" {{ old('type') === 'attestation_loyer' ? 'selected' : '' }}>Attestation de loyer</option>
                            <option value="document_externe" {{ old('type') === 'document_externe' ? 'selected' : '' }}>Document externe</option>
                            <option value="autre" {{ old('type') === 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fichier principal -->
                    <div class="mb-4">
                        <label for="fichier" class="block text-sm font-medium text-gray-700 mb-2">
                            Fichier <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="file" 
                            name="fichier" 
                            id="fichier"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            Formats acceptés : PDF, Word (.doc, .docx), Excel (.xls, .xlsx), Images (JPG, PNG, GIF) - Max : 10 Mo
                        </p>
                        @error('fichier')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Photos (pour EDL) -->
                    <div class="mb-4">
                        <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">
                            Photos (optionnel - pour états des lieux)
                        </label>
                        <input 
                            type="file" 
                            name="photos[]" 
                            id="photos"
                            accept=".jpg,.jpeg,.png"
                            multiple
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            Formats : JPG, PNG - Max : 5 Mo par photo - Multiple sélection possible
                        </p>
                        @error('photos.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Associations -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Contrat -->
                        <div>
                            <label for="contrat_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Contrat associé (optionnel)
                            </label>
                            <select 
                                name="contrat_id" 
                                id="contrat_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">-- Aucun --</option>
                                @foreach($contrats as $contrat)
                                    <option value="{{ $contrat->id }}" {{ old('contrat_id') == $contrat->id ? 'selected' : '' }}>
                                        {{ $contrat->reference }} - {{ $contrat->bien->adresse }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bien -->
                        <div>
                            <label for="bien_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Bien associé (optionnel)
                            </label>
                            <select 
                                name="bien_id" 
                                id="bien_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">-- Aucun --</option>
                                @foreach($biens as $bien)
                                    <option value="{{ $bien->id }}" {{ old('bien_id') == $bien->id ? 'selected' : '' }}>
                                        {{ $bien->adresse }} - {{ $bien->ville }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Locataire -->
                        <div>
                            <label for="locataire_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Locataire associé (optionnel)
                            </label>
                            <select 
                                name="locataire_id" 
                                id="locataire_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">-- Aucun --</option>
                                @foreach($locataires as $locataire)
                                    <option value="{{ $locataire->id }}" {{ old('locataire_id') == $locataire->id ? 'selected' : '' }}>
                                        {{ $locataire->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Propriétaire -->
                        <div>
                            <label for="proprietaire_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Propriétaire associé (optionnel)
                            </label>
                            <select 
                                name="proprietaire_id" 
                                id="proprietaire_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">-- Aucun --</option>
                                @foreach($proprietaires as $proprietaire)
                                    <option value="{{ $proprietaire->id }}" {{ old('proprietaire_id') == $proprietaire->id ? 'selected' : '' }}>
                                        {{ $proprietaire->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (optionnel)
                        </label>
                        <textarea 
                            name="notes" 
                            id="notes" 
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ajoutez des notes ou commentaires..."
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Boutons -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <a href="{{ route('documents.index') }}" class="text-gray-600 hover:text-gray-900">
                            ← Retour à la liste
                        </a>
                        <button 
                            type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors font-medium"
                        >
                            📤 Uploader le document
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Aide -->
        <div class="space-y-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">📋 Formats acceptés</h3>
                <ul class="text-xs text-blue-800 space-y-1">
                    <li>• PDF (.pdf)</li>
                    <li>• Word (.doc, .docx)</li>
                    <li>• Excel (.xls, .xlsx)</li>
                    <li>• Images (JPG, PNG, GIF)</li>
                </ul>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-green-900 mb-2">✅ États des lieux</h3>
                <p class="text-xs text-green-800">
                    Pour les états des lieux, vous pouvez ajouter plusieurs photos en sélectionnant plusieurs fichiers.
                </p>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">💡 Conseil</h3>
                <p class="text-xs text-gray-600">
                    Associez le document à un contrat, bien, locataire ou propriétaire pour faciliter la recherche ultérieure.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection