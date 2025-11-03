@extends('layouts.app')

@section('title', $document->nom)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-2">
            <a href="{{ route('documents.index') }}" class="hover:text-blue-600">Documents</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900">{{ $document->nom }}</span>
        </div>
        
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $document->nom }}</h1>
                <div class="flex items-center mt-2 space-x-3">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $document->template->type_libelle }}
                    </span>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $document->format === 'pdf' ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800' }}">
                        {{ strtoupper($document->format) }}
                    </span>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('documents.download', $document) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Télécharger
                </a>
            </div>
        </div>
    </div>

    <!-- Messages de succès -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r" role="alert">
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Aperçu du contenu -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Aperçu du document</h2>
                <div class="prose max-w-none border border-gray-200 rounded p-4 bg-gray-50 overflow-x-auto max-h-[600px] overflow-y-auto">
                    {!! $document->contenu !!}
                </div>
            </div>

            <!-- Historique -->
            @if($document->logs->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Historique</h2>
                
                <div class="flow-root">
                    <ul class="-mb-8">
                        @foreach($document->logs as $log)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            @if($log->action === 'generated')
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            @elseif($log->action === 'downloaded')
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">
                                                @if($log->action === 'generated')
                                                    <span class="font-medium text-gray-900">Document généré</span>
                                                @elseif($log->action === 'downloaded')
                                                    <span class="font-medium text-gray-900">Document téléchargé</span>
                                                @else
                                                    <span class="font-medium text-gray-900">{{ $log->action }}</span>
                                                @endif
                                                @if($log->user)
                                                    par <span class="font-medium text-gray-900">{{ $log->user->name }}</span>
                                                @endif
                                            </p>
                                            @if($log->details)
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ collect($log->details)->map(fn($v, $k) => "$k: $v")->implode(' • ') }}
                                            </p>
                                            @endif
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $log->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>

        <!-- Colonne latérale -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Informations -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations</h2>
                
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Modèle utilisé</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ route('document-templates.show', $document->template) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $document->template->nom }}
                            </a>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type de document</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->template->type_libelle }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Format</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $document->format === 'pdf' ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800' }}">
                                {{ strtoupper($document->format) }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Contrat</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $document->contrat->reference }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Bien</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $document->bien->adresse }}<br>
                            {{ $document->bien->code_postal }} {{ $document->bien->ville }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Locataire(s)</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @foreach($document->contrat->locataires as $locataire)
                                {{ $locataire->nom_complet }}<br>
                            @endforeach
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Généré le</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $document->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Taille du fichier</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if(Storage::exists($document->file_path))
                                {{ number_format(Storage::size($document->file_path) / 1024, 2) }} Ko
                            @else
                                N/A
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Actions</h2>
                
                <div class="space-y-2">
                    <a href="{{ route('documents.download', $document) }}" 
                       class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded transition duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Télécharger
                    </a>

                    <form action="{{ route('documents.regenerate', $document) }}" method="POST">
                        @csrf
                        <input type="hidden" name="format" value="{{ $document->format }}">
                        <button type="submit" 
                                class="w-full bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium py-2 px-4 rounded transition duration-200 flex items-center justify-center"
                                onclick="return confirm('Êtes-vous sûr de vouloir régénérer ce document ?');">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Régénérer
                        </button>
                    </form>

                    <form action="{{ route('documents.destroy', $document) }}" method="POST" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document ? Cette action est irréversible.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-100 hover:bg-red-200 text-red-700 font-medium py-2 px-4 rounded transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <!-- Métadonnées -->
            @if($document->metadata)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Métadonnées</h2>
                
                <dl class="space-y-2 text-sm">
                    @foreach($document->metadata as $key => $value)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                        <dd class="text-gray-900 font-medium">{{ $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection