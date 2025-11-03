@extends('layouts.app')

@section('title', 'Modifier le contrat')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Modifier le contrat</h1>
            <p class="text-gray-600 mt-1">{{ $contrat->reference }}</p>
        </div>
        <a href="{{ route('contrats.show', $contrat) }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au contrat
        </a>
    </div>

    <form action="{{ route('contrats.update', $contrat) }}" method="POST" id="contratForm">
        @csrf
        @method('PUT')

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

                    <!-- Bien et propriétaire -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Bien et propriétaire</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Bien <span class="text-red-500">*</span>
                                </label>
                                <select name="bien_id" id="bien_id" required onchange="updateProprietaire()"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @foreach($biens as $bien)
                                        <option value="{{ $bien->id }}" 
                                            data-proprietaire="{{ $bien->proprietaire_id }}"
                                            {{ $contrat->bien_id == $bien->id ? 'selected' : '' }}>
                                            {{ $bien->reference }} - {{ $bien->adresse }}, {{ $bien->ville }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Propriétaire <span class="text-red-500">*</span>
                                </label>
                                <select name="proprietaire_id" id="proprietaire_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @foreach($proprietaires as $proprio)
                                        <option value="{{ $proprio->id }}" {{ $contrat->proprietaire_id == $proprio->id ? 'selected' : '' }}>
                                            {{ $proprio->nom_complet }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="my-8">

                    <!-- Locataires -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Locataires</h2>

                        <div id="locataires-container" class="space-y-4">
                            @foreach($contrat->locataires as $index => $locataire)
                                <div class="border border-gray-300 rounded-lg p-4 locataire-item {{ $index === 0 ? 'bg-gray-50' : 'bg-white' }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-medium text-gray-900">
                                            @if($index === 0)
                                                Locataire principal <span class="text-red-500">*</span>
                                            @else
                                                Co-locataire {{ $index + 1 }}
                                            @endif
                                        </h3>
                                        @if($index > 0)
                                            <button type="button" onclick="retirerLocataire(this)" class="text-red-600 hover:text-red-800 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="col-span-2">
                                            <select name="locataires[]" required 
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                                @foreach($locataires as $loc)
                                                    <option value="{{ $loc->id }}" {{ $locataire->id == $loc->id ? 'selected' : '' }}>
                                                        {{ $loc->nom_complet }} ({{ $loc->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Part du loyer (%)</label>
                                            <input type="number" name="parts_loyer[]" 
                                                value="{{ $locataire->pivot->part_loyer }}" 
                                                min="0" max="100" step="0.01"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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

                    <!-- Détails du bail -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Détails du bail</h2>
                        @include('contrats._form', ['contrat' => $contrat])
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('contrats.show', $contrat) }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                            Annuler
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium shadow-sm transition-all hover:shadow-md">
                            Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar d'info -->
            <div class="lg:col-span-1">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h3 class="font-bold text-yellow-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Attention
                    </h3>
                    <ul class="text-sm text-yellow-800 space-y-2">
                        <li>✅ La modification ne supprime pas les documents déjà générés</li>
                        <li>⚠️ Pensez à régénérer les documents si vous changez des informations importantes</li>
                        <li>📅 Vérifiez la cohérence des dates</li>
                    </ul>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
                    <h3 class="font-bold text-blue-900 mb-3">Informations</h3>
                    <div class="text-sm text-blue-800 space-y-2">
                        <p><strong>Créé le :</strong> {{ $contrat->created_at->format('d/m/Y à H:i') }}</p>
                        <p><strong>Dernière modif. :</strong> {{ $contrat->updated_at->format('d/m/Y à H:i') }}</p>
                        <p><strong>Documents associés :</strong> {{ $contrat->documents->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Même JavaScript que dans create.blade.php
let locataireCount = {{ $contrat->locataires->count() }};

function updateProprietaire() {
    // Auto-update proprietaire si besoin
}

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
                    <option value="">-- Sélectionner --</option>
                    @foreach($locataires as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->nom_complet }}</option>
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
}

function retirerLocataire(button) {
    const item = button.closest('.locataire-item');
    item.remove();
    locataireCount--;
}

function calculerLoyerCC() {
    const loyerHC = parseFloat(document.getElementById('loyer_hc')?.value || 0);
    const charges = parseFloat(document.getElementById('charges')?.value || 0);
    const loyerCCInput = document.getElementById('loyer_cc');
    if (loyerCCInput) {
        loyerCCInput.value = (loyerHC + charges).toFixed(2);
    }
}

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