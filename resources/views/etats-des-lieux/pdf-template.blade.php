<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $edl->type_libelle }} - {{ $edl->bien->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 9pt;
            color: #666;
        }

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #333;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px;
            width: 30%;
            border-bottom: 1px solid #ddd;
        }

        .info-value {
            display: table-cell;
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th {
            background-color: #f5f5f5;
            padding: 6px;
            text-align: left;
            font-size: 9pt;
            border: 1px solid #ddd;
            font-weight: bold;
        }

        table td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 9pt;
        }

        table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .piece-header {
            background-color: #e0e0e0;
            padding: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 11pt;
            page-break-before: avoid;
        }

        .commentaires {
            background-color: #fffacd;
            padding: 8px;
            margin-bottom: 10px;
            border-left: 3px solid #ffd700;
            font-style: italic;
        }

        .photos-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .photo-item img {
            width: 100%;
            height: auto;
            max-height: 150px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #999;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }

        .page-break {
            page-break-after: always;
        }

        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-box {
            display: inline-block;
            width: 45%;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 2%;
            min-height: 100px;
            vertical-align: top;
        }

        .signature-box h3 {
            font-size: 10pt;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .conditions {
            font-size: 8pt;
            color: #666;
            line-height: 1.6;
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        .conditions h3 {
            font-size: 9pt;
            margin-bottom: 5px;
        }

        @page {
            margin: 20mm;
        }
    </style>
</head>
<body>

    <!-- En-tête -->
    <div class="header">
        <h1>{{ $edl->type_libelle }}</h1>
        <p>État des lieux contradictoire à annexer au contrat de location, dont il ne peut être dissocié.</p>
    </div>

    <!-- Adresse du bien -->
    <div class="section">
        <div class="section-title">ADRESSE DU BIEN</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Référence</div>
                <div class="info-value">{{ $edl->bien->reference }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Adresse</div>
                <div class="info-value">{{ $edl->bien->adresse }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Code postal</div>
                <div class="info-value">{{ $edl->bien->code_postal }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Ville</div>
                <div class="info-value">{{ $edl->bien->ville }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Type</div>
                <div class="info-value">{{ $edl->bien->type_libelle }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Surface</div>
                <div class="info-value">{{ $edl->bien->surface }} m²</div>
            </div>
        </div>
    </div>

    <!-- Le(s) Bailleur(s) -->
    <div class="section">
        <div class="section-title">LE(S) BAILLEUR(S)</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom</div>
                <div class="info-value">{{ $edl->bien->proprietaire->nom_complet }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Adresse</div>
                <div class="info-value">
                    {{ $edl->bien->proprietaire->adresse ?? 'Non renseignée' }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone</div>
                <div class="info-value">{{ $edl->bien->proprietaire->telephone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $edl->bien->proprietaire->email }}</div>
            </div>
        </div>
    </div>

    <!-- Le(s) Locataire(s) -->
    @if($edl->contrat && $edl->contrat->locataires->count() > 0)
    <div class="section">
        <div class="section-title">LE(S) LOCATAIRE(S)</div>
        @foreach($edl->contrat->locataires as $locataire)
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom</div>
                <div class="info-value">{{ $locataire->nom_complet }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone</div>
                <div class="info-value">{{ $locataire->telephone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $locataire->email }}</div>
            </div>
        </div>
        @if(!$loop->last)
            <hr style="margin: 10px 0; border: none; border-top: 1px dashed #ccc;">
        @endif
        @endforeach
    </div>
    @endif

    <!-- Compteurs -->
    @if($edl->compteurs_eau || $edl->compteurs_gaz || $edl->compteurs_electricite)
    <div class="section page-break">
        <div class="section-title">RELEVÉS DES COMPTEURS</div>
        
        <table>
            <thead>
                <tr>
                    <th>TYPE</th>
                    <th>N° SÉRIE</th>
                    <th>RELEVÉ</th>
                    <th>FONCTIONNEMENT</th>
                    <th>COMMENTAIRE</th>
                </tr>
            </thead>
            <tbody>
                @if($edl->compteurs_eau)
                <tr>
                    <td>💧 Eau</td>
                    <td>{{ $edl->compteurs_eau['numero_serie'] ?? '-' }}</td>
                    <td>{{ $edl->compteurs_eau['m3'] ?? '-' }} m³</td>
                    <td>{{ $edl->compteurs_eau['fonctionnement'] ?? '-' }}</td>
                    <td>{{ $edl->compteurs_eau['commentaire'] ?? '-' }}</td>
                </tr>
                @endif

                @if($edl->compteurs_gaz)
                <tr>
                    <td>🔥 Gaz</td>
                    <td>{{ $edl->compteurs_gaz['numero_serie'] ?? '-' }}</td>
                    <td>{{ $edl->compteurs_gaz['m3'] ?? '-' }} m³</td>
                    <td>{{ $edl->compteurs_gaz['fonctionnement'] ?? '-' }}</td>
                    <td>{{ $edl->compteurs_gaz['commentaire'] ?? '-' }}</td>
                </tr>
                @endif

                @if($edl->compteurs_electricite)
                <tr>
                    <td>⚡ Électricité</td>
                    <td>{{ $edl->compteurs_electricite['numero_serie'] ?? '-' }}</td>
                    <td>{{ $edl->compteurs_electricite['kwh'] ?? '-' }} kWh</td>
                    <td>{{ $edl->compteurs_electricite['fonctionnement'] ?? '-' }}</td>
                    <td>{{ $edl->compteurs_electricite['commentaire'] ?? '-' }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif

    <!-- Remise des clés -->
    @if($edl->cles)
    <div class="section">
        <div class="section-title">REMISE DES CLÉS</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Type</div>
                <div class="info-value">{{ $edl->cles['type'] ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nombre</div>
                <div class="info-value">{{ $edl->cles['nombre'] ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date</div>
                <div class="info-value">{{ $edl->cles['date'] ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Commentaire</div>
                <div class="info-value">{{ $edl->cles['commentaire'] ?? '-' }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- PIÈCES -->
    @foreach($edl->pieces as $piece)
    <div class="section page-break">
        <div class="piece-header">🚪 {{ strtoupper($piece->nom_piece) }}</div>

        @if($piece->commentaires_piece)
        <div class="commentaires">
            <strong>Commentaires sur la pièce :</strong><br>
            {{ $piece->commentaires_piece }}
        </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">ÉLÉMENT</th>
                    <th style="width: 20%;">NATURE</th>
                    <th style="width: 15%;">ÉTAT D'USURE</th>
                    <th style="width: 15%;">FONCTIONNEMENT</th>
                    <th style="width: 30%;">COMMENTAIRES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($piece->elements as $element)
                <tr>
                    <td><strong>{{ $element->element }}</strong></td>
                    <td>{{ $element->nature ?: '-' }}</td>
                    <td>{{ $element->etat_usure ?: '-' }}</td>
                    <td>{{ $element->fonctionnement ?: '-' }}</td>
                    <td>{{ $element->commentaires ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Photos de la pièce -->
        @if($piece->photos && count($piece->photos) > 0)
        <div style="margin-top: 15px;">
            <strong>📷 Photos ({{ count($piece->photos) }}) :</strong>
            <div class="photos-grid">
                @foreach($piece->photos as $photo)
                <div class="photo-item">
                    <img src="{{ public_path('storage/' . $photo) }}" alt="Photo {{ $loop->index + 1 }}">
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endforeach

    <!-- Observations générales -->
    @if($edl->observations_generales)
    <div class="section page-break">
        <div class="section-title">OBSERVATIONS GÉNÉRALES</div>
        <div class="commentaires">
            {{ $edl->observations_generales }}
        </div>
    </div>
    @endif

    <!-- Conditions générales -->
    <div class="section page-break">
        <div class="section-title">CONDITIONS GÉNÉRALES</div>
        <div class="conditions">
            <p>Conformément à l'article 3 de la Loi n° 89 – 462 du 6 Juillet 1989, un état des lieux doit être établi contradictoirement entre les parties lors de la remise des clefs au locataire, et lors de la restitution de celles-ci.</p>
            <p style="margin-top: 5px;">Le locataire dispose d'un délai de 10 jours pour demander au bailleur ou à son représentant de compléter le présent état des lieux (pour l'état des équipements de chauffe, le délai est porté au 1er mois de la période de chauffe).</p>
            <p style="margin-top: 5px;">Les cosignataires reconnaissent avoir reçu chacun un exemplaire du présent état des lieux et s'accordent pour y faire référence lors du départ du locataire.</p>
            <p style="margin-top: 5px;">Le présent état des lieux établi contradictoirement entre les parties qui le reconnaissent exact, fait partie intégrante du contrat de location dont il ne peut être dissocié.</p>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signature-section page-break">
        <div class="section-title">SIGNATURES</div>
        <p style="margin-bottom: 10px;">Date d'établissement : <strong>{{ $edl->date_etat->format('d/m/Y') }}</strong></p>
        
        <div class="signature-box">
            <h3>Le(s) BAILLEUR(S)</h3>
            <p style="font-size: 8pt; margin-bottom: 40px;">Le présent état des lieux est transmis et accepté par le propriétaire ou son mandataire.</p>
            <p><strong>{{ $edl->bien->proprietaire->nom_complet }}</strong></p>
        </div>

        @if($edl->contrat && $edl->contrat->locataires->count() > 0)
        <div class="signature-box">
            <h3>Le(s) LOCATAIRE(S)</h3>
            <p style="font-size: 8pt; margin-bottom: 40px;">Le présent état des lieux est transmis et accepté par le locataire ou son représentant.</p>
            @foreach($edl->contrat->locataires as $locataire)
            <p><strong>{{ $locataire->nom_complet }}</strong></p>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} par GEST'IMMO - Page <span class="pagenum"></span>
    </div>

</body>
</html>