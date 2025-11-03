<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Type de propriétaire -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Type de propriétaire *</label>
        <div class="flex space-x-4">
            <label class="flex items-center">
                <input type="radio" name="type" value="particulier" 
                    {{ old('type', $proprietaire->type ?? 'particulier') === 'particulier' ? 'checked' : '' }}
                    class="mr-2 type-radio" onchange="toggleTypeFields()">
                <span>Particulier</span>
            </label>
            <label class="flex items-center">
                <input type="radio" name="type" value="societe" 
                    {{ old('type', $proprietaire->type ?? '') === 'societe' ? 'checked' : '' }}
                    class="mr-2 type-radio" onchange="toggleTypeFields()">
                <span>Société</span>
            </label>
        </div>
        @error('type')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Champs pour Particulier -->
    <div id="particulier-fields" class="space-y-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom', $proprietaire->nom ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nom')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $proprietaire->prenom ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('prenom')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Champs pour Société -->
    <div id="societe-fields" class="space-y-4 mb-6" style="display: none;">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nom_societe" class="block text-sm font-medium text-gray-700 mb-1">Nom de la société *</label>
                <input type="text" name="nom_societe" id="nom_societe" value="{{ old('nom_societe', $proprietaire->nom_societe ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nom_societe')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="siret" class="block text-sm font-medium text-gray-700 mb-1">SIRET *</label>
                <input type="text" name="siret" id="siret" value="{{ old('siret', $proprietaire->siret ?? '') }}" maxlength="14" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="14 chiffres">
                @error('siret')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Contact -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Coordonnées</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email', $proprietaire->email ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $proprietaire->telephone ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="06 12 34 56 78">
                @error('telephone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="telephone_secondaire" class="block text-sm font-medium text-gray-700 mb-1">Téléphone secondaire</label>
                <input type="text" name="telephone_secondaire" id="telephone_secondaire" value="{{ old('telephone_secondaire', $proprietaire->telephone_secondaire ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('telephone_secondaire')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Adresse -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Adresse</h3>
        <div class="space-y-4">
            <div>
                <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $proprietaire->adresse ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Numéro et nom de rue">
                @error('adresse')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="code_postal" class="block text-sm font-medium text-gray-700 mb-1">Code postal *</label>
                    <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal', $proprietaire->code_postal ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('code_postal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="ville" class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                    <input type="text" name="ville" id="ville" value="{{ old('ville', $proprietaire->ville ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('ville')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="pays" class="block text-sm font-medium text-gray-700 mb-1">Pays *</label>
                    <input type="text" name="pays" id="pays" value="{{ old('pays', $proprietaire->pays ?? 'France') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('pays')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Informations bancaires -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations bancaires (optionnel)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="iban" class="block text-sm font-medium text-gray-700 mb-1">IBAN</label>
                <input type="text" name="iban" id="iban" value="{{ old('iban', $proprietaire->iban ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="FR76 XXXX XXXX XXXX XXXX XXXX XXX">
                @error('iban')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="bic" class="block text-sm font-medium text-gray-700 mb-1">BIC</label>
                <input type="text" name="bic" id="bic" value="{{ old('bic', $proprietaire->bic ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="11 caractères">
                @error('bic')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Mandat de gestion -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Mandat de gestion</h3>
        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="mandat_actif" value="1" 
                    {{ old('mandat_actif', $proprietaire->mandat_actif ?? false) ? 'checked' : '' }}
                    class="mr-2">
                <span class="text-sm text-gray-700">Mandat de gestion actif</span>
            </label>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="date_debut_mandat" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                <input type="date" name="date_debut_mandat" id="date_debut_mandat" value="{{ old('date_debut_mandat', $proprietaire->date_debut_mandat?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('date_debut_mandat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="date_fin_mandat" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                <input type="date" name="date_fin_mandat" id="date_fin_mandat" value="{{ old('date_fin_mandat', $proprietaire->date_fin_mandat?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('date_fin_mandat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Notes -->
    <div class="mb-6">
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes internes</label>
        <textarea name="notes" id="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Notes, remarques, informations complémentaires...">{{ old('notes', $proprietaire->notes ?? '') }}</textarea>
        @error('notes')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Boutons -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('proprietaires.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
            Annuler
        </a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
            {{ isset($proprietaire) && $proprietaire->exists ? 'Mettre à jour' : 'Créer' }}
        </button>
    </div>
</div>

<script>
function toggleTypeFields() {
    const type = document.querySelector('input[name="type"]:checked').value;
    const particulierFields = document.getElementById('particulier-fields');
    const societeFields = document.getElementById('societe-fields');
    
    if (type === 'particulier') {
        particulierFields.style.display = 'block';
        societeFields.style.display = 'none';
    } else {
        particulierFields.style.display = 'none';
        societeFields.style.display = 'block';
    }
}

// Appeler au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    toggleTypeFields();
});
</script>