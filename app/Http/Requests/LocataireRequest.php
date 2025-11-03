<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocataireRequest extends FormRequest
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
        $locataireId = $this->route('locataire') ? $this->route('locataire')->id : null;

        return [
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'lieu_naissance' => ['required', 'string', 'max:100'],
            'adresse_actuelle' => ['required', 'string', 'max:255'],
            'code_postal' => ['required', 'string', 'max:10'],
            'ville' => ['required', 'string', 'max:100'],
            'pays' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('locataires')->ignore($locataireId)],
            'telephone' => ['required', 'string', 'max:20'],
            'telephone_secondaire' => ['nullable', 'string', 'max:20'],
            'profession' => ['nullable', 'string', 'max:100'],
            'employeur' => ['nullable', 'string', 'max:255'],
            'revenus_mensuels' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'notes' => ['nullable', 'string', 'max:5000'],

            // Validation des garants (tableau)
            'garants' => ['nullable', 'array', 'max:5'],
            'garants.*.nom' => ['nullable', 'string', 'max:100'],
            'garants.*.prenom' => ['nullable', 'string', 'max:100'],
            'garants.*.date_naissance' => ['nullable', 'date', 'before:today'],
            'garants.*.adresse' => ['nullable', 'string', 'max:255'],
            'garants.*.code_postal' => ['nullable', 'string', 'max:10'],
            'garants.*.ville' => ['nullable', 'string', 'max:100'],
            'garants.*.pays' => ['nullable', 'string', 'max:100'],
            'garants.*.email' => ['nullable', 'email'],
            'garants.*.telephone' => ['nullable', 'string', 'max:20'],
            'garants.*.profession' => ['nullable', 'string', 'max:100'],
            'garants.*.revenus_mensuels' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'garants.*.lien_avec_locataire' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'prenom' => 'prénom',
            'date_naissance' => 'date de naissance',
            'lieu_naissance' => 'lieu de naissance',
            'adresse_actuelle' => 'adresse actuelle',
            'code_postal' => 'code postal',
            'ville' => 'ville',
            'pays' => 'pays',
            'email' => 'email',
            'telephone' => 'téléphone',
            'telephone_secondaire' => 'téléphone secondaire',
            'profession' => 'profession',
            'employeur' => 'employeur',
            'revenus_mensuels' => 'revenus mensuels',
            'notes' => 'notes',

            // Attributs garants
            'garants.*.nom' => 'nom du garant',
            'garants.*.prenom' => 'prénom du garant',
            'garants.*.date_naissance' => 'date de naissance du garant',
            'garants.*.adresse' => 'adresse du garant',
            'garants.*.code_postal' => 'code postal du garant',
            'garants.*.ville' => 'ville du garant',
            'garants.*.pays' => 'pays du garant',
            'garants.*.email' => 'email du garant',
            'garants.*.telephone' => 'téléphone du garant',
            'garants.*.profession' => 'profession du garant',
            'garants.*.revenus_mensuels' => 'revenus mensuels du garant',
            'garants.*.lien_avec_locataire' => 'lien avec le locataire',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'lieu_naissance.required' => 'Le lieu de naissance est obligatoire.',
            'adresse_actuelle.required' => 'L\'adresse actuelle est obligatoire.',
            'code_postal.required' => 'Le code postal est obligatoire.',
            'ville.required' => 'La ville est obligatoire.',
            'pays.required' => 'Le pays est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'revenus_mensuels.numeric' => 'Les revenus mensuels doivent être un nombre.',
            'revenus_mensuels.min' => 'Les revenus mensuels doivent être supérieurs ou égaux à 0.',

            // Messages garants
            'garants.array' => 'Les garants doivent être un tableau.',
            'garants.max' => 'Vous ne pouvez pas ajouter plus de 5 garants.',
            'garants.*.email.email' => 'L\'email du garant doit être une adresse valide.',
            'garants.*.date_naissance.before' => 'La date de naissance du garant doit être antérieure à aujourd\'hui.',
            'garants.*.revenus_mensuels.numeric' => 'Les revenus mensuels du garant doivent être un nombre.',
        ];
    }
}