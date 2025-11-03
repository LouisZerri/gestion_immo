@extends('layouts.app')

@section('title', 'Notification - ' . $notification->titre)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('dashboard') }}" class="hover:text-blue-600">Tableau de bord</a></li>
            <li>/</li>
            <li><a href="{{ route('notifications.index') }}" class="hover:text-blue-600">Notifications</a></li>
            <li>/</li>
            <li class="text-gray-900 font-medium">{{ $notification->titre }}</li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-3">
                    <span class="text-3xl">{{ $notification->priorite_icon }}</span>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $notification->titre }}</h1>
                </div>

                <div class="flex flex-wrap gap-2">
                    <!-- Badge type -->
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full font-medium">
                        {{ $notification->type_libelle }}
                    </span>

                    <!-- Badge priorité -->
                    <span class="px-3 py-1 bg-{{ $notification->priorite_color }}-100 text-{{ $notification->priorite_color }}-800 text-sm rounded-full font-medium">
                        Priorité : {{ ucfirst($notification->priorite) }}
                    </span>

                    <!-- Badge statut -->
                    @if($notification->lue)
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full font-medium">
                        ✓ Lue le {{ $notification->lue_le->format('d/m/Y à H:i') }}
                    </span>
                    @else
                    <span class="px-3 py-1 bg-blue-600 text-white text-sm rounded-full font-medium">
                        Nouveau
                    </span>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2">
                <a href="{{ route('notifications.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">
                    ← Retour
                </a>

                <form method="POST" action="{{ route('notifications.destroy', $notification) }}" 
                      onsubmit="return confirm('Supprimer cette notification ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                        🗑️ Supprimer
                    </button>
                </form>
            </div>
        </div>

        <!-- Date -->
        <p class="text-sm text-gray-600">
            📅 Reçue le {{ $notification->created_at->format('d/m/Y à H:i') }} 
            ({{ $notification->created_at->diffForHumans() }})
        </p>
    </div>

    <!-- Message principal -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">📝 Message</h2>
        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $notification->message }}</p>
    </div>

    <!-- Métadonnées -->
    @if($notification->metadata && count($notification->metadata) > 0)
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">ℹ️ Informations détaillées</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($notification->metadata as $key => $value)
            <div class="flex items-start">
                <div class="flex-shrink-0 w-32 font-medium text-gray-600">
                    {{ ucfirst(str_replace('_', ' ', $key)) }} :
                </div>
                <div class="flex-1 text-gray-900">
                    @if(is_numeric($value))
                        {{ number_format($value, 2, ',', ' ') }}
                        @if(str_contains($key, 'montant'))
                            €
                        @endif
                    @else
                        {{ $value }}
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Contrat associé -->
    @if($notification->contrat)
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📄 Contrat associé</h2>
        
        <div class="space-y-3">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-32 font-medium text-gray-600">Référence :</div>
                <div class="flex-1">
                    <a href="{{ route('contrats.show', $notification->contrat) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium">
                        {{ $notification->contrat->reference }}
                    </a>
                </div>
            </div>

            @if($notification->contrat->bien)
            <div class="flex items-start">
                <div class="flex-shrink-0 w-32 font-medium text-gray-600">Bien :</div>
                <div class="flex-1">
                    <a href="{{ route('biens.show', $notification->contrat->bien) }}" 
                       class="text-blue-600 hover:text-blue-800">
                        {{ $notification->contrat->bien->adresse }}, 
                        {{ $notification->contrat->bien->code_postal }} 
                        {{ $notification->contrat->bien->ville }}
                    </a>
                </div>
            </div>
            @endif

            <div class="flex items-start">
                <div class="flex-shrink-0 w-32 font-medium text-gray-600">Loyer CC :</div>
                <div class="flex-1 font-semibold text-gray-900">
                    {{ number_format($notification->contrat->loyer_cc, 2, ',', ' ') }} €
                </div>
            </div>

            @if($notification->contrat->locataires->isNotEmpty())
            <div class="flex items-start">
                <div class="flex-shrink-0 w-32 font-medium text-gray-600">Locataire(s) :</div>
                <div class="flex-1">
                    @foreach($notification->contrat->locataires as $locataire)
                    <a href="{{ route('locataires.show', $locataire) }}" 
                       class="text-blue-600 hover:text-blue-800">
                        {{ $locataire->nom_complet }}
                    </a>{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <a href="{{ route('contrats.show', $notification->contrat) }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                Voir le contrat complet 
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
    @endif

    <!-- Document associé -->
    @if($notification->document)
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📎 Document associé</h2>
        
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center space-x-4">
                <div class="text-4xl">
                    @if($notification->document->format === 'pdf')
                        📄
                    @else
                        📝
                    @endif
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ $notification->document->nom }}</p>
                    <p class="text-sm text-gray-600">
                        {{ $notification->document->type_libelle }} • 
                        {{ $notification->document->format_libelle }} • 
                        {{ $notification->document->file_size_formatted }}
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('documents.show', $notification->document) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    Voir le document
                </a>
                <a href="{{ route('documents.download', $notification->document) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                    Télécharger
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Bien associé -->
    @if($notification->bien && !$notification->contrat)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">🏠 Bien associé</h2>
        
        <div class="space-y-2">
            <p class="text-gray-900">
                <strong>{{ $notification->bien->adresse }}</strong>
            </p>
            <p class="text-gray-600">
                {{ $notification->bien->code_postal }} {{ $notification->bien->ville }}
            </p>
            <p class="text-sm text-gray-600">
                {{ $notification->bien->type_libelle }} • 
                {{ $notification->bien->surface }} m² • 
                {{ $notification->bien->nombre_pieces }} pièce(s)
            </p>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <a href="{{ route('biens.show', $notification->bien) }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                Voir le bien complet 
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
    @endif
</div>
@endsection