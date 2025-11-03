<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prévisualisation - {{ $documentTemplate->nom }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f3f4f6;
        }
        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #3b82f6;
            color: white;
            padding: 15px;
            margin: -40px -40px 30px -40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .preview-badge {
            background: #fbbf24;
            color: #78350f;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
        .actions {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 5px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        .btn-primary:hover {
            background: #2563eb;
        }
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }
        @media print {
            .header {
                display: none;
            }
            .actions {
                display: none;
            }
            body {
                background: white;
            }
            .container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="actions">
        <a href="{{ route('document-templates.show', $documentTemplate) }}" class="btn btn-secondary">
            ← Retour
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Imprimer
        </button>
    </div>

    <div class="container">
        <div class="header">
            <div>
                <h2 style="margin: 0;">Prévisualisation du modèle</h2>
                <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">{{ $documentTemplate->nom }}</p>
            </div>
            <div class="preview-badge">
                ⚠️ APERÇU AVEC DONNÉES D'EXEMPLE
            </div>
        </div>

        <!-- Logo si présent -->
        @if($documentTemplate->logo_path)
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ asset('storage/' . $documentTemplate->logo_path) }}" alt="Logo" style="max-height: 100px; max-width: 300px;">
        </div>
        @endif

        <div class="content">
            {!! $content !!}
        </div>

        <!-- Signature si présente -->
        @if($documentTemplate->signature_path)
        <div style="margin-top: 40px; text-align: right;">
            <p style="margin-bottom: 10px;"><strong>Signature :</strong></p>
            <img src="{{ asset('storage/' . $documentTemplate->signature_path) }}" alt="Signature" style="max-height: 80px; max-width: 200px;">
        </div>
        @endif

        @if($documentTemplate->footer_text)
        <div class="footer">
            {{ $documentTemplate->footer_text }}
        </div>
        @endif
    </div>

    <script>
        // Fonction pour imprimer
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>