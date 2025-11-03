<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProprietaireRequest extends FormRequest
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
        $proprietaireId = $this->route('proprietaire') ? $this->route('proprietaire')->id : null;

        $rules = [
            'type' => ['required', Rule::in(['particulier', 'societe'])],
            'email' => ['required', 'email', Rule::unique('proprietaires')->ignore($proprietaireId)],
            'telephone' => ['required', 'string', 'max:20'],
            'telephone_secondaire' => ['nullable', 'string', 'max:20'],
            'adresse' => ['required', 'string', 'max:255'],
            'code_postal' => ['required', 'string', 'max:10'],
            'ville' => ['required', 'string', 'max:100'],
            'pays' => ['required', 'string', 'max:100'],
            'iban' => ['nullable', 'string', 'max:34'],
            'bic' => ['nullable', 'string', 'max:11'],
            'mandat_actif' => ['nullable', 'boolean'],
            'date_debut_mandat' => ['nullable', 'date'],
            'date_fin_mandat' => ['nullable', 'date', 'after:date_debut_mandat'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];

        // Règles conditionnelles selon le type
        if ($this->type === 'particulier') {
            $rules['nom'] = ['required', 'string', 'max:100'];
            $rules['prenom'] = ['required', 'string', 'max:100'];
            $rules['nom_societe'] = ['nullable'];
            $rules['siret'] = ['nullable'];
        } else {
            $rules['nom_societe'] = ['required', 'string', 'max:255'];
            $rules['siret'] = ['required', 'string', 'size:14', Rule::unique('proprietaires')->ignore($proprietaireId)];
            $rules['nom'] = ['nullable', 'string', 'max:100'];
            $rules['prenom'] = ['nullable'];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'type' => 'type de propriétaire',
            'nom' => 'nom',
            'prenom' => 'prénom',
            'nom_societe' => 'nom de la société',
            'siret' => 'SIRET',
            'adresse' => 'adresse',
            'code_postal' => 'code postal',
            'ville' => 'ville',
            'pays' => 'pays',
            'email' => 'email',
            'telephone' => 'téléphone',
            'telephone_secondaire' => 'téléphone secondaire',
            'iban' => 'IBAN',
            'bic' => 'BIC',
            'mandat_actif' => 'mandat actif',
            'date_debut_mandat' => 'date de début du mandat',
            'date_fin_mandat' => 'date de fin du mandat',
            'notes' => 'notes',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Le type de propriétaire est obligatoire.',
            'type.in' => 'Le type doit être "particulier" ou "société".',
            'nom.required' => 'Le nom est obligatoire pour un particulier.',
            'prenom.required' => 'Le prénom est obligatoire pour un particulier.',
            'nom_societe.required' => 'Le nom de la société est obligatoire.',
            'siret.required' => 'Le SIRET est obligatoire pour une société.',
            'siret.size' => 'Le SIRET doit contenir exactement 14 chiffres.',
            'siret.unique' => 'Ce SIRET est déjà utilisé.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'code_postal.required' => 'Le code postal est obligatoire.',
            'ville.required' => 'La ville est obligatoire.',
            'pays.required' => 'Le pays est obligatoire.',
            'iban.max' => 'L\'IBAN ne peut pas dépasser 34 caractères.',
            'bic.max' => 'Le BIC ne peut pas dépasser 11 caractères.',
            'date_fin_mandat.after' => 'La date de fin du mandat doit être postérieure à la date de début.',
        ];
    }
}