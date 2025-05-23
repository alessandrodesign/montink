<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Bem-vindo ao nosso sistema</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: auto; padding: 20px; background: #fafafa; border: 1px solid #eee; }
        h1 { color: #007bff; }
        .footer { margin-top: 30px; font-size: 0.9em; color: #666; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h1>Bem-vindo, <?= esc($user->name ?? 'Usuário') ?>!</h1>
    <p>Obrigado por se registrar em nosso sistema. Estamos felizes em tê-lo conosco.</p>
    <p>Se precisar de ajuda, entre em contato conosco.</p>
    <div class="footer">
        <p>Atenciosamente,<br />Equipe de Atendimento</p>
    </div>
</div>
</body>
</html>