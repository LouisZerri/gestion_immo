@extends('layouts.app')

@section('title', 'Générer un document')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-2">
            <a href="{{ route('documents.index') }}" class="hover:text-blue-600">Documents</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900">Générer un document</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Générer un document</h1>
        <p class="text-gray-600 mt-1">Sélectionnez un modèle et un contrat pour générer un document</p>
    </div>

    <!-- Messages -->
    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r" role="alert">
        <p class="font-medium">{{ session('error') }}</p>
    </div>
    @endif

    <form id="generation-form" action="{{ route('documents.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Sélection du contrat -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 inline-flex items-center justify-center text-sm mr-2">1</span>
                        Sélectionnez un contrat
                    </h2>

                    @if($contrats->count() > 0)
                        <div class="space-y-3">
                            @foreach($contrats as $contrat)
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition {{ $selectedContrat && $selectedContrat->id === $contrat->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <input type="radio" name="contrat_id" value="{{ $contrat->id }}" 
                                       {{ $selectedContrat && $selectedContrat->id === $contrat->id ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500" required>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $contrat->reference }}</p>
                                            <p class="text-sm text-gray-500">{{ $contrat->bien->adresse }}, {{ $contrat->bien->ville }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">
                                                @if($contrat->locataires->count() > 1)
                                                    {{ $contrat->locataires->count() }} locataires
                                                @else
                                                    {{ $contrat->locataires->first()->nom_complet ?? 'Aucun locataire' }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $contrat->type_bail_libelle }}</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun contrat actif</h3>
                            <p class="mt-1 text-sm text-gray-500">Vous devez créer un contrat avant de générer un document.</p>
                        </div>
                    @endif

                    @error('contrat_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sélection du modèle -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 inline-flex items-center justify-center text-sm mr-2">2</span>
                        Sélectionnez un modèle de document
                    </h2>

                    @if($templates->count() > 0)
                        <div class="space-y-4">
                            @foreach($templates as $type => $typeTemplates)
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                    <h3 class="text-sm font-medium text-gray-700">
                                        {{ $typeTemplates->first()->type_libelle }}
                                    </h3>
                                </div>
                                <div class="p-3 space-y-2">
                                    @foreach($typeTemplates as $template)
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition {{ old('template_id') == $template->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                        <input type="radio" name="template_id" value="{{ $template->id }}" 
                                               {{ old('template_id') == $template->id ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500" required>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $template->nom }}</p>
                                                </div>
                                                @if($template->is_default)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Par défaut
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun modèle actif</h3>
                            <p class="mt-1 text-sm text-gray-500">Créez d'abord un modèle de document.</p>
                            <div class="mt-4">
                                <a href="{{ route('document-templates.create') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Créer un modèle →
                                </a>
                            </div>
                        </div>
                    @endif

                    @error('template_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sélection du format -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 inline-flex items-center justify-center text-sm mr-2">3</span>
                        Choisissez le format
                    </h2>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- PDF -->
                        <label id="format-pdf-label" class="format-option flex flex-col items-center p-6 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition border-blue-500 bg-blue-50">
                            <input type="radio" name="format" value="pdf" id="format-pdf" checked class="hidden" required>
                            <svg class="w-16 h-16 text-red-500 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/>
                                <path d="M14 2v6h6M10 13H8v-2h2v2zm0 4H8v-2h2v2zm6-4h-2v-2h2v2zm0 4h-2v-2h2v2z"/>
                            </svg>
                            <span class="text-lg font-semibold text-gray-900">PDF</span>
                            <span class="text-sm text-gray-500 mt-1">Format universel</span>
                        </label>

                        <!-- Word -->
                        <label id="format-docx-label" class="format-option flex flex-col items-center p-6 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition border-gray-200">
                            <input type="radio" name="format" value="docx" id="format-docx" class="hidden">
                            <svg class="w-16 h-16 text-blue-500 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/>
                                <path d="M14 2v6h6"/>
                            </svg>
                            <span class="text-lg font-semibold text-gray-900">Word</span>
                            <span class="text-sm text-gray-500 mt-1">Modifiable (.docx)</span>
                        </label>
                    </div>

                    @error('format')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Colonne latérale - Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Actions</h2>
                    
                    <div class="space-y-3">
                        <!-- Prévisualiser -->
                        <button type="button" onclick="previewDocument()" 
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Prévisualiser
                        </button>

                        <!-- Générer -->
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Générer le document
                        </button>

                        <!-- Annuler -->
                        <a href="{{ route('documents.index') }}" 
                           class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            Annuler
                        </a>
                    </div>

                    <!-- Aide -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="text-sm font-semibold text-blue-800 mb-2">💡 Aide</h3>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li>• Sélectionnez un contrat actif</li>
                            <li>• Choisissez un modèle adapté</li>
                            <li>• Prévisualisez avant de générer</li>
                            <li>• Le document sera téléchargeable</li>
                        </ul>
                    </div>

                    <!-- Indicateur de format sélectionné -->
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm">
                        <strong>Format sélectionné :</strong>
                        <span id="selected-format-display" class="ml-2 font-medium text-blue-600">PDF</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// ✅ Gestion améliorée du changement de format
document.addEventListener('DOMContentLoaded', function() {
    const formatPdfLabel = document.getElementById('format-pdf-label');
    const formatDocxLabel = document.getElementById('format-docx-label');
    const formatPdfInput = document.getElementById('format-pdf');
    const formatDocxInput = document.getElementById('format-docx');
    const selectedFormatDisplay = document.getElementById('selected-format-display');
    
    // Fonction pour mettre à jour l'UI
    function updateFormatUI(selectedFormat) {
        if (selectedFormat === 'pdf') {
            formatPdfLabel.classList.add('border-blue-500', 'bg-blue-50');
            formatPdfLabel.classList.remove('border-gray-200');
            formatDocxLabel.classList.remove('border-blue-500', 'bg-blue-50');
            formatDocxLabel.classList.add('border-gray-200');
            formatPdfInput.checked = true;
            formatDocxInput.checked = false;
            selectedFormatDisplay.textContent = 'PDF';
        } else {
            formatDocxLabel.classList.add('border-blue-500', 'bg-blue-50');
            formatDocxLabel.classList.remove('border-gray-200');
            formatPdfLabel.classList.remove('border-blue-500', 'bg-blue-50');
            formatPdfLabel.classList.add('border-gray-200');
            formatDocxInput.checked = true;
            formatPdfInput.checked = false;
            selectedFormatDisplay.textContent = 'Word (DOCX)';
        }
        console.log('Format sélectionné :', selectedFormat);
    }
    
    // Click sur le label PDF
    formatPdfLabel.addEventListener('click', function(e) {
        e.preventDefault();
        updateFormatUI('pdf');
    });
    
    // Click sur le label Word
    formatDocxLabel.addEventListener('click', function(e) {
        e.preventDefault();
        updateFormatUI('docx');
    });
    
    // Click sur les inputs (au cas où)
    formatPdfInput.addEventListener('change', function() {
        if (this.checked) updateFormatUI('pdf');
    });
    
    formatDocxInput.addEventListener('change', function() {
        if (this.checked) updateFormatUI('docx');
    });
});

function previewDocument() {
    const form = document.getElementById('generation-form');
    const formData = new FormData(form);
    
    // Vérifier que le contrat et le modèle sont sélectionnés
    const contratId = formData.get('contrat_id');
    const templateId = formData.get('template_id');
    
    if (!contratId) {
        alert('⚠️ Veuillez sélectionner un contrat');
        return;
    }
    
    if (!templateId) {
        alert('⚠️ Veuillez sélectionner un modèle de document');
        return;
    }
    
    // Créer un formulaire temporaire pour la prévisualisation
    const previewForm = document.createElement('form');
    previewForm.method = 'POST';
    previewForm.action = '{{ route("documents.preview") }}';
    previewForm.target = '_blank';
    
    // Ajouter le token CSRF
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    previewForm.appendChild(csrfInput);
    
    // Ajouter les données
    const contratInput = document.createElement('input');
    contratInput.type = 'hidden';
    contratInput.name = 'contrat_id';
    contratInput.value = contratId;
    previewForm.appendChild(contratInput);
    
    const templateInput = document.createElement('input');
    templateInput.type = 'hidden';
    templateInput.name = 'template_id';
    templateInput.value = templateId;
    previewForm.appendChild(templateInput);
    
    // Soumettre
    document.body.appendChild(previewForm);
    previewForm.submit();
    document.body.removeChild(previewForm);
}
</script>
@endsection