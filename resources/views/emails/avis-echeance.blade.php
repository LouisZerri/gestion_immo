<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis d'échéance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #059669;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #059669;
            border-radius: 4px;
        }
        .montant {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
            text-align: center;
            margin: 20px 0;
        }
        .date-echeance {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            background-color: #1f2937;
            color: #9ca3af;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📅 Avis d'Échéance</h1>
        <p>{{ $periode }}</p>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $locataire->prenom }} {{ $locataire->nom }}</strong>,</p>

        <p>Nous vous informons que le prochain loyer est à régler avant la date d'échéance.</p>

        <div class="date-echeance">
            <strong>⏰ Date d'échéance :</strong><br>
            <span style="font-size: 20px; color: #f59e0b;">{{ $dateEcheance->format('d/m/Y') }}</span>
        </div>

        <div class="info-box">
            <strong>📍 Bien loué :</strong><br>
            {{ $bien->adresse }}<br>
            {{ $bien->code_postal }} {{ $bien->ville }}
        </div>

        <div class="info-box">
            <strong>💰 Montant à régler :</strong><br>
            Loyer hors charges : {{ number_format($contrat->loyer_hc, 2, ',', ' ') }} €<br>
            Charges : {{ number_format($contrat->charges, 2, ',', ' ') }} €
        </div>

        <div class="montant">
            Total à payer : {{ number_format($montantTotal, 2, ',', ' ') }} €
        </div>

        <div class="info-box">
            <strong>🏦 Modalités de paiement :</strong><br>
            @if($contrat->bien->proprietaire->iban)
            IBAN : {{ $contrat->bien->proprietaire->iban }}<br>
            @endif
            Référence : {{ $contrat->reference }}
        </div>

        <p><strong>⚠️ Important :</strong> Merci de procéder au règlement avant la date d'échéance pour éviter tout retard.</p>

        <p>Si vous avez des questions concernant ce paiement, n'hésitez pas à nous contacter.</p>

        <p>Cordialement,<br>
        <strong>L'équipe GEST'IMMO</strong></p>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement par GEST'IMMO</p>
        <p>© {{ date('Y') }} GEST'IMMO - Tous droits réservés</p>
    </div>
</body>
</html>