<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Ticket - {{ $ticket->id }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
        }
        .ticket-info {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .ticket-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #495057;
        }
        .ticket-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        .meta-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .meta-value {
            font-size: 14px;
            color: #495057;
        }
        .priority-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .priority-high { background-color: #f8d7da; color: #721c24; }
        .priority-medium { background-color: #fff3cd; color: #856404; }
        .priority-low { background-color: #d1ecf1; color: #0c5460; }
        .action-section {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .action-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 0;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
        .message {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="#" class="logo">Help Desk Master</a>
        </div>

        <h2>Notification Ticket #{{ $ticket->id }}</h2>
        
        <p>Bonjour {{ $user->name }},</p>
        
        <p>Une action a été effectuée sur le ticket <strong>#{{ $ticket->id }}</strong> :</p>
        
        <div class="action-section">
            <div class="action-title">Action : {{ $actionLabel }}</div>
            @if($message)
                <div class="message">{{ $message }}</div>
            @endif
        </div>

        <div class="ticket-info">
            <div class="ticket-title">{{ $ticket->title }}</div>
            
            <div class="ticket-meta">
                <div class="meta-item">
                    <span class="meta-label">ID</span>
                    <span class="meta-value">#{{ $ticket->id }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Statut</span>
                    <span class="meta-value">{{ $ticket->status?->name ?? 'N/A' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Priorité</span>
                    <span class="meta-value">
                        @if($ticket->priority)
                            <span class="priority-badge priority-{{ $ticket->priority->name }}">
                                {{ $ticket->priority->name }}
                            </span>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Assigné à</span>
                    <span class="meta-value">{{ $ticket->assignedTo?->name ?? 'Non assigné' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Entreprise</span>
                    <span class="meta-value">{{ $ticket->company?->name ?? 'N/A' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Projet</span>
                    <span class="meta-value">{{ $ticket->project?->name ?? 'N/A' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Créé par</span>
                    <span class="meta-value">{{ $ticket->user?->name ?? 'N/A' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Date de création</span>
                    <span class="meta-value">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            @if($ticket->description)
                <div style="margin-top: 20px;">
                    <strong>Description :</strong><br>
                    <p>{{ nl2br($ticket->description) }}</p>
                </div>
            @endif
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn">
                Voir le ticket
            </a>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement par le système Help Desk Master.</p>
            <p>Si vous n'êtes pas censé recevoir cet email, veuillez contacter l'administrateur.</p>
        </div>
    </div>
</body>
</html>
