<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prévisualisation - {{ $template->nom }}</title>
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
            background: #10b981;
            color: white;
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
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        .btn-primary:hover {
            background: #2563eb;
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-success:hover {
            background: #059669;
        }
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }
        .info-box {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin-bottom: 20px;
        }
        .info-box h3 {
            margin: 0 0 10px 0;
            color: #1e40af;
            font-size: 16px;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 13px;
            color: #1e3a8a;
        }
        @media print {
            .header, .actions, .info-box {
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
        <button onclick="window.close()" class="btn btn-secondary">
            ← Fermer
        </button>
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Imprimer
        </button>
        <button onclick="generateDocument()" class="btn btn-success">
            ✅ Générer
        </button>
    </div>

    <div class="container">
        <div class="header">
            <div>
                <h2 style="margin: 0;">Prévisualisation avec données réelles</h2>
                <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">{{ $template->nom }}</p>
            </div>
            <div class="preview-badge">
                ✅ DONNÉES RÉELLES
            </div>
        </div>

        <div class="info-box">
            <h3>📋 Informations du contrat</h3>
            <p><strong>Contrat :</strong> {{ $contrat->reference }}</p>
            <p><strong>Bien :</strong> {{ $contrat->bien->adresse }}, {{ $contrat->bien->ville }}</p>
            <p><strong>Locataire(s) :</strong> 
                @foreach($contrat->locataires as $locataire)
                    {{ $locataire->nom_complet }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </p>
        </div>

        <!-- Logo si présent -->
        @if($template->logo_path)
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ asset('storage/' . $template->logo_path) }}" alt="Logo" style="max-height: 100px; max-width: 300px;">
        </div>
        @endif

        <div class="content">
            {!! $content !!}
        </div>

        <!-- Signature si présente -->
        @if($template->signature_path)
        <div style="margin-top: 40px; text-align: right;">
            <p style="margin-bottom: 10px;"><strong>Signature :</strong></p>
            <img src="{{ asset('storage/' . $template->signature_path) }}" alt="Signature" style="max-height: 80px; max-width: 200px;">
        </div>
        @endif

        @if($template->footer_text)
        <div class="footer">
            {{ $template->footer_text }}
        </div>
        @endif
    </div>

    <script>
        function generateDocument() {
            if (confirm('Générer ce document ?\n\nVous pourrez le télécharger une fois généré.')) {
                // Créer un formulaire pour soumettre
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("documents.store") }}';
                
                // Token CSRF
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                
                // Template ID
                const templateInput = document.createElement('input');
                templateInput.type = 'hidden';
                templateInput.name = 'template_id';
                templateInput.value = '{{ $template->id }}';
                form.appendChild(templateInput);
                
                // Contrat ID
                const contratInput = document.createElement('input');
                contratInput.type = 'hidden';
                contratInput.name = 'contrat_id';
                contratInput.value = '{{ $contrat->id }}';
                form.appendChild(contratInput);
                
                // Format (demander à l'utilisateur)
                const format = prompt('Format de sortie :\n\nEntrez "pdf" pour PDF\nEntrez "docx" pour Word', 'pdf');
                if (format && (format === 'pdf' || format === 'docx')) {
                    const formatInput = document.createElement('input');
                    formatInput.type = 'hidden';
                    formatInput.name = 'format';
                    formatInput.value = format;
                    form.appendChild(formatInput);
                    
                    // Soumettre
                    document.body.appendChild(form);
                    form.submit();
                } else if (format !== null) {
                    alert('Format invalide. Utilisez "pdf" ou "docx".');
                }
            }
        }

        // Raccourci clavier pour imprimer
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>