{{-- Formulaire partagé pour create et edit --}}

<div class="space-y-6">
    <!-- Type de bail et dates -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Type de bail <span class="text-red-500">*</span>
            </label>
            <select name="type_bail" id="type_bail" required onchange="verifierDureeMinimale()"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('type_bail') border-red-500 @enderror">
                <option value="">-- Sélectionner --</option>
                <option value="vide" {{ old('type_bail', $contrat?->type_bail) == 'vide' ? 'selected' : '' }}>
                    Location vide
                </option>
                <option value="meuble" {{ old('type_bail', $contrat?->type_bail) == 'meuble' ? 'selected' : '' }}>
                    Location meublée
                </option>
                <option value="commercial" {{ old('type_bail', $contrat?->type_bail) == 'commercial' ? 'selected' : '' }}>
                    Bail commercial
                </option>
                <option value="professionnel" {{ old('type_bail', $contrat?->type_bail) == 'professionnel' ? 'selected' : '' }}>
                    Bail professionnel
                </option>
                <option value="parking" {{ old('type_bail', $contrat?->type_bail) == 'parking' ? 'selected' : '' }}>
                    Parking/Garage
                </option>
            </select>
            @error('type_bail')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Date de signature
            </label>
            <input type="date" name="date_signature" id="date_signature" 
                value="{{ old('date_signature', $contrat?->date_signature?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('date_signature') border-red-500 @enderror">
            @error('date_signature')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Dates du bail -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Date de début <span class="text-red-500">*</span>
            </label>
            <input type="date" name="date_debut" id="date_debut" required onchange="calculerDuree()"
                value="{{ old('date_debut', $contrat?->date_debut?->format('Y-m-d')) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('date_debut') border-red-500 @enderror">
            @error('date_debut')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Date de fin <span class="text-red-500">*</span>
            </label>
            <input type="date" name="date_fin" id="date_fin" required onchange="calculerDuree()"
                value="{{ old('date_fin', $contrat?->date_fin?->format('Y-m-d')) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('date_fin') border-red-500 @enderror">
            @error('date_fin')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Durée (mois) <span class="text-red-500">*</span>
            </label>
            <input type="number" name="duree_mois" id="duree_mois" required min="1" max="120"
                value="{{ old('duree_mois', $contrat?->duree_mois) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('duree_mois') border-red-500 @enderror">
            @error('duree_mois')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Loyers et charges -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
        <h3 class="font-bold text-gray-900 mb-4">Loyer et charges</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Loyer hors charges <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" name="loyer_hc" id="loyer_hc" required min="0" step="0.01" onchange="calculerLoyerCC()"
                        value="{{ old('loyer_hc', $contrat?->loyer_hc) }}"
                        class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('loyer_hc') border-red-500 @enderror">
                    <span class="absolute right-3 top-2.5 text-gray-500 font-medium">€</span>
                </div>
                @error('loyer_hc')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Charges
                </label>
                <div class="relative">
                    <input type="number" name="charges" id="charges" min="0" step="0.01" onchange="calculerLoyerCC()"
                        value="{{ old('charges', $contrat?->charges ?? 0) }}"
                        class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('charges') border-red-500 @enderror">
                    <span class="absolute right-3 top-2.5 text-gray-500 font-medium">€</span>
                </div>
                @error('charges')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Loyer charges comprises
                </label>
                <div class="relative">
                    <input type="number" name="loyer_cc" id="loyer_cc" readonly
                        value="{{ old('loyer_cc', $contrat?->loyer_cc) }}"
                        class="w-full px-4 py-2 pr-10 border border-gray-300 bg-gray-100 rounded-lg text-gray-600 cursor-not-allowed">
                    <span class="absolute right-3 top-2.5 text-gray-500 font-medium">€</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Dépôt de garantie et paiement -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Dépôt de garantie <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input type="number" name="depot_garantie" id="depot_garantie" required min="0" step="0.01"
                    value="{{ old('depot_garantie', $contrat?->depot_garantie) }}"
                    class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('depot_garantie') border-red-500 @enderror">
                <span class="absolute right-3 top-2.5 text-gray-500 font-medium">€</span>
            </div>
            @error('depot_garantie')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 mt-1">Généralement 1 mois de loyer HC</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Périodicité du paiement <span class="text-red-500">*</span>
            </label>
            <select name="periodicite_paiement" id="periodicite_paiement" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('periodicite_paiement') border-red-500 @enderror">
                <option value="mensuel" {{ old('periodicite_paiement', $contrat?->periodicite_paiement) == 'mensuel' ? 'selected' : '' }}>
                    Mensuel
                </option>
                <option value="trimestriel" {{ old('periodicite_paiement', $contrat?->periodicite_paiement) == 'trimestriel' ? 'selected' : '' }}>
                    Trimestriel
                </option>
                <option value="annuel" {{ old('periodicite_paiement', $contrat?->periodicite_paiement) == 'annuel' ? 'selected' : '' }}>
                    Annuel
                </option>
            </select>
            @error('periodicite_paiement')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Jour de paiement <span class="text-red-500">*</span>
            </label>
            <input type="number" name="jour_paiement" id="jour_paiement" required min="1" max="31"
                value="{{ old('jour_paiement', $contrat?->jour_paiement ?? 1) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('jour_paiement') border-red-500 @enderror">
            @error('jour_paiement')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 mt-1">Jour du mois (1-31)</p>
        </div>
    </div>

    <!-- Révision du loyer -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="font-bold text-blue-900 mb-4">Révision du loyer (optionnel)</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Indice de référence (IRL)
                </label>
                <input type="number" name="indice_reference" id="indice_reference" min="0" step="0.01"
                    value="{{ old('indice_reference', $contrat?->indice_reference) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('indice_reference') border-red-500 @enderror">
                @error('indice_reference')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Indice INSEE du trimestre de référence</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Date de révision
                </label>
                <input type="date" name="date_revision" id="date_revision"
                    value="{{ old('date_revision', $contrat?->date_revision?->format('Y-m-d')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('date_revision') border-red-500 @enderror">
                @error('date_revision')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Date de la prochaine révision</p>
            </div>
        </div>
    </div>

    <!-- Options -->
    <div>
        <label class="flex items-center space-x-3 cursor-pointer">
            <input type="checkbox" name="tacite_reconduction" value="1"
                {{ old('tacite_reconduction', $contrat?->tacite_reconduction) ? 'checked' : '' }}
                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 transition-colors cursor-pointer">
            <span class="text-sm font-medium text-gray-700">
                Reconduction tacite
            </span>
        </label>
        <p class="text-xs text-gray-500 ml-7 mt-1">Le bail se renouvelle automatiquement si aucune des parties ne donne congé</p>
    </div>

    @if(!$contrat)
        <div>
            <label class="flex items-center space-x-3 cursor-pointer">
                <input type="checkbox" name="generer_documents" value="1" checked
                    class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 transition-colors cursor-pointer">
                <span class="text-sm font-medium text-gray-700">
                    Générer automatiquement les documents (bail, état des lieux)
                </span>
            </label>
        </div>
    @endif

    <!-- Conditions particulières -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Conditions particulières
        </label>
        <textarea name="conditions_particulieres" id="conditions_particulieres" rows="4" maxlength="5000"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y @error('conditions_particulieres') border-red-500 @enderror"
            placeholder="Clauses spécifiques, obligations particulières...">{{ old('conditions_particulieres', $contrat?->conditions_particulieres) }}</textarea>
        @error('conditions_particulieres')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
        <p class="text-xs text-gray-500 mt-1">Maximum 5000 caractères</p>
    </div>
</div>