@extends('layouts.app')

@section('title', 'Modifier le modèle - ' . $template->nom)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-2">
            <a href="{{ route('document-templates.index') }}" class="hover:text-blue-600">Modèles de documents</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('document-templates.show', $template) }}" class="hover:text-blue-600">{{ $template->nom }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900">Modifier</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Modifier le modèle</h1>
        <p class="text-gray-600 mt-1">{{ $template->nom }}</p>
    </div>

    <!-- Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r" role="alert">
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Formulaire -->
    <form action="{{ route('document-templates.update', $template) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('document-templates._form')
    </form>
</div>
@endsection