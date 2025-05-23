<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"/>
    <title>Falha no Pagamento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            background-color: #fafafa;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            border: 1px solid #eee;
            padding: 20px;
            border-radius: 5px;
        }

        h1 {
            color: #dc3545;
        }

        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Falha no Pagamento</h1>
    <p>Olá <?= esc($order->user_name ?? 'Cliente') ?>,</p>
    <p>Infelizmente houve uma falha no pagamento do seu pedido <strong>#<?= esc($order->id) ?></strong>.</p>
    <p>Motivo: <?= esc($reason) ?></p>
    <p>Por favor, tente novamente ou entre em contato conosco.</p>
    <div class="footer">
        <p>Atenciosamente,<br/>Equipe de Atendimento</p>
    </div>
</div>
</body>
</html>