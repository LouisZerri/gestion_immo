@extends('layouts.app')

@section('title', 'Partager le document')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-4">
            <a href="{{ route('documents.index') }}" class="hover:text-blue-600">Documents</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('documents.show', $document) }}" class="hover:text-blue-600">{{ $document->nom }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900">Partager</span>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900">Partager le document</h1>
        <p class="text-gray-600 mt-2">{{ $document->nom }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulaire de partage -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('documents.share.store', $document) }}">
                    @csrf

                    <!-- Sélection des utilisateurs -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Sélectionner les utilisateurs <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="space-y-2 max-h-96 overflow-y-auto border border-gray-300 rounded-lg p-4">
                            @forelse($users as $user)
                                <label class="flex items-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                    <input 
                                        type="checkbox" 
                                        name="user_ids[]" 
                                        value="{{ $user->id }}"
                                        {{ in_array($user->id, $sharedUserIds) ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                    >
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                {{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $user->role === 'gestionnaire' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $user->role === 'proprietaire' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $user->role === 'locataire' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            ">
                                                {{ $user->role_label }}
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <p class="text-center text-gray-500 py-4">Aucun utilisateur disponible pour le partage.</p>
                            @endforelse
                        </div>
                        
                        @error('user_ids')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permissions -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Permissions <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="space-y-3">
                            <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input 
                                    type="radio" 
                                    name="permissions" 
                                    value="view"
                                    checked
                                    class="mt-1 w-4 h-4 text-blue-600 border-gray-300 focus:ring-2 focus:ring-blue-500"
                                >
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">👁️ Lecture seule</p>
                                    <p class="text-xs text-gray-500">Les utilisateurs peuvent uniquement consulter le document</p>
                                </div>
                            </label>

                            <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input 
                                    type="radio" 
                                    name="permissions" 
                                    value="download"
                                    class="mt-1 w-4 h-4 text-blue-600 border-gray-300 focus:ring-2 focus:ring-blue-500"
                                >
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">⬇️ Téléchargement</p>
                                    <p class="text-xs text-gray-500">Les utilisateurs peuvent consulter et télécharger le document</p>
                                </div>
                            </label>
                        </div>
                        
                        @error('permissions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Boutons -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <a href="{{ route('documents.show', $document) }}" class="text-gray-600 hover:text-gray-900">
                            ← Retour
                        </a>
                        <button 
                            type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors font-medium"
                        >
                            🔗 Partager le document
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informations -->
        <div class="space-y-6">
            <!-- Document info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">📄 Document</h3>
                <p class="text-sm text-blue-800">{{ $document->nom }}</p>
                <p class="text-xs text-blue-600 mt-1">{{ $document->type_libelle }}</p>
            </div>

            <!-- Déjà partagé avec -->
            @if(count($sharedUserIds) > 0)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-green-900 mb-2">✅ Déjà partagé avec</h3>
                <p class="text-xs text-green-700">{{ count($sharedUserIds) }} utilisateur(s)</p>
            </div>
            @endif

            <!-- Aide -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">💡 Aide</h3>
                <ul class="text-xs text-gray-600 space-y-1">
                    <li>• Sélectionnez les utilisateurs avec qui partager</li>
                    <li>• Choisissez les permissions appropriées</li>
                    <li>• Les utilisateurs recevront une notification</li>
                    <li>• Vous pouvez retirer le partage à tout moment</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection