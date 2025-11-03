<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne principale (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Informations générales -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations générales</h2>
            
            <div class="space-y-4">
                <!-- Nom -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">
                        Nom du modèle <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom', $template->nom) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nom') border-red-500 @enderror">
                    @error('nom')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                        Type de document <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">Sélectionnez un type</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $template->type) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Types de biens concernés -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Types de biens concernés (optionnel)
                    </label>
                    <p class="text-xs text-gray-500 mb-3">
                        Sélectionnez les types de biens pour lesquels ce modèle peut être utilisé. 
                        Laissez vide si le modèle concerne tous les types de biens.
                    </p>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach(\App\Models\DocumentTemplate::getTypesBiens() as $key => $label)
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input 
                                    type="checkbox" 
                                    name="biens_concernes[]" 
                                    value="{{ $key }}"
                                    {{ in_array($key, old('biens_concernes', $template->biens_concernes ?? [])) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    
                    @error('biens_concernes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Checkboxes -->
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="actif" id="actif" value="1" 
                               {{ old('actif', $template->actif ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="actif" class="ml-2 block text-sm text-gray-700">
                            Modèle actif
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_default" id="is_default" value="1"
                               {{ old('is_default', $template->is_default) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_default" class="ml-2 block text-sm text-gray-700">
                            Modèle par défaut
                        </label>
                    </div>
                </div>

                <p class="text-xs text-gray-500">
                    <strong>Note :</strong> Si vous cochez "Modèle par défaut", il sera automatiquement utilisé pour ce type de document lorsqu'aucun autre modèle n'est spécifié.
                </p>
            </div>
        </div>

        <!-- Contenu du modèle -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Contenu du modèle</h2>
            
            <div>
                <label for="contenu" class="block text-sm font-medium text-gray-700 mb-2">
                    Éditeur de contenu <span class="text-red-500">*</span>
                </label>
                <p class="text-sm text-gray-500 mb-3">
                    Utilisez les balises dynamiques de la colonne de droite pour personnaliser votre document.
                    <strong>Ne supprimez pas les balises</strong> sinon elles ne seront pas remplacées.
                </p>
                
                <textarea name="contenu" id="contenu" rows="20"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contenu') border-red-500 @enderror">{{ old('contenu', $template->contenu) }}</textarea>
                
                @error('contenu')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Personnalisation -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Personnalisation</h2>
            
            <div class="space-y-4">
                <!-- Logo -->
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">
                        Logo (optionnel)
                    </label>
                    @if($template->logo_path)
                        <div class="mb-2">
                            <img src="{{ Storage::url($template->logo_path) }}" alt="Logo actuel" class="h-16 border border-gray-300 rounded">
                            <p class="text-xs text-gray-500 mt-1">Logo actuel</p>
                        </div>
                    @endif
                    <input type="file" name="logo" id="logo" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Formats acceptés : JPG, PNG, GIF, SVG (max 2 Mo)</p>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Signature -->
                <div>
                    <label for="signature" class="block text-sm font-medium text-gray-700 mb-1">
                        Signature (optionnel)
                    </label>
                    @if($template->signature_path)
                        <div class="mb-2">
                            <img src="{{ Storage::url($template->signature_path) }}" alt="Signature actuelle" class="h-16 border border-gray-300 rounded">
                            <p class="text-xs text-gray-500 mt-1">Signature actuelle</p>
                        </div>
                    @endif
                    <input type="file" name="signature" id="signature" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Formats acceptés : JPG, PNG, GIF, SVG (max 2 Mo)</p>
                    @error('signature')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Footer -->
                <div>
                    <label for="footer_text" class="block text-sm font-medium text-gray-700 mb-1">
                        Texte du pied de page (optionnel)
                    </label>
                    <textarea name="footer_text" id="footer_text" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Ex: GEST'IMMO - 123 Rue Exemple, 75001 Paris - contact@gestimmo.fr">{{ old('footer_text', $template->footer_text) }}</textarea>
                    @error('footer_text')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex items-center justify-between">
            <a href="{{ route('document-templates.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-6 rounded-lg transition duration-200">
                Annuler
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                {{ $template->exists ? 'Mettre à jour' : 'Créer le modèle' }}
            </button>
        </div>
    </div>

    <!-- Colonne latérale (1/3) - Balises dynamiques -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">✨ Balises dynamiques</h2>
            <p class="text-sm text-gray-600 mb-4">Cliquez sur une balise pour l'insérer dans le contenu.</p>
            
            <div class="space-y-4 max-h-[600px] overflow-y-auto">
                @foreach($availableTags as $category => $tags)
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ $category }}</h3>
                        <div class="space-y-1">
                            @foreach($tags as $tagCode => $tagDescription)
                                <button type="button" onclick="insertTag('{{ $tagCode }}')"
                                        class="w-full text-left px-3 py-2 text-xs bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-300 rounded transition duration-150"
                                        title="{{ $tagDescription }}">
                                    <code class="text-blue-600 font-mono">{{ $tagCode }}</code>
                                    <span class="block text-gray-600 text-[10px] mt-0.5">{{ $tagDescription }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Aide sur les boucles -->
            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                <h4 class="text-xs font-semibold text-yellow-800 mb-1">💡 Utilisation des boucles</h4>
                <p class="text-[10px] text-yellow-700">
                    Pour gérer plusieurs locataires ou garants, utilisez les balises de bloc.<br>
                    <strong>Exemple :</strong><br>
                    <code class="bg-white px-1 py-0.5 rounded text-[9px]">@{{LocataireBlockStart}}</code><br>
                    Nom : <code class="bg-white px-1 py-0.5 rounded text-[9px]">@{{Locataire_NomComplet}}</code><br>
                    <code class="bg-white px-1 py-0.5 rounded text-[9px]">@{{LocataireBlockEnd}}</code>
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/tplw78no1mure7qlrep2sb7wfp1y67awyoaxdufevbb410nc/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    // Initialiser TinyMCE
    tinymce.init({
        selector: '#contenu',
        height: 600,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | table | code | help',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.6; }',
        language: 'fr_FR',
        promotion: false,
        setup: function (editor) {
            editor.on('init', function () {
                console.log('✅ TinyMCE initialisé avec succès');
            });
        }
    });

    // Fonction pour insérer une balise dans l'éditeur
    function insertTag(tag) {
        const editor = tinymce.get('contenu');
        
        if (editor) {
            // Insérer la balise avec un espace après pour faciliter la saisie
            editor.insertContent(tag + ' ');
            
            // Mettre le focus sur l'éditeur
            editor.focus();
            
            console.log('✅ Balise insérée:', tag);
        } else {
            console.error('❌ Éditeur TinyMCE non trouvé');
        }
    }

    // IMPORTANT : Synchroniser et valider TinyMCE avant soumission
    document.querySelector('form').addEventListener('submit', function(e) {
        const editor = tinymce.get('contenu');
        
        if (editor) {
            // Forcer la sauvegarde du contenu dans le textarea
            editor.save();
            
            // Récupérer le contenu
            const content = editor.getContent();
            
            // Vérifier que le contenu n'est pas vide
            if (!content || content.trim() === '' || content === '<p></p>' || content === '<p><br></p>') {
                e.preventDefault();
                e.stopPropagation();
                
                // Mettre le focus sur l'éditeur
                editor.focus();
                
                // Afficher une alerte
                alert('⚠️ Le contenu du modèle est obligatoire.\n\nVeuillez saisir du contenu dans l\'éditeur.');
                
                return false;
            }
            
            console.log('✅ Formulaire valide, soumission en cours...');
        }
        
        return true;
    });
</script>
@endpush