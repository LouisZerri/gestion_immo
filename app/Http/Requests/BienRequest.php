<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BienRequest extends FormRequest
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
        $bienId = $this->route('bien') ? $this->route('bien')->id : null;

        return [
            'proprietaire_id' => ['required', 'exists:proprietaires,id'],
            'reference' => ['nullable', 'string', 'max:50', Rule::unique('biens')->ignore($bienId)],
            'type' => ['required', Rule::in(['appartement', 'maison', 'studio', 'parking', 'garage', 'local_commercial', 'bureau', 'terrain'])],
            'adresse' => ['required', 'string', 'max:255'],
            'code_postal' => ['required', 'string', 'max:10'],
            'ville' => ['required', 'string', 'max:100'],
            'pays' => ['required', 'string', 'max:100'],
            'surface' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'nombre_pieces' => ['nullable', 'integer', 'min:0', 'max:100'],
            'etage' => ['nullable', 'integer', 'min:-5', 'max:200'],
            'dpe' => ['nullable', Rule::in(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'NC'])],
            'statut' => ['required', Rule::in(['disponible', 'loue', 'en_travaux', 'vendu'])],
            'description' => ['nullable', 'string', 'max:5000'],
            'rentabilite' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB max par photo
            'delete_photos' => ['nullable', 'array'],
            'delete_photos.*' => ['string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'proprietaire_id' => 'propriétaire',
            'reference' => 'référence',
            'type' => 'type de bien',
            'adresse' => 'adresse',
            'code_postal' => 'code postal',
            'ville' => 'ville',
            'pays' => 'pays',
            'surface' => 'surface',
            'nombre_pieces' => 'nombre de pièces',
            'etage' => 'étage',
            'dpe' => 'DPE',
            'statut' => 'statut',
            'description' => 'description',
            'rentabilite' => 'rentabilité',
            'photos' => 'photos',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'proprietaire_id.required' => 'Le propriétaire est obligatoire.',
            'proprietaire_id.exists' => 'Le propriétaire sélectionné n\'existe pas.',
            'reference.unique' => 'Cette référence est déjà utilisée.',
            'type.required' => 'Le type de bien est obligatoire.',
            'type.in' => 'Le type de bien sélectionné est invalide.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'code_postal.required' => 'Le code postal est obligatoire.',
            'ville.required' => 'La ville est obligatoire.',
            'pays.required' => 'Le pays est obligatoire.',
            'surface.required' => 'La surface est obligatoire.',
            'surface.numeric' => 'La surface doit être un nombre.',
            'surface.min' => 'La surface doit être supérieure ou égale à 0.',
            'surface.max' => 'La surface ne peut pas dépasser 99999.99 m².',
            'nombre_pieces.integer' => 'Le nombre de pièces doit être un entier.',
            'nombre_pieces.min' => 'Le nombre de pièces doit être supérieur ou égal à 0.',
            'etage.integer' => 'L\'étage doit être un entier.',
            'etage.min' => 'L\'étage doit être supérieur ou égal à -5.',
            'etage.max' => 'L\'étage ne peut pas dépasser 200.',
            'dpe.in' => 'Le DPE doit être une lettre entre A et G, ou NC.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné est invalide.',
            'rentabilite.numeric' => 'La rentabilité doit être un nombre.',
            'rentabilite.min' => 'La rentabilité doit être supérieure ou égale à 0.',
            'rentabilite.max' => 'La rentabilité ne peut pas dépasser 100%.',
            'photos.array' => 'Les photos doivent être un tableau.',
            'photos.max' => 'Vous ne pouvez pas uploader plus de 10 photos.',
            'photos.*.image' => 'Chaque fichier doit être une image.',
            'photos.*.mimes' => 'Les images doivent être au format: jpeg, png, jpg, gif ou webp.',
            'photos.*.max' => 'Chaque photo ne peut pas dépasser 5 Mo.',
        ];
    }
}