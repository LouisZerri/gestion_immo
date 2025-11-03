@extends('layouts.app')

@section('title', 'Remplir l\'état des lieux')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <div class="flex items-center text-sm text-gray-600 mb-2">
                    <a href="{{ route('etats-des-lieux.index') }}" class="hover:text-blue-600">États des lieux</a>
                    <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-gray-900">Remplir</span>
                </div>
                <div class="flex items-center">
                    <h1 class="text-3xl font-bold text-gray-800 mr-3">{{ $etatDesLieux->type_libelle }}</h1>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $etatDesLieux->statut_color }}">
                        {{ $etatDesLieux->statut_libelle }}
                    </span>
                </div>
                @if ($etatDesLieux->bien)
                    <p class="text-gray-600 mt-1">
                        <strong>{{ $etatDesLieux->bien->reference }}</strong> - {{ $etatDesLieux->bien->adresse }},
                        {{ $etatDesLieux->bien->ville }}
                    </p>
                @else
                    <p class="text-red-600 mt-1">⚠️ Aucun bien associé</p>
                @endif
            </div>
            <div class="flex space-x-2">
                @if ($etatDesLieux->isBrouillon())
                    <form action="{{ route('etats-des-lieux.terminer', $etatDesLieux) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                            <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Marquer terminé
                        </button>
                    </form>
                @endif

                <a href="{{ route('etats-des-lieux.pdf', $etatDesLieux) }}" target="_blank"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Générer PDF
                </a>
            </div>
        </div>

        <!-- Messages -->
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r" role="alert">
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r" role="alert">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <form id="edl-form" action="{{ route('etats-des-lieux.update', $etatDesLieux) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Navigation des pièces (sidebar gauche) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-4 sticky top-4">
                        <h3 class="font-semibold text-gray-800 mb-3">Navigation</h3>
                        <nav class="space-y-1">
                            <a href="#general"
                                class="block px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-md transition">
                                📋 Informations générales
                            </a>
                            <a href="#compteurs"
                                class="block px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-md transition">
                                ⚡ Compteurs & Clés
                            </a>
                            <hr class="my-2">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pièces</p>
                            @foreach ($etatDesLieux->pieces as $piece)
                                <a href="#piece-{{ $piece->id }}"
                                    class="block px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-md transition">
                                    🚪 {{ $piece->nom_piece }}
                                    @if ($piece->hasPhotos())
                                        <span class="ml-1 text-xs text-blue-600">({{ $piece->getPhotosCount() }}📷)</span>
                                    @endif
                                </a>
                            @endforeach
                        </nav>

                        <!-- Bouton ajouter pièce -->
                        <button type="button" onclick="openModalAjouterPiece()"
                            class="mt-4 w-full bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium py-2 px-3 rounded-md transition">
                            + Ajouter une pièce
                        </button>

                        <!-- Sauvegarder -->
                        <button type="submit"
                            class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md transition">
                            💾 Sauvegarder
                        </button>
                    </div>
                </div>

                <!-- Contenu principal -->
                <div class="lg:col-span-3 space-y-6">

                    <!-- Section : Informations générales -->
                    <div id="general" class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">📋 Informations générales</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="date_etat" class="block text-sm font-medium text-gray-700 mb-1">
                                    Date de l'état des lieux *
                                </label>
                                <input type="date" name="date_etat" id="date_etat" required
                                    value="{{ old('date_etat', $etatDesLieux->date_etat->format('Y-m-d')) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-1">Type</p>
                                <p class="text-gray-900 font-semibold">{{ $etatDesLieux->type_libelle }}</p>
                            </div>
                        </div>

                        <div>
                            <label for="observations_generales" class="block text-sm font-medium text-gray-700 mb-1">
                                Observations générales
                            </label>
                            <textarea name="observations_generales" id="observations_generales" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Observations, remarques générales...">{{ old('observations_generales', $etatDesLieux->observations_generales) }}</textarea>
                        </div>
                    </div>
                    <!-- Section : Compteurs et Clés -->
                    <div id="compteurs" class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">⚡ Compteurs, Chauffage & Clés</h2>

                        <!-- Compteurs -->
                        <div class="mb-6">
                            <h3 class="text-md font-semibold text-gray-700 mb-3">Relevés des compteurs</h3>
                            <div class="space-y-3">
                                <!-- Eau -->
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <p class="font-medium text-sm text-gray-700 mb-2">💧 Eau</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        <input type="text" name="compteurs_eau[numero_serie]" placeholder="N° série"
                                            value="{{ old('compteurs_eau.numero_serie', $etatDesLieux->compteurs_eau['numero_serie'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                        <input type="text" name="compteurs_eau[m3]" placeholder="m³"
                                            value="{{ old('compteurs_eau.m3', $etatDesLieux->compteurs_eau['m3'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                        <input type="text" name="compteurs_eau[fonctionnement]"
                                            placeholder="Fonctionnement"
                                            value="{{ old('compteurs_eau.fonctionnement', $etatDesLieux->compteurs_eau['fonctionnement'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                        <input type="text" name="compteurs_eau[commentaire]" placeholder="Commentaire"
                                            value="{{ old('compteurs_eau.commentaire', $etatDesLieux->compteurs_eau['commentaire'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                    </div>
                                </div>

                                <!-- Gaz -->
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <p class="font-medium text-sm text-gray-700 mb-2">🔥 Gaz</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        <input type="text" name="compteurs_gaz[numero_serie]" placeholder="N° série"
                                            value="{{ old('compteurs_gaz.numero_serie', $etatDesLieux->compteurs_gaz['numero_serie'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                        <input type="text" name="compteurs_gaz[m3]" placeholder="m³"
                                            value="{{ old('compteurs_gaz.m3', $etatDesLieux->compteurs_gaz['m3'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                        <input type="text" name="compteurs_gaz[fonctionnement]"
                                            placeholder="Fonctionnement"
                                            value="{{ old('compteurs_gaz.fonctionnement', $etatDesLieux->compteurs_gaz['fonctionnement'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                        <input type="text" name="compteurs_gaz[commentaire]" placeholder="Commentaire"
                                            value="{{ old('compteurs_gaz.commentaire', $etatDesLieux->compteurs_gaz['commentaire'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                    </div>
                                </div>

                                <!-- Électricité -->
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <p class="font-medium text-sm text-gray-700 mb-2">⚡ Électricité</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        <input type="text" name="compteurs_electricite[numero_serie]"
                                            placeholder="N° série"
                                            value="{{ old('compteurs_electricite.numero_serie', $etatDesLieux->compteurs_electricite['numero_serie'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                        <input type="text" name="compteurs_electricite[kwh]" placeholder="kWh"
                                            value="{{ old('compteurs_electricite.kwh', $etatDesLieux->compteurs_electricite['kwh'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                        <input type="text" name="compteurs_electricite[fonctionnement]"
                                            placeholder="Fonctionnement"
                                            value="{{ old('compteurs_electricite.fonctionnement', $etatDesLieux->compteurs_electricite['fonctionnement'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                        <input type="text" name="compteurs_electricite[commentaire]"
                                            placeholder="Commentaire"
                                            value="{{ old('compteurs_electricite.commentaire', $etatDesLieux->compteurs_electricite['commentaire'] ?? '') }}"
                                            class="px-2 py-1 text-sm border border-gray-300 rounded">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remise des clés -->
                        <div>
                            <h3 class="text-md font-semibold text-gray-700 mb-3">🔑 Remise des clés</h3>
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                    <input type="text" name="cles[type]" placeholder="Type de clé"
                                        value="{{ old('cles.type', $etatDesLieux->cles['type'] ?? '') }}"
                                        class="px-2 py-1 text-sm border border-gray-300 rounded">
                                    <input type="number" name="cles[nombre]" placeholder="Nombre"
                                        value="{{ old('cles.nombre', $etatDesLieux->cles['nombre'] ?? '') }}"
                                        class="px-2 py-1 text-sm border border-gray-300 rounded">
                                    <input type="date" name="cles[date]"
                                        value="{{ old('cles.date', $etatDesLieux->cles['date'] ?? '') }}"
                                        class="px-2 py-1 text-sm border border-gray-300 rounded">
                                    <input type="text" name="cles[commentaire]" placeholder="Commentaire"
                                        value="{{ old('cles.commentaire', $etatDesLieux->cles['commentaire'] ?? '') }}"
                                        class="px-2 py-1 text-sm border border-gray-300 rounded">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PIÈCES -->
                    @foreach ($etatDesLieux->pieces as $piece)
                        <div id="piece-{{ $piece->id }}" class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-800">🚪 {{ $piece->nom_piece }}</h2>
                                <button type="button" onclick="openModalAjouterElement({{ $piece->id }})"
                                    class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium py-1 px-3 rounded transition">
                                    + Ajouter un élément
                                </button>
                            </div>

                            <!-- Commentaires sur la pièce -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Commentaires sur la pièce
                                </label>
                                <textarea name="pieces[{{ $piece->id }}][commentaires_piece]" rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Observations générales sur cette pièce...">{{ old("pieces.{$piece->id}.commentaires_piece", $piece->commentaires_piece) }}</textarea>
                            </div>

                            <!-- Tableau des éléments -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Élément</th>
                                            <th
                                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nature</th>
                                            <th
                                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                État d'usure</th>
                                            <th
                                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Fonctionnement</th>
                                            <th
                                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Commentaires</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($piece->elements as $element)
                                            <tr>
                                                <td class="px-3 py-2 text-sm font-medium text-gray-900 whitespace-nowrap">
                                                    {{ $element->element }}
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text"
                                                        name="pieces[{{ $piece->id }}][elements][{{ $element->id }}][nature]"
                                                        value="{{ old("pieces.{$piece->id}.elements.{$element->id}.nature", $element->nature) }}"
                                                        placeholder="Ex: Carrelage, Peinture..."
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <select
                                                        name="pieces[{{ $piece->id }}][elements][{{ $element->id }}][etat_usure]"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                        <option value="">-</option>
                                                        <option value="Neuf"
                                                            {{ $element->etat_usure === 'Neuf' ? 'selected' : '' }}>Neuf
                                                        </option>
                                                        <option value="Bon état"
                                                            {{ $element->etat_usure === 'Bon état' ? 'selected' : '' }}>Bon
                                                            état</option>
                                                        <option value="État moyen"
                                                            {{ $element->etat_usure === 'État moyen' ? 'selected' : '' }}>
                                                            État moyen</option>
                                                        <option value="Mauvais état"
                                                            {{ $element->etat_usure === 'Mauvais état' ? 'selected' : '' }}>
                                                            Mauvais état</option>
                                                        <option value="Vétuste"
                                                            {{ $element->etat_usure === 'Vétuste' ? 'selected' : '' }}>
                                                            Vétuste</option>
                                                    </select>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <select
                                                        name="pieces[{{ $piece->id }}][elements][{{ $element->id }}][fonctionnement]"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                        <option value="">-</option>
                                                        <option value="Fonctionne"
                                                            {{ $element->fonctionnement === 'Fonctionne' ? 'selected' : '' }}>
                                                            Fonctionne</option>
                                                        <option value="Ne fonctionne pas"
                                                            {{ $element->fonctionnement === 'Ne fonctionne pas' ? 'selected' : '' }}>
                                                            Ne fonctionne pas</option>
                                                        <option value="N/A"
                                                            {{ $element->fonctionnement === 'N/A' ? 'selected' : '' }}>N/A
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text"
                                                        name="pieces[{{ $piece->id }}][elements][{{ $element->id }}][commentaires]"
                                                        value="{{ old("pieces.{$piece->id}.elements.{$element->id}.commentaires", $element->commentaires) }}"
                                                        placeholder="Remarques..."
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Photos de la pièce -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center mb-3">
                                    <h3 class="text-md font-semibold text-gray-700">📷 Photos
                                        ({{ $piece->getPhotosCount() }})</h3>
                                    <button type="button" onclick="openModalUploadPhotos({{ $piece->id }})"
                                        class="text-sm bg-green-100 hover:bg-green-200 text-green-700 font-medium py-1 px-3 rounded transition">
                                        + Ajouter des photos
                                    </button>
                                </div>

                                @if ($piece->hasPhotos())
                                    <div id="photos-piece-{{ $piece->id }}"
                                        class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        @foreach ($piece->photos as $photo)
                                            <div class="relative group">
                                                <img src="{{ Storage::url($photo) }}" alt="Photo"
                                                    class="w-full h-32 object-cover rounded border cursor-pointer"
                                                    onclick="openImageModal('{{ Storage::url($photo) }}')">
                                                <button type="button"
                                                    onclick="deletePhoto({{ $piece->id }}, '{{ $photo }}')"
                                                    class="absolute top-1 right-1 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic">Aucune photo pour cette pièce</p>
                                @endif
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </form>
    </div>
    <!-- Modal : Ajouter une pièce -->
    <div id="modal-ajouter-piece"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Ajouter une pièce</h3>
            <form action="{{ route('etats-des-lieux.ajouter-piece', $etatDesLieux) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="nom_piece" class="block text-sm font-medium text-gray-700 mb-1">
                        Nom de la pièce *
                    </label>
                    <input type="text" name="nom_piece" id="nom_piece" required
                        placeholder="Ex: CHAMBRE 2, BUREAU, CAVE..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeModalAjouterPiece()"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md transition">
                        Annuler
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal : Ajouter un élément -->
    <div id="modal-ajouter-element"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Ajouter un élément</h3>
            <form id="form-ajouter-element" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="element" class="block text-sm font-medium text-gray-700 mb-1">
                        Nom de l'élément *
                    </label>
                    <input type="text" name="element" id="element" required
                        placeholder="Ex: RADIATEUR, MIROIR, ETAGERE..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeModalAjouterElement()"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md transition">
                        Annuler
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal : Upload photos -->
    <div id="modal-upload-photos"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Ajouter des photos</h3>
            <form id="form-upload-photos" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="photos" class="block text-sm font-medium text-gray-700 mb-1">
                        Sélectionner des photos (max 5 Mo chacune)
                    </label>
                    <input type="file" name="photos[]" id="photos" multiple accept="image/*" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Formats : JPG, PNG, GIF, WEBP</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeModalUploadPhotos()"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md transition">
                        Annuler
                    </button>
                    <button type="submit"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition">
                        Uploader
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal : Afficher image en grand -->
    <div id="image-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4"
        onclick="closeImageModal()">
        <img id="modal-image" src="" alt="Image" class="max-w-full max-h-full rounded-lg">
    </div>

    <script>
        // Variables globales
        let currentPieceId = null;

        // Modal : Ajouter une pièce
        function openModalAjouterPiece() {
            document.getElementById('modal-ajouter-piece').classList.remove('hidden');
        }

        function closeModalAjouterPiece() {
            document.getElementById('modal-ajouter-piece').classList.add('hidden');
        }

        // Modal : Ajouter un élément
        function openModalAjouterElement(pieceId) {
            currentPieceId = pieceId;
            const form = document.getElementById('form-ajouter-element');
            form.action = `/edl-pieces/${pieceId}/ajouter-element`;
            document.getElementById('modal-ajouter-element').classList.remove('hidden');
        }

        function closeModalAjouterElement() {
            document.getElementById('modal-ajouter-element').classList.add('hidden');
            currentPieceId = null;
        }

        // Modal : Upload photos
        function openModalUploadPhotos(pieceId) {
            currentPieceId = pieceId;
            document.getElementById('modal-upload-photos').classList.remove('hidden');
        }

        function closeModalUploadPhotos() {
            document.getElementById('modal-upload-photos').classList.add('hidden');
            document.getElementById('form-upload-photos').reset();
            currentPieceId = null;
        }

        // Upload photos (AJAX)
        document.getElementById('form-upload-photos').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!currentPieceId) {
                alert('Erreur : Pièce non sélectionnée');
                return;
            }

            const formData = new FormData(this);
            const url = `/edl-pieces/${currentPieceId}/photos`;

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Photos ajoutées avec succès !');
                        closeModalUploadPhotos();
                        location.reload(); // Recharger pour afficher les nouvelles photos
                    } else {
                        alert('❌ Erreur : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('❌ Erreur lors de l\'upload');
                });
        });

        // Supprimer une photo (AJAX)
        function deletePhoto(pieceId, photoPath) {
            if (!confirm('Supprimer cette photo ?')) {
                return;
            }

            fetch(`/edl-pieces/${pieceId}/photos`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        photo_path: photoPath
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Photo supprimée');
                        location.reload();
                    } else {
                        alert('❌ Erreur : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('❌ Erreur lors de la suppression');
                });
        }

        // Afficher image en grand
        function openImageModal(src) {
            document.getElementById('modal-image').src = src;
            document.getElementById('image-modal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('image-modal').classList.add('hidden');
        }

        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalAjouterPiece();
                closeModalAjouterElement();
                closeModalUploadPhotos();
                closeImageModal();
            }
        });

        // Sauvegarde automatique (toutes les 2 minutes)
        setInterval(function() {
            const form = document.getElementById('edl-form');
            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(() => {
                    console.log('✅ Sauvegarde automatique effectuée');
                })
                .catch(error => {
                    console.error('❌ Erreur sauvegarde auto:', error);
                });
        }, 120000); // 2 minutes

        // Confirmation avant quitter sans sauvegarder
        let formModified = false;
        document.getElementById('edl-form').addEventListener('change', function() {
            formModified = true;
        });

        document.getElementById('edl-form').addEventListener('submit', function() {
            formModified = false;
        });

        window.addEventListener('beforeunload', function(e) {
            if (formModified) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>

@endsection
