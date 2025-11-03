@extends('layouts.app')

@section('title', 'Créer un contrat')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Nouveau contrat de location</h1>
            <p class="text-gray-600 mt-1">Création guidée en 3 étapes</p>
        </div>
        <a href="{{ route('contrats.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour à la liste
        </a>
    </div>

    <form action="{{ route('contrats.store') }}" method="POST" id="contratForm">
        @csrf

        <!-- Indicateur d'étapes -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <!-- Étape 1 -->
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                        1
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Bien immobilier</p>
                        <p class="text-xs text-gray-500">Sélectionner le bien</p>
                    </div>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4"></div>

                <!-- Étape 2 -->
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                        2
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Locataires</p>
                        <p class="text-xs text-gray-500">Ajouter les locataires</p>
                    </div>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4"></div>

                <!-- Étape 3 -->
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                        3
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Détails du bail</p>
                        <p class="text-xs text-gray-500">Informations contractuelles</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulaire principal -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    @if($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Erreurs de validation</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- ÉTAPE 1 : Sélection du bien -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">1</span>
                            Sélection du bien immobilier
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Bien à louer <span class="text-red-500">*</span>
                                </label>
                                <select name="bien_id" id="bien_id" required onchange="updateProprietaire()"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('bien_id') border-red-500 @enderror">
                                    <option value="">-- Sélectionner un bien --</option>
                                    @foreach($biens as $bien)
                                        <option value="{{ $bien->id }}" 
                                            data-proprietaire="{{ $bien->proprietaire_id }}"
                                            data-type="{{ $bien->type }}"
                                            {{ old('bien_id') == $bien->id ? 'selected' : '' }}>
                                            {{ $bien->reference }} - {{ $bien->adresse }}, {{ $bien->ville }}
                                            ({{ $bien->type_libelle }}, {{ $bien->nombre_pieces }}P, {{ $bien->surface }}m²)
                                        </option>
                                    @endforeach
                                </select>
                                @error('bien_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Propriétaire
                                </label>
                                <input type="hidden" name="proprietaire_id" id="proprietaire_id" value="{{ old('proprietaire_id') }}">
                                <input type="text" id="proprietaire_nom" readonly
                                    class="w-full px-4 py-2 border border-gray-300 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed" 
                                    placeholder="Sélectionnez d'abord un bien">
                            </div>
                        </div>
                    </div>

                    <hr class="my-8">

                    <!-- ÉTAPE 2 : Sélection des locataires -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">2</span>
                            Locataires
                        </h2>

                        <div id="locataires-container" class="space-y-4">
                            <!-- Locataire 1 (principal) -->
                            <div class="border border-gray-300 rounded-lg p-4 locataire-item bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-medium text-gray-900">
                                        Locataire principal <span class="text-red-500">*</span>
                                    </h3>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <select name="locataires[]" required 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="">-- Sélectionner un locataire --</option>
                                            @foreach($locataires as $locataire)
                                                <option value="{{ $locataire->id }}">
                                                    {{ $locataire->nom_complet }} 
                                                    ({{ $locataire->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Part du loyer (%)</label>
                                        <input type="number" name="parts_loyer[]" value="100" min="0" max="100" step="0.01"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="ajouterLocataire()" 
                            class="mt-4 text-blue-600 hover:text-blue-800 flex items-center gap-2 font-medium transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Ajouter un co-locataire
                        </button>
                    </div>

                    <hr class="my-8">

                    <!-- ÉTAPE 3 : Détails du bail -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">3</span>
                            Détails du bail
                        </h2>

                        @include('contrats._form', ['contrat' => null])
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('contrats.index') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                            Annuler
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium shadow-sm transition-all hover:shadow-md">
                            Créer le contrat
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar d'aide -->
            <div class="lg:col-span-1">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Aide à la création
                    </h3>
                    <div class="text-sm text-blue-800 space-y-3">
                        <p><strong>Étape 1 :</strong> Sélectionnez le bien à louer. Le propriétaire sera automatiquement renseigné.</p>
                        <p><strong>Étape 2 :</strong> Ajoutez un ou plusieurs locataires. Le premier sera le locataire principal.</p>
                        <p><strong>Étape 3 :</strong> Renseignez les informations du bail (dates, loyer, charges, etc.).</p>
                        <p><strong>💡 Astuce :</strong> Vous pourrez générer automatiquement les documents (bail, état des lieux) après la création.</p>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mt-6">
                    <h3 class="font-bold text-yellow-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Points d'attention
                    </h3>
                    <ul class="text-sm text-yellow-800 space-y-2 list-disc list-inside">
                        <li>Bail vide : durée minimum 3 ans</li>
                        <li>Bail meublé : durée minimum 1 an</li>
                        <li>En colocation, la somme des parts doit faire 100%</li>
                        <li>Vérifiez que le bien n'a pas déjà un contrat actif</li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
// Données des propriétaires (pour la mise à jour automatique)
const proprietaires = {!! json_encode($biens->pluck('proprietaire')->keyBy('id')->map(function($p) {
    return ['id' => $p->id, 'nom' => $p->nom_complet];
})) !!};

// Mettre à jour le propriétaire quand le bien est sélectionné
function updateProprietaire() {
    const select = document.getElementById('bien_id');
    const selectedOption = select.options[select.selectedIndex];
    const proprietaireId = selectedOption.dataset.proprietaire;
    
    if (proprietaireId && proprietaires[proprietaireId]) {
        document.getElementById('proprietaire_id').value = proprietaireId;
        document.getElementById('proprietaire_nom').value = proprietaires[proprietaireId].nom;
    } else {
        document.getElementById('proprietaire_id').value = '';
        document.getElementById('proprietaire_nom').value = '';
    }
}

// Ajouter un co-locataire
let locataireCount = 1;
function ajouterLocataire() {
    if (locataireCount >= 10) {
        alert('Maximum 10 locataires par contrat');
        return;
    }
    
    locataireCount++;
    const container = document.getElementById('locataires-container');
    const newLocataire = document.createElement('div');
    newLocataire.className = 'border border-gray-300 rounded-lg p-4 locataire-item bg-white';
    newLocataire.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-medium text-gray-900">Co-locataire ${locataireCount}</h3>
            <button type="button" onclick="retirerLocataire(this)" class="text-red-600 hover:text-red-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <select name="locataires[]" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">-- Sélectionner un locataire --</option>
                    @foreach($locataires as $locataire)
                        <option value="{{ $locataire->id }}">
                            {{ $locataire->nom_complet }} ({{ $locataire->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Part du loyer (%)</label>
                <input type="number" name="parts_loyer[]" value="0" min="0" max="100" step="0.01"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>
        </div>
    `;
    container.appendChild(newLocataire);

    // Redistribuer les parts équitablement
    redistribuerParts();
}

// Retirer un co-locataire
function retirerLocataire(button) {
    const item = button.closest('.locataire-item');
    item.remove();
    locataireCount--;
    redistribuerParts();
}

// Redistribuer les parts de loyer équitablement
function redistribuerParts() {
    const inputs = document.querySelectorAll('input[name="parts_loyer[]"]');
    const partEquitable = (100 / inputs.length).toFixed(2);
    inputs.forEach(input => {
        input.value = partEquitable;
    });
}

// Calculer automatiquement le loyer CC
function calculerLoyerCC() {
    const loyerHC = parseFloat(document.getElementById('loyer_hc')?.value || 0);
    const charges = parseFloat(document.getElementById('charges')?.value || 0);
    const loyerCCInput = document.getElementById('loyer_cc');
    if (loyerCCInput) {
        loyerCCInput.value = (loyerHC + charges).toFixed(2);
    }
}

// Calculer automatiquement la durée
function calculerDuree() {
    const dateDebut = document.getElementById('date_debut')?.value;
    const dateFin = document.getElementById('date_fin')?.value;
    
    if (dateDebut && dateFin) {
        const debut = new Date(dateDebut);
        const fin = new Date(dateFin);
        const mois = (fin.getFullYear() - debut.getFullYear()) * 12 + (fin.getMonth() - debut.getMonth());
        
        const dureeInput = document.getElementById('duree_mois');
        if (dureeInput) {
            dureeInput.value = mois;
        }
    }
}
</script>
@endpush