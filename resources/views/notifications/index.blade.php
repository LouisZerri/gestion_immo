@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">🔔 Notifications</h1>
            <p class="text-gray-600 mt-1">{{ $countNonLues }} notification(s) non lue(s)</p>
        </div>
        
        @if($countNonLues > 0)
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Tout marquer comme lu
            </button>
        </form>
        @endif
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('notifications.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les types</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Priorité -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                <select name="priorite" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Toutes les priorités</option>
                    @foreach($priorites as $key => $label)
                        <option value="{{ $key }}" {{ request('priorite') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Statut -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($statuts as $key => $label)
                        <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Boutons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Filtrer
                </button>
                <a href="{{ route('notifications.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des notifications -->
    <div class="space-y-3">
        @forelse($notifications as $notification)
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow {{ $notification->lue ? 'opacity-75' : 'border-l-4 border-blue-500' }}">
            <div class="p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <!-- En-tête -->
                        <div class="flex items-center space-x-3 mb-2">
                            <!-- Icône priorité -->
                            <span class="text-2xl">{{ $notification->priorite_icon }}</span>
                            
                            <!-- Titre -->
                            <h3 class="font-semibold text-gray-900 {{ !$notification->lue ? 'font-bold' : '' }}">
                                {{ $notification->titre }}
                            </h3>

                            <!-- Badge type -->
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">
                                {{ $notification->type_libelle }}
                            </span>

                            <!-- Badge priorité -->
                            <span class="px-2 py-1 bg-{{ $notification->priorite_color }}-100 text-{{ $notification->priorite_color }}-800 text-xs rounded-full">
                                {{ ucfirst($notification->priorite) }}
                            </span>

                            <!-- Badge non lu -->
                            @if(!$notification->lue)
                            <span class="px-2 py-1 bg-blue-600 text-white text-xs rounded-full font-semibold">
                                Nouveau
                            </span>
                            @endif
                        </div>

                        <!-- Message -->
                        <p class="text-gray-700 mb-2">{{ $notification->message }}</p>

                        <!-- Métadonnées -->
                        @if($notification->metadata)
                        <div class="flex flex-wrap gap-3 text-sm text-gray-600 mb-2">
                            @if(isset($notification->metadata['montant']))
                            <span>💰 {{ number_format($notification->metadata['montant'], 2, ',', ' ') }} €</span>
                            @endif

                            @if(isset($notification->metadata['jours_retard']))
                            <span>⏰ {{ $notification->metadata['jours_retard'] }} jour(s) de retard</span>
                            @endif

                            @if(isset($notification->metadata['adresse_bien']))
                            <span>📍 {{ $notification->metadata['adresse_bien'] }}</span>
                            @endif
                        </div>
                        @endif

                        <!-- Date -->
                        <p class="text-xs text-gray-500">
                            📅 {{ $notification->created_at->diffForHumans() }} 
                            ({{ $notification->created_at->format('d/m/Y H:i') }})
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-start space-x-2 ml-4">
                        <!-- Voir -->
                        <a href="{{ route('notifications.show', $notification) }}" 
                           class="text-blue-600 hover:text-blue-800 p-2 rounded hover:bg-blue-50"
                           title="Voir les détails">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>

                        <!-- Marquer comme lu -->
                        @if(!$notification->lue)
                        <form method="POST" action="{{ route('notifications.mark-read', $notification) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="text-green-600 hover:text-green-800 p-2 rounded hover:bg-green-50"
                                    title="Marquer comme lu">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </form>
                        @endif

                        <!-- Supprimer -->
                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}" 
                              onsubmit="return confirm('Supprimer cette notification ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-600 hover:text-red-800 p-2 rounded hover:bg-red-50"
                                    title="Supprimer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-600 text-lg">Aucune notification</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
@endsection