<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Confirmação de Pagamento</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; background-color: #fafafa; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; border: 1px solid #eee; padding: 20px; border-radius: 5px; }
        h1 { color: #28a745; }
        .footer { margin-top: 30px; font-size: 0.9em; color: #666; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h1>Pagamento Confirmado</h1>
    <p>Olá <?= esc($order->user_name ?? 'Cliente') ?>,</p>
    <p>Recebemos o pagamento do seu pedido <strong>#<?= esc($order->id) ?></strong> com sucesso.</p>
    <p>Obrigado pela preferência!</p>
    <div class="footer">
        <p>Atenciosamente,<br />Equipe de Atendimento</p>
    </div>
</div>
</body>
</html>