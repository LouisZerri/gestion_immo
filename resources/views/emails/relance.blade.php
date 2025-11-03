<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relance - Loyer impayé</title>
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
            background-color: #dc2626;
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
        .alert-box {
            background-color: #fef2f2;
            border: 2px solid #dc2626;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #dc2626;
            border-radius: 4px;
        }
        .montant {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            text-align: center;
            margin: 20px 0;
        }
        .retard {
            background-color: #fee2e2;
            padding: 10px;
            border-radius: 6px;
            font-weight: bold;
            color: #991b1b;
            text-align: center;
            margin: 15px 0;
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
        <h1>⚠️ Relance de Paiement</h1>
        <p>Loyer impayé</p>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $locataire->prenom }} {{ $locataire->nom }}</strong>,</p>

        <div class="alert-box">
            <h2 style="margin: 0; color: #dc2626;">📢 Paiement en retard</h2>
        </div>

        <p>Nous constatons que le loyer pour la période de <strong>{{ $periode }}</strong> n'a pas encore été réglé.</p>

        <div class="retard">
            ⏰ Retard : {{ $joursRetard }} jour{{ $joursRetard > 1 ? 's' : '' }}
        </div>

        <div class="info-box">
            <strong>📍 Bien concerné :</strong><br>
            {{ $bien->adresse }}<br>
            {{ $bien->code_postal }} {{ $bien->ville }}
        </div>

        <div class="info-box">
            <strong>📅 Date d'échéance initiale :</strong><br>
            {{ $dateEcheance->format('d/m/Y') }}
        </div>

        <div class="info-box">
            <strong>💰 Montant dû :</strong><br>
            Loyer hors charges : {{ number_format($contrat->loyer_hc, 2, ',', ' ') }} €<br>
            Charges : {{ number_format($contrat->charges, 2, ',', ' ') }} €
        </div>

        <div class="montant">
            Total à régler : {{ number_format($montantTotal, 2, ',', ' ') }} €
        </div>

        <div class="info-box" style="border-left-color: #2563eb;">
            <strong>🏦 Coordonnées bancaires :</strong><br>
            @if($contrat->bien->proprietaire->iban)
            IBAN : {{ $contrat->bien->proprietaire->iban }}<br>
            @endif
            Référence de paiement : {{ $contrat->reference }}
        </div>

        <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <strong>⚠️ Action requise :</strong><br>
            Merci de procéder au règlement dans les plus brefs délais pour éviter d'éventuelles pénalités ou poursuites.
        </div>

        <p>Si vous rencontrez des difficultés de paiement ou si vous avez déjà effectué ce règlement, merci de nous contacter rapidement afin que nous puissions trouver une solution ensemble.</p>

        <p><strong>📞 Contact :</strong><br>
        Téléphone : [Votre numéro]<br>
        Email : [Votre email]</p>

        <p>Nous vous remercions de votre compréhension et de votre prompte régularisation.</p>

        <p>Cordialement,<br>
        <strong>L'équipe GEST'IMMO</strong></p>
    </div>

    <div class="footer">
        <p>Cet email de relance a été envoyé automatiquement par GEST'IMMO</p>
        <p>© {{ date('Y') }} GEST'IMMO - Tous droits réservés</p>
    </div>
</body>
</html>