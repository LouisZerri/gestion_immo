@extends('layouts.app')

@section('title', 'Créer un modèle de document')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-2">
            <a href="{{ route('document-templates.index') }}" class="hover:text-blue-600">Modèles de documents</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900">Nouveau modèle</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Créer un modèle de document</h1>
        <p class="text-gray-600 mt-1">Configurez un nouveau modèle pour vos contrats, quittances ou autres documents</p>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('document-templates.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('document-templates._form')
    </form>
</div>
@endsection