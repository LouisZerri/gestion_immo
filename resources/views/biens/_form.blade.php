<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Propriétaire -->
    <div class="mb-6">
        <label for="proprietaire_id" class="block text-sm font-medium text-gray-700 mb-1">Propriétaire *</label>
        <select name="proprietaire_id" id="proprietaire_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Sélectionner un propriétaire</option>
            @foreach($proprietaires as $proprietaire)
                <option value="{{ $proprietaire->id }}" 
                    {{ old('proprietaire_id', $bien->proprietaire_id ?? $selectedProprietaire ?? '') == $proprietaire->id ? 'selected' : '' }}>
                    {{ $proprietaire->nom_complet }}
                    @if($proprietaire->type === 'societe') ({{ $proprietaire->nom_societe }}) @endif
                </option>
            @endforeach
        </select>
        @error('proprietaire_id')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Type et Statut -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type de bien *</label>
            <select name="type" id="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Sélectionner</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ old('type', $bien->type ?? '') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('type')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">Référence</label>
            <input type="text" name="reference" id="reference" value="{{ old('reference', $bien->reference ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Auto-généré si vide">
            @error('reference')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
            <select name="statut" id="statut" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="disponible" {{ old('statut', $bien->statut ?? 'disponible') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                <option value="loue" {{ old('statut', $bien->statut ?? '') === 'loue' ? 'selected' : '' }}>Loué</option>
                <option value="en_travaux" {{ old('statut', $bien->statut ?? '') === 'en_travaux' ? 'selected' : '' }}>En travaux</option>
                <option value="vendu" {{ old('statut', $bien->statut ?? '') === 'vendu' ? 'selected' : '' }}>Vendu</option>
            </select>
            @error('statut')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Adresse -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Localisation</h3>
        <div class="space-y-4">
            <div>
                <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                <input type="text" name="adresse" id="adresse" required value="{{ old('adresse', $bien->adresse ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Numéro et nom de rue">
                @error('adresse')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="code_postal" class="block text-sm font-medium text-gray-700 mb-1">Code postal *</label>
                    <input type="text" name="code_postal" id="code_postal" required value="{{ old('code_postal', $bien->code_postal ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('code_postal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="ville" class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                    <input type="text" name="ville" id="ville" required value="{{ old('ville', $bien->ville ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('ville')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="pays" class="block text-sm font-medium text-gray-700 mb-1">Pays *</label>
                    <input type="text" name="pays" id="pays" required value="{{ old('pays', $bien->pays ?? 'France') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('pays')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Caractéristiques -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Caractéristiques</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="surface" class="block text-sm font-medium text-gray-700 mb-1">Surface (m²) *</label>
                <input type="number" step="0.01" name="surface" id="surface" required value="{{ old('surface', $bien->surface ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                @error('surface')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="nombre_pieces" class="block text-sm font-medium text-gray-700 mb-1">Nombre de pièces</label>
                <input type="number" name="nombre_pieces" id="nombre_pieces" value="{{ old('nombre_pieces', $bien->nombre_pieces ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                @error('nombre_pieces')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="etage" class="block text-sm font-medium text-gray-700 mb-1">Étage</label>
                <input type="number" name="etage" id="etage" value="{{ old('etage', $bien->etage ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                @error('etage')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="dpe" class="block text-sm font-medium text-gray-700 mb-1">DPE</label>
                <select name="dpe" id="dpe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Non renseigné</option>
                    <option value="A" {{ old('dpe', $bien->dpe ?? '') === 'A' ? 'selected' : '' }}>A</option>
                    <option value="B" {{ old('dpe', $bien->dpe ?? '') === 'B' ? 'selected' : '' }}>B</option>
                    <option value="C" {{ old('dpe', $bien->dpe ?? '') === 'C' ? 'selected' : '' }}>C</option>
                    <option value="D" {{ old('dpe', $bien->dpe ?? '') === 'D' ? 'selected' : '' }}>D</option>
                    <option value="E" {{ old('dpe', $bien->dpe ?? '') === 'E' ? 'selected' : '' }}>E</option>
                    <option value="F" {{ old('dpe', $bien->dpe ?? '') === 'F' ? 'selected' : '' }}>F</option>
                    <option value="G" {{ old('dpe', $bien->dpe ?? '') === 'G' ? 'selected' : '' }}>G</option>
                    <option value="NC" {{ old('dpe', $bien->dpe ?? '') === 'NC' ? 'selected' : '' }}>NC (Non classé)</option>
                </select>
                @error('dpe')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Photos -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Photos</h3>
        
        <!-- Photos existantes (en édition) -->
        @if(isset($photos) && count($photos) > 0)
        <div class="mb-4">
            <p class="text-sm text-gray-600 mb-2">Photos actuelles:</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($photos as $index => $photo)
                <div class="relative group">
                    <img src="{{ Storage::url($photo) }}" alt="Photo {{ $index + 1 }}" class="w-full h-32 object-cover rounded border">
                    <label class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded cursor-pointer opacity-0 group-hover:opacity-100 transition">
                        <input type="checkbox" name="delete_photos[]" value="{{ $photo }}" class="mr-1">
                        <span class="text-xs">Supprimer</span>
                    </label>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Upload de nouvelles photos -->
        <div>
            <label for="photos" class="block text-sm font-medium text-gray-700 mb-1">
                Ajouter des photos (max 10, 5 Mo chacune)
            </label>
            <input type="file" name="photos[]" id="photos" multiple accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPEG, PNG, JPG, GIF, WEBP</p>
            @error('photos')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            @error('photos.*')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Description -->
    <div class="mb-6">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Décrivez le bien...">{{ old('description', $bien->description ?? '') }}</textarea>
        @error('description')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Rentabilité -->
    <div class="mb-6">
        <label for="rentabilite" class="block text-sm font-medium text-gray-700 mb-1">Rentabilité (%)</label>
        <input type="number" step="0.01" name="rentabilite" id="rentabilite" value="{{ old('rentabilite', $bien->rentabilite ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
        @error('rentabilite')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Boutons -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('biens.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
            Annuler
        </a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
            {{ isset($bien) && $bien->exists ? 'Mettre à jour' : 'Créer' }}
        </button>
    </div>
</div>