<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // À adapter selon votre système d'authentification
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'type' => 'required|in:bail_vide,bail_meuble,bail_commercial,bail_parking,etat_lieux_entree,etat_lieux_sortie,quittance_loyer,avis_echeance,mandat_gestion,inventaire,attestation_loyer,autre',
            'contenu' => 'required|string',
            'actif' => 'boolean',
            'is_default' => 'boolean',
            'biens_concernes' => 'nullable|array',
            'biens_concernes.*' => 'string|in:appartement,maison,studio,parking,garage,local_commercial,bureau,terrain,immeuble',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'footer_text' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom du modèle',
            'type' => 'type de document',
            'contenu' => 'contenu du modèle',
            'actif' => 'statut actif',
            'is_default' => 'modèle par défaut',
            'biens_concernes' => 'types de biens concernés',
            'logo' => 'logo',
            'signature' => 'signature',
            'footer_text' => 'texte du pied de page',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du modèle est obligatoire.',
            'nom.max' => 'Le nom du modèle ne peut pas dépasser 255 caractères.',
            'type.required' => 'Le type de document est obligatoire.',
            'type.in' => 'Le type de document sélectionné n\'est pas valide.',
            'contenu.required' => 'Le contenu du modèle est obligatoire.',
            'logo.image' => 'Le fichier doit être une image.',
            'logo.max' => 'Le logo ne peut pas dépasser 2 Mo.',
            'logo.mimes' => 'Le logo doit être au format : jpeg, png, jpg, gif ou svg.',
            'signature.image' => 'Le fichier doit être une image.',
            'signature.max' => 'La signature ne peut pas dépasser 2 Mo.',
            'signature.mimes' => 'La signature doit être au format : jpeg, png, jpg, gif ou svg.',
            'footer_text.max' => 'Le texte du pied de page ne peut pas dépasser 1000 caractères.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convertir les checkboxes en booléens
        $this->merge([
            'actif' => $this->has('actif'),
            'is_default' => $this->has('is_default'),
        ]);
    }
}
