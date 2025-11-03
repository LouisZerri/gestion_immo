<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentShareController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\DocumentUploadController;
use App\Http\Controllers\LocataireController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProprietaireController;
use Illuminate\Support\Facades\Route;

// ========================================
// ROUTES PUBLIQUES (sans authentification)
// ========================================

// Page de connexion
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Page d'inscription
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ========================================
// ROUTES PROTÉGÉES (authentification requise)
// ========================================

Route::middleware('auth')->group(function () {
    
    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (page d'accueil après connexion)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // ========================================
    // ROUTES POUR GESTIONNAIRES UNIQUEMENT
    // ========================================
    
    Route::middleware('role:super_admin,gestionnaire')->group(function () {
        
        // Routes CRUD Propriétaires
        Route::resource('proprietaires', ProprietaireController::class);
        Route::post('proprietaires/{proprietaire}/toggle-mandat', [ProprietaireController::class, 'toggleMandat'])
            ->name('proprietaires.toggle-mandat');

        // Routes CRUD Biens
        Route::resource('biens', BienController::class);

        // Routes CRUD Locataires
        Route::resource('locataires', LocataireController::class);

        // Routes CRUD Contrats
        Route::resource('contrats', ContratController::class);
        Route::post('contrats/{contrat}/resilier', [ContratController::class, 'resilier'])
            ->name('contrats.resilier');
        Route::post('contrats/{contrat}/renouveler', [ContratController::class, 'renouveler'])
            ->name('contrats.renouveler');
        Route::post('contrats/{contrat}/generer-document', [ContratController::class, 'genererDocument'])
            ->name('contrats.generer-document');

        // Routes pour les modèles de documents
        Route::prefix('document-templates')->name('document-templates.')->group(function () {
            Route::get('/', [DocumentTemplateController::class, 'index'])->name('index');
            Route::get('/create', [DocumentTemplateController::class, 'create'])->name('create');
            Route::post('/', [DocumentTemplateController::class, 'store'])->name('store');
            Route::get('/{documentTemplate}', [DocumentTemplateController::class, 'show'])->name('show');
            Route::get('/{documentTemplate}/edit', [DocumentTemplateController::class, 'edit'])->name('edit');
            Route::put('/{documentTemplate}', [DocumentTemplateController::class, 'update'])->name('update');
            Route::delete('/{documentTemplate}', [DocumentTemplateController::class, 'destroy'])->name('destroy');
            Route::post('/{documentTemplate}/duplicate', [DocumentTemplateController::class, 'duplicate'])->name('duplicate');
            Route::patch('/{documentTemplate}/toggle-active', [DocumentTemplateController::class, 'toggleActive'])->name('toggle-active');
            Route::get('/{documentTemplate}/preview', [DocumentTemplateController::class, 'preview'])->name('preview');
        });

        // ✅ ROUTES UPLOAD DE DOCUMENTS EXTERNES
        Route::get('/documents/upload/create', [DocumentUploadController::class, 'create'])->name('documents.upload.create');
        Route::post('/documents/upload', [DocumentUploadController::class, 'store'])->name('documents.upload.store');

        // ✅ ROUTES PARTAGE DE DOCUMENTS
        Route::get('/documents/{document}/share', [DocumentShareController::class, 'create'])->name('documents.share.create');
        Route::post('/documents/{document}/share', [DocumentShareController::class, 'store'])->name('documents.share.store');
        Route::delete('/documents/{document}/share', [DocumentShareController::class, 'destroy'])->name('documents.share.destroy');

        // Routes pour générer des documents (accessible aux gestionnaires)
        Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents/preview', [DocumentController::class, 'preview'])->name('documents.preview');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::post('/documents/{document}/regenerate', [DocumentController::class, 'regenerate'])->name('documents.regenerate');
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    });

    // ========================================
    // ROUTES DOCUMENTS (tous utilisateurs authentifiés)
    // ========================================
    
    // Consultation des documents (avec filtrage automatique selon le rôle)
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    
    // Documents par contrat
    Route::get('/contrats/{contrat}/documents', [DocumentController::class, 'byContrat'])->name('contrats.documents');

    // ========================================
    // 🆕 ROUTES NOTIFICATIONS (tous utilisateurs authentifiés)
    // ========================================
    
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::patch('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        
        // API endpoints (pour badge et dropdown)
        Route::get('/api/count-unread', [NotificationController::class, 'countUnread'])->name('count-unread');
        Route::get('/api/recent', [NotificationController::class, 'recent'])->name('recent');
    });
});