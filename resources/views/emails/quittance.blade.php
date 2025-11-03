<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quittance de loyer</title>
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
            background-color: #2563eb;
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
            border-left: 4px solid #2563eb;
            border-radius: 4px;
        }
        .montant {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            text-align: center;
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
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Quittance de Loyer</h1>
        <p>{{ $periode }}</p>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $locataire->prenom }} {{ $locataire->nom }}</strong>,</p>

        <p>Veuillez trouver ci-joint votre quittance de loyer pour la période de <strong>{{ $periode }}</strong>.</p>

        <div class="info-box">
            <strong>📍 Bien loué :</strong><br>
            {{ $bien->adresse }}<br>
            {{ $bien->code_postal }} {{ $bien->ville }}
        </div>

        <div class="info-box">
            <strong>💰 Détail du paiement :</strong><br>
            Loyer hors charges : {{ number_format($contrat->loyer_hc, 2, ',', ' ') }} €<br>
            Charges : {{ number_format($contrat->charges, 2, ',', ' ') }} €
        </div>

        <div class="montant">
            Total : {{ number_format($montantTotal, 2, ',', ' ') }} €
        </div>

        <p>Ce document atteste du paiement complet de votre loyer pour cette période.</p>

        <p style="text-align: center; margin: 30px 0;">
            <em>La quittance est jointe en pièce jointe au format PDF.</em>
        </p>

        <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>

        <p>Cordialement,<br>
        <strong>L'équipe GEST'IMMO</strong></p>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement par GEST'IMMO</p>
        <p>© {{ date('Y') }} GEST'IMMO - Tous droits réservés</p>
    </div>
</body>
</html>