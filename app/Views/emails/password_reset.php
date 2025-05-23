<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Redefinição de senha</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: auto; padding: 20px; background: #fafafa; border: 1px solid #eee; }
        h1 { color: #dc3545; }
        a.button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 15px;
            background-color: #dc3545;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        .footer { margin-top: 30px; font-size: 0.9em; color: #666; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h1>Redefinição de senha</h1>
    <p>Olá, <?= esc($user->name ?? 'Usuário') ?>,</p>
    <p>Recebemos uma solicitação para redefinir sua senha. Clique no botão abaixo para criar uma nova senha:</p>
    <p><a href="<?= esc($resetLink) ?>" class="button">Redefinir Senha</a></p>
    <p>Se você não solicitou essa alteração, ignore este email.</p>
    <div class="footer">
        <p>Atenciosamente,<br />Equipe de Atendimento</p>
    </div>
</div>
</body>
</html>