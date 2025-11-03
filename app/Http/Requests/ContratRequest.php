<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContratRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $contratId = $this->route('contrat') ? $this->route('contrat')->id : null;

        return [
            // Bien et propriétaire
            'bien_id' => 'required|exists:biens,id',
            'proprietaire_id' => 'required|exists:proprietaires,id',

            // Type et dates
            'type_bail' => 'required|in:vide,meuble,commercial,professionnel,parking',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'duree_mois' => 'required|integer|min:1|max:120',
            'date_signature' => 'nullable|date|before_or_equal:date_debut',

            // Loyer
            'loyer_hc' => 'required|numeric|min:0',
            'charges' => 'nullable|numeric|min:0',
            'depot_garantie' => 'required|numeric|min:0',

            // Paiement
            'periodicite_paiement' => 'required|in:mensuel,trimestriel,annuel',
            'jour_paiement' => 'required|integer|min:1|max:31',

            // Révision
            'indice_reference' => 'nullable|numeric|min:0',
            'date_revision' => 'nullable|date',
            'tacite_reconduction' => 'nullable|boolean',

            // Locataires
            'locataires' => 'required|array|min:1|max:10',
            'locataires.*' => 'required|exists:locataires,id',
            'parts_loyer' => 'nullable|array',
            'parts_loyer.*' => 'nullable|numeric|min:0|max:100',

            // Conditions particulières
            'conditions_particulieres' => 'nullable|string|max:5000',

            // Options
            'generer_documents' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Bien et propriétaire
            'bien_id.required' => 'Veuillez sélectionner un bien.',
            'bien_id.exists' => 'Le bien sélectionné n\'existe pas.',
            'proprietaire_id.required' => 'Le propriétaire est obligatoire.',
            'proprietaire_id.exists' => 'Le propriétaire sélectionné n\'existe pas.',

            // Type et dates
            'type_bail.required' => 'Le type de bail est obligatoire.',
            'type_bail.in' => 'Type de bail invalide.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.date' => 'Format de date invalide.',
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.date' => 'Format de date invalide.',
            'date_fin.after' => 'La date de fin doit être après la date de début.',
            'duree_mois.required' => 'La durée en mois est obligatoire.',
            'duree_mois.integer' => 'La durée doit être un nombre entier.',
            'duree_mois.min' => 'La durée minimum est de 1 mois.',
            'duree_mois.max' => 'La durée maximum est de 120 mois (10 ans).',
            'date_signature.date' => 'Format de date invalide.',
            'date_signature.before_or_equal' => 'La date de signature ne peut pas être après le début du bail.',

            // Loyer
            'loyer_hc.required' => 'Le loyer hors charges est obligatoire.',
            'loyer_hc.numeric' => 'Le loyer doit être un nombre.',
            'loyer_hc.min' => 'Le loyer ne peut pas être négatif.',
            'charges.numeric' => 'Les charges doivent être un nombre.',
            'charges.min' => 'Les charges ne peuvent pas être négatives.',
            'depot_garantie.required' => 'Le dépôt de garantie est obligatoire.',
            'depot_garantie.numeric' => 'Le dépôt de garantie doit être un nombre.',
            'depot_garantie.min' => 'Le dépôt de garantie ne peut pas être négatif.',

            // Paiement
            'periodicite_paiement.required' => 'La périodicité de paiement est obligatoire.',
            'periodicite_paiement.in' => 'Périodicité invalide.',
            'jour_paiement.required' => 'Le jour de paiement est obligatoire.',
            'jour_paiement.integer' => 'Le jour de paiement doit être un nombre.',
            'jour_paiement.min' => 'Le jour minimum est 1.',
            'jour_paiement.max' => 'Le jour maximum est 31.',

            // Révision
            'indice_reference.numeric' => 'L\'indice de référence doit être un nombre.',
            'indice_reference.min' => 'L\'indice de référence ne peut pas être négatif.',
            'date_revision.date' => 'Format de date invalide.',

            // Locataires
            'locataires.required' => 'Au moins un locataire est obligatoire.',
            'locataires.array' => 'Format de données invalide.',
            'locataires.min' => 'Au moins un locataire est requis.',
            'locataires.max' => 'Maximum 10 locataires par contrat.',
            'locataires.*.required' => 'Veuillez sélectionner un locataire valide.',
            'locataires.*.exists' => 'Un des locataires sélectionnés n\'existe pas.',
            'parts_loyer.array' => 'Format de données invalide pour les parts de loyer.',
            'parts_loyer.*.numeric' => 'La part de loyer doit être un nombre.',
            'parts_loyer.*.min' => 'La part de loyer ne peut pas être négative.',
            'parts_loyer.*.max' => 'La part de loyer ne peut pas dépasser 100%.',

            // Conditions particulières
            'conditions_particulieres.string' => 'Les conditions particulières doivent être du texte.',
            'conditions_particulieres.max' => 'Les conditions particulières ne peuvent pas dépasser 5000 caractères.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'bien_id' => 'bien',
            'proprietaire_id' => 'propriétaire',
            'type_bail' => 'type de bail',
            'date_debut' => 'date de début',
            'date_fin' => 'date de fin',
            'duree_mois' => 'durée en mois',
            'date_signature' => 'date de signature',
            'loyer_hc' => 'loyer hors charges',
            'charges' => 'charges',
            'depot_garantie' => 'dépôt de garantie',
            'periodicite_paiement' => 'périodicité de paiement',
            'jour_paiement' => 'jour de paiement',
            'indice_reference' => 'indice de référence',
            'date_revision' => 'date de révision',
            'tacite_reconduction' => 'reconduction tacite',
            'locataires' => 'locataires',
            'parts_loyer' => 'parts de loyer',
            'conditions_particulieres' => 'conditions particulières',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Calculer automatiquement la durée si les dates sont fournies
        if ($this->has('date_debut') && $this->has('date_fin') && !$this->has('duree_mois')) {
            $debut = \Carbon\Carbon::parse($this->date_debut);
            $fin = \Carbon\Carbon::parse($this->date_fin);
            $this->merge([
                'duree_mois' => $debut->diffInMonths($fin)
            ]);
        }

        // Normaliser les parts de loyer (si non fournies, répartir équitablement)
        if ($this->has('locataires') && !$this->has('parts_loyer')) {
            $nbLocataires = count($this->locataires);
            $partEquitable = round(100 / $nbLocataires, 2);
            
            $this->merge([
                'parts_loyer' => array_fill(0, $nbLocataires, $partEquitable)
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Vérifier que le bien appartient bien au propriétaire
            if ($this->filled('bien_id') && $this->filled('proprietaire_id')) {
                $bien = \App\Models\Bien::find($this->bien_id);
                if ($bien && $bien->proprietaire_id != $this->proprietaire_id) {
                    $validator->errors()->add(
                        'proprietaire_id',
                        'Le bien sélectionné n\'appartient pas à ce propriétaire.'
                    );
                }
            }

            // Vérifier que les parts de loyer totalisent environ 100%
            if ($this->has('parts_loyer')) {
                $totalParts = array_sum($this->parts_loyer);
                if (abs($totalParts - 100) > 1) { // Tolérance de 1%
                    $validator->errors()->add(
                        'parts_loyer',
                        'La somme des parts de loyer doit être égale à 100% (actuellement : ' . $totalParts . '%).'
                    );
                }
            }

            // Vérifier que le bien n'a pas déjà un contrat actif (sauf en édition)
            if ($this->filled('bien_id')) {
                $contratId = $this->route('contrat') ? $this->route('contrat')->id : null;
                
                $contratActif = \App\Models\Contrat::where('bien_id', $this->bien_id)
                    ->where('statut', 'actif')
                    ->when($contratId, function ($query) use ($contratId) {
                        return $query->where('id', '!=', $contratId);
                    })
                    ->exists();

                if ($contratActif) {
                    $validator->errors()->add(
                        'bien_id',
                        'Ce bien a déjà un contrat actif. Résiliez d\'abord le contrat existant.'
                    );
                }
            }

            // Vérifier la durée minimum selon le type de bail
            if ($this->filled('type_bail') && $this->filled('duree_mois')) {
                $dureesMinimales = [
                    'vide' => 36, // 3 ans
                    'meuble' => 12, // 1 an
                    'commercial' => 36, // 3 ans
                    'professionnel' => 12, // 1 an
                    'parking' => 1, // Aucune durée minimum
                ];

                $dureeMin = $dureesMinimales[$this->type_bail] ?? 1;
                if ($this->duree_mois < $dureeMin) {
                    $validator->errors()->add(
                        'duree_mois',
                        "Pour un bail {$this->type_bail}, la durée minimum est de {$dureeMin} mois."
                    );
                }
            }
        });
    }
}