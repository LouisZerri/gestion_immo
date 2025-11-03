<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations personnelles</h3>
    
    <!-- Nom et Prénom -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
            <input type="text" name="nom" id="nom" required value="{{ old('nom', $locataire->nom ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('nom')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
            <input type="text" name="prenom" id="prenom" required value="{{ old('prenom', $locataire->prenom ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('prenom')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Date et lieu de naissance -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-1">Date de naissance *</label>
            <input type="date" name="date_naissance" id="date_naissance" required value="{{ old('date_naissance', $locataire->date_naissance?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('date_naissance')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="lieu_naissance" class="block text-sm font-medium text-gray-700 mb-1">Lieu de naissance *</label>
            <input type="text" name="lieu_naissance" id="lieu_naissance" required value="{{ old('lieu_naissance', $locataire->lieu_naissance ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('lieu_naissance')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Contact -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input type="email" name="email" id="email" required value="{{ old('email', $locataire->email ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
            <input type="text" name="telephone" id="telephone" required value="{{ old('telephone', $locataire->telephone ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="06 12 34 56 78">
            @error('telephone')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="telephone_secondaire" class="block text-sm font-medium text-gray-700 mb-1">Téléphone secondaire</label>
            <input type="text" name="telephone_secondaire" id="telephone_secondaire" value="{{ old('telephone_secondaire', $locataire->telephone_secondaire ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('telephone_secondaire')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- Adresse -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Adresse actuelle</h3>
    <div class="space-y-4">
        <div>
            <label for="adresse_actuelle" class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
            <input type="text" name="adresse_actuelle" id="adresse_actuelle" required value="{{ old('adresse_actuelle', $locataire->adresse_actuelle ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Numéro et nom de rue">
            @error('adresse_actuelle')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="code_postal" class="block text-sm font-medium text-gray-700 mb-1">Code postal *</label>
                <input type="text" name="code_postal" id="code_postal" required value="{{ old('code_postal', $locataire->code_postal ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('code_postal')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="ville" class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                <input type="text" name="ville" id="ville" required value="{{ old('ville', $locataire->ville ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('ville')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="pays" class="block text-sm font-medium text-gray-700 mb-1">Pays *</label>
                <input type="text" name="pays" id="pays" required value="{{ old('pays', $locataire->pays ?? 'France') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('pays')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

<!-- Situation professionnelle -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Situation professionnelle (optionnel)</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="profession" class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
            <input type="text" name="profession" id="profession" value="{{ old('profession', $locataire->profession ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('profession')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="employeur" class="block text-sm font-medium text-gray-700 mb-1">Employeur</label>
            <input type="text" name="employeur" id="employeur" value="{{ old('employeur', $locataire->employeur ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('employeur')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="revenus_mensuels" class="block text-sm font-medium text-gray-700 mb-1">Revenus mensuels (€)</label>
            <input type="number" step="0.01" name="revenus_mensuels" id="revenus_mensuels" value="{{ old('revenus_mensuels', $locataire->revenus_mensuels ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
            @error('revenus_mensuels')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- Garants -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Garants (max 5)</h3>
        <button type="button" onclick="addGarant()" class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium py-2 px-4 rounded-md transition duration-200">
            + Ajouter un garant
        </button>
    </div>
    
    <div id="garants-container">
        @php
            $existingGarants = old('garants', isset($locataire) ? $locataire->garants->toArray() : []);
        @endphp
        
        @if(count($existingGarants) > 0)
            @foreach($existingGarants as $index => $garant)
                <div class="garant-block border border-gray-300 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-semibold text-gray-700">Garant {{ $index + 1 }}</h4>
                        <button type="button" onclick="removeGarant(this)" class="text-red-600 hover:text-red-800 text-sm">Supprimer</button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" name="garants[{{ $index }}][nom]" placeholder="Nom" value="{{ $garant['nom'] ?? '' }}" class="px-3 py-2 border border-gray-300 rounded-md">
                        <input type="text" name="garants[{{ $index }}][prenom]" placeholder="Prénom" value="{{ $garant['prenom'] ?? '' }}" class="px-3 py-2 border border-gray-300 rounded-md">
                        <input type="email" name="garants[{{ $index }}][email]" placeholder="Email" value="{{ $garant['email'] ?? '' }}" class="px-3 py-2 border border-gray-300 rounded-md">
                        <input type="text" name="garants[{{ $index }}][telephone]" placeholder="Téléphone" value="{{ $garant['telephone'] ?? '' }}" class="px-3 py-2 border border-gray-300 rounded-md">
                        <input type="text" name="garants[{{ $index }}][profession]" placeholder="Profession" value="{{ $garant['profession'] ?? '' }}" class="px-3 py-2 border border-gray-300 rounded-md">
                        <select name="garants[{{ $index }}][lien_avec_locataire]" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Lien avec locataire --</option>
                            <option value="parent" {{ ($garant['lien_avec_locataire'] ?? '') == 'parent' ? 'selected' : '' }}>Parent</option>
                            <option value="ami" {{ ($garant['lien_avec_locataire'] ?? '') == 'ami' ? 'selected' : '' }}>Ami</option>
                            <option value="famille" {{ ($garant['lien_avec_locataire'] ?? '') == 'famille' ? 'selected' : '' }}>Famille</option>
                            <option value="organisme" {{ ($garant['lien_avec_locataire'] ?? '') == 'organisme' ? 'selected' : '' }}>Organisme</option>
                            <option value="autre" {{ ($garant['lien_avec_locataire'] ?? '') == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-gray-500 text-sm text-center py-4" id="no-garants">Aucun garant ajouté. Cliquez sur "Ajouter un garant" pour en ajouter.</p>
        @endif
    </div>
</div>

<!-- Notes -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes internes</label>
    <textarea name="notes" id="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Notes, remarques, informations complémentaires...">{{ old('notes', $locataire->notes ?? '') }}</textarea>
    @error('notes')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Boutons -->
<div class="flex justify-end space-x-3">
    <a href="{{ route('locataires.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
        Annuler
    </a>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
        {{ isset($locataire) && $locataire->exists ? 'Mettre à jour' : 'Créer' }}
    </button>
</div>

<script>
let garantIndex = {{ count($existingGarants) }};

function addGarant() {
    if (garantIndex >= 5) {
        alert('Vous ne pouvez pas ajouter plus de 5 garants.');
        return;
    }

    const noGarants = document.getElementById('no-garants');
    if (noGarants) noGarants.remove();

    const container = document.getElementById('garants-container');
    const garantBlock = `
        <div class="garant-block border border-gray-300 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-semibold text-gray-700">Garant ${garantIndex + 1}</h4>
                <button type="button" onclick="removeGarant(this)" class="text-red-600 hover:text-red-800 text-sm">Supprimer</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input type="text" name="garants[${garantIndex}][nom]" placeholder="Nom" class="px-3 py-2 border border-gray-300 rounded-md">
                <input type="text" name="garants[${garantIndex}][prenom]" placeholder="Prénom" class="px-3 py-2 border border-gray-300 rounded-md">
                <input type="email" name="garants[${garantIndex}][email]" placeholder="Email" class="px-3 py-2 border border-gray-300 rounded-md">
                <input type="text" name="garants[${garantIndex}][telephone]" placeholder="Téléphone" class="px-3 py-2 border border-gray-300 rounded-md">
                <input type="text" name="garants[${garantIndex}][profession]" placeholder="Profession" class="px-3 py-2 border border-gray-300 rounded-md">
                <select name="garants[${garantIndex}][lien_avec_locataire]" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Lien avec locataire --</option>
                    <option value="parent">Parent</option>
                    <option value="ami">Ami</option>
                    <option value="famille">Famille</option>
                    <option value="organisme">Organisme</option>
                    <option value="autre">Autre</option>
                </select>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', garantBlock);
    garantIndex++;
}

function removeGarant(button) {
    button.closest('.garant-block').remove();
    
    // Réafficher le message si plus aucun garant
    const container = document.getElementById('garants-container');
    if (container.children.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-sm text-center py-4" id="no-garants">Aucun garant ajouté. Cliquez sur "Ajouter un garant" pour en ajouter.</p>';
    }
}
</script>