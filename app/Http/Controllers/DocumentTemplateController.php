<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Http\Requests\DocumentTemplateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DocumentTemplate::query();

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par statut
        if ($request->filled('actif')) {
            $query->where('actif', $request->actif);
        }

        // Recherche par nom
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        $templates = $query->orderBy('type')->orderBy('nom')->paginate(15);

        // Liste des types pour le filtre
        $types = $this->getTypes();

        return view('document-templates.index', compact('templates', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $template = new DocumentTemplate();
        $types = $this->getTypes();
        $bienTypes = $this->getBienTypes();
        $availableTags = $this->getAvailableTags();

        return view('document-templates.create', compact('template', 'types', 'bienTypes', 'availableTags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DocumentTemplateRequest $request)
    {
        $data = $request->validated();

        // Gestion de l'upload du logo
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('templates/logos', 'public');
        }

        // Gestion de l'upload de la signature
        if ($request->hasFile('signature')) {
            $data['signature_path'] = $request->file('signature')->store('templates/signatures', 'public');
        }

        // Si marqué comme défaut, désactiver les autres défauts du même type
        if ($data['is_default'] ?? false) {
            DocumentTemplate::where('type', $data['type'])
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $template = DocumentTemplate::create($data);

        return redirect()
            ->route('document-templates.show', $template)
            ->with('success', 'Modèle créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentTemplate $documentTemplate)
    {
        return view('document-templates.show', compact('documentTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentTemplate $documentTemplate)
    {
        $template = $documentTemplate;
        $types = $this->getTypes();
        $bienTypes = $this->getBienTypes();
        $availableTags = $this->getAvailableTags();

        return view('document-templates.edit', compact('template', 'types', 'bienTypes', 'availableTags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DocumentTemplateRequest $request, DocumentTemplate $documentTemplate)
    {
        $data = $request->validated();

        // Gestion de l'upload du logo
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo si existe
            if ($documentTemplate->logo_path) {
                Storage::disk('public')->delete($documentTemplate->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('templates/logos', 'public');
        }

        // Gestion de l'upload de la signature
        if ($request->hasFile('signature')) {
            // Supprimer l'ancienne signature si existe
            if ($documentTemplate->signature_path) {
                Storage::disk('public')->delete($documentTemplate->signature_path);
            }
            $data['signature_path'] = $request->file('signature')->store('templates/signatures', 'public');
        }

        // Si marqué comme défaut, désactiver les autres défauts du même type
        if (($data['is_default'] ?? false) && $data['type'] === $documentTemplate->type) {
            DocumentTemplate::where('type', $data['type'])
                ->where('id', '!=', $documentTemplate->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $documentTemplate->update($data);

        return redirect()
            ->route('document-templates.show', $documentTemplate)
            ->with('success', 'Modèle mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentTemplate $documentTemplate)
    {
        // Supprimer les fichiers associés
        if ($documentTemplate->logo_path) {
            Storage::disk('public')->delete($documentTemplate->logo_path);
        }
        if ($documentTemplate->signature_path) {
            Storage::disk('public')->delete($documentTemplate->signature_path);
        }

        $documentTemplate->delete();

        return redirect()
            ->route('document-templates.index')
            ->with('success', 'Modèle supprimé avec succès.');
    }

    /**
     * Duplicate a template
     */
    public function duplicate(DocumentTemplate $documentTemplate)
    {
        $newTemplate = $documentTemplate->replicate();
        $newTemplate->nom = $documentTemplate->nom . ' (Copie)';
        $newTemplate->is_default = false;
        $newTemplate->save();

        return redirect()
            ->route('document-templates.edit', $newTemplate)
            ->with('success', 'Modèle dupliqué avec succès. Vous pouvez maintenant le modifier.');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(DocumentTemplate $documentTemplate)
    {
        $documentTemplate->update(['actif' => !$documentTemplate->actif]);

        $status = $documentTemplate->actif ? 'activé' : 'désactivé';

        return back()->with('success', "Modèle {$status} avec succès.");
    }

    /**
     * Preview template with sample data
     */
    public function preview(DocumentTemplate $documentTemplate)
    {
        // Générer des données d'exemple pour la prévisualisation
        $sampleData = $this->getSampleData();
        
        // Remplacer les balises dans le contenu
        $content = $this->replaceTags($documentTemplate->contenu, $sampleData);

        return view('document-templates.preview', compact('documentTemplate', 'content'));
    }

    /**
     * Get available document types
     */
    private function getTypes(): array
    {
        return [
            'bail_vide' => 'Contrat de bail - vide',
            'bail_meuble' => 'Contrat de bail - meublé',
            'bail_commercial' => 'Bail commercial',
            'bail_parking' => 'Bail parking/garage',
            'etat_lieux_entree' => 'État des lieux d\'entrée',
            'etat_lieux_sortie' => 'État des lieux de sortie',
            'quittance_loyer' => 'Quittance de loyer',
            'avis_echeance' => 'Avis d\'échéance',
            'mandat_gestion' => 'Mandat de gestion',
            'inventaire' => 'Inventaire',
            'attestation_loyer' => 'Attestation de loyer',
            'autre' => 'Autre',
        ];
    }

    /**
     * Get property types
     */
    private function getBienTypes(): array
    {
        return [
            'appartement' => 'Appartement',
            'maison' => 'Maison',
            'studio' => 'Studio',
            'parking' => 'Parking',
            'garage' => 'Garage',
            'local_commercial' => 'Local commercial',
            'bureau' => 'Bureau',
            'terrain' => 'Terrain',
        ];
    }

    /**
     * Get available tags for templates
     */
    private function getAvailableTags(): array
    {
        return [
            'Bien' => [
                '{{Bien_Reference}}' => 'Référence du bien',
                '{{Bien_Adresse}}' => 'Adresse complète',
                '{{Bien_CodePostal}}' => 'Code postal',
                '{{Bien_Ville}}' => 'Ville',
                '{{Bien_Pays}}' => 'Pays',
                '{{Bien_Type}}' => 'Type de bien',
                '{{Bien_Surface}}' => 'Surface en m²',
                '{{Bien_NombrePieces}}' => 'Nombre de pièces',
                '{{Bien_Etage}}' => 'Étage',
                '{{Bien_DPE}}' => 'Classe DPE',
            ],
            'Propriétaire' => [
                '{{Proprietaire_Nom}}' => 'Nom',
                '{{Proprietaire_Prenom}}' => 'Prénom',
                '{{Proprietaire_NomComplet}}' => 'Nom complet',
                '{{Proprietaire_Adresse}}' => 'Adresse',
                '{{Proprietaire_CodePostal}}' => 'Code postal',
                '{{Proprietaire_Ville}}' => 'Ville',
                '{{Proprietaire_Email}}' => 'Email',
                '{{Proprietaire_Telephone}}' => 'Téléphone',
                '{{Proprietaire_IBAN}}' => 'IBAN',
            ],
            'Locataire' => [
                '{{Locataire_Nom}}' => 'Nom',
                '{{Locataire_Prenom}}' => 'Prénom',
                '{{Locataire_NomComplet}}' => 'Nom complet',
                '{{Locataire_DateNaissance}}' => 'Date de naissance',
                '{{Locataire_LieuNaissance}}' => 'Lieu de naissance',
                '{{Locataire_Adresse}}' => 'Adresse',
                '{{Locataire_CodePostal}}' => 'Code postal',
                '{{Locataire_Ville}}' => 'Ville',
                '{{Locataire_Email}}' => 'Email',
                '{{Locataire_Telephone}}' => 'Téléphone',
                '{{Locataire_Profession}}' => 'Profession',
            ],
            'Contrat' => [
                '{{Contrat_Reference}}' => 'Référence',
                '{{Contrat_TypeBail}}' => 'Type de bail',
                '{{Contrat_DateDebut}}' => 'Date de début',
                '{{Contrat_DateFin}}' => 'Date de fin',
                '{{Contrat_DureeMois}}' => 'Durée en mois',
                '{{Contrat_LoyerHC}}' => 'Loyer HC',
                '{{Contrat_Charges}}' => 'Charges',
                '{{Contrat_LoyerCC}}' => 'Loyer CC',
                '{{Contrat_DepotGarantie}}' => 'Dépôt de garantie',
                '{{Contrat_JourPaiement}}' => 'Jour de paiement',
                '{{Contrat_DateSignature}}' => 'Date de signature',
            ],
            'Dates' => [
                '{{Date_Aujourdhui}}' => 'Date du jour',
                '{{Date_Generation}}' => 'Date de génération',
            ],
            'Boucles' => [
                '{{LocataireBlockStart}}' => 'Début bloc locataire',
                '{{LocataireBlockEnd}}' => 'Fin bloc locataire',
                '{{GarantBlockStart}}' => 'Début bloc garant',
                '{{GarantBlockEnd}}' => 'Fin bloc garant',
            ],
        ];
    }

    /**
     * Get sample data for preview
     */
    private function getSampleData(): array
    {
        return [
            'Bien_Reference' => 'BIEN-001',
            'Bien_Adresse' => '10 Rue Victor Hugo',
            'Bien_CodePostal' => '75016',
            'Bien_Ville' => 'Paris',
            'Bien_Pays' => 'France',
            'Bien_Type' => 'Appartement',
            'Bien_Surface' => '65.50',
            'Bien_NombrePieces' => '3',
            'Bien_Etage' => '2',
            'Bien_DPE' => 'C',
            'Proprietaire_Nom' => 'Dupont',
            'Proprietaire_Prenom' => 'Jean',
            'Proprietaire_NomComplet' => 'Jean Dupont',
            'Proprietaire_Adresse' => '123 Avenue des Champs',
            'Proprietaire_CodePostal' => '75008',
            'Proprietaire_Ville' => 'Paris',
            'Proprietaire_Email' => 'jean.dupont@example.com',
            'Proprietaire_Telephone' => '0612345678',
            'Proprietaire_IBAN' => 'FR76 3000 1007 9412 3456 7890 185',
            'Locataire_Nom' => 'Bernard',
            'Locataire_Prenom' => 'Marie',
            'Locataire_NomComplet' => 'Marie Bernard',
            'Locataire_DateNaissance' => '15/05/1990',
            'Locataire_LieuNaissance' => 'Paris',
            'Locataire_Adresse' => '25 Rue de la Paix',
            'Locataire_CodePostal' => '75002',
            'Locataire_Ville' => 'Paris',
            'Locataire_Email' => 'marie.bernard@example.com',
            'Locataire_Telephone' => '0634567890',
            'Locataire_Profession' => 'Ingénieure',
            'Contrat_Reference' => 'BAIL-2024-001',
            'Contrat_TypeBail' => 'Location vide',
            'Contrat_DateDebut' => '01/01/2024',
            'Contrat_DateFin' => '31/12/2024',
            'Contrat_DureeMois' => '12',
            'Contrat_LoyerHC' => '1 200,00 €',
            'Contrat_Charges' => '150,00 €',
            'Contrat_LoyerCC' => '1 350,00 €',
            'Contrat_DepotGarantie' => '1 200,00 €',
            'Contrat_JourPaiement' => '1',
            'Contrat_DateSignature' => '15/12/2023',
            'Date_Aujourdhui' => date('d/m/Y'),
            'Date_Generation' => date('d/m/Y H:i'),
        ];
    }

    /**
     * Replace tags in content with data
     */
    private function replaceTags(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        // Gérer les blocs de boucle (simulation simple pour la prévisualisation)
        $content = preg_replace('/\{\{LocataireBlockStart\}\}(.*?)\{\{LocataireBlockEnd\}\}/s', '$1', $content);
        $content = preg_replace('/\{\{GarantBlockStart\}\}(.*?)\{\{GarantBlockEnd\}\}/s', '$1', $content);

        return $content;
    }
}