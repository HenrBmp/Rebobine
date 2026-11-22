<?php
require_once __DIR__ . '/../../autoloader.php';
require_once __DIR__ . '/../includes/admin_guard.php';
use model\AdminRepository;

if (empty($_SESSION['admin_id']) || (int)($_SESSION['admin_tipo'] ?? 0) !== 7) {
    header('Location: login.php'); exit;
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_validar_csrf()) $erro = 'Sessão expirada. Atualize a página.';
    else {
        $senhaAtual = (string)($_POST['senha_atual'] ?? '');
        $nova = (string)($_POST['nova_senha'] ?? '');
        $confirmacao = (string)($_POST['confirmacao'] ?? '');
        $admin = AdminRepository::autenticar((string)$_SESSION['admin_login'], $senhaAtual);
        if (!$admin) $erro = 'A senha atual está incorreta.';
        elseif (strlen($nova) < 10) $erro = 'A nova senha deve possuir pelo menos 10 caracteres.';
        elseif ($nova !== $confirmacao) $erro = 'A confirmação não corresponde à nova senha.';
        elseif (!AdminRepository::alterarSenha((int)$_SESSION['admin_id'], $nova, false)) $erro = 'Não foi possível atualizar a senha.';
        else {
            $_SESSION['admin_trocar_senha'] = false;
            $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
            header('Location: dashboard.php'); exit;
        }
    }
}
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Alterar senha | REBOBINE</title><link rel="stylesheet" href="../assets/css/tokens.css"><link rel="stylesheet" href="../assets/css/global.css"><link rel="stylesheet" href="../assets/css/pages/admin.css"></head><body class="admin-login-body"><main class="login-card"><img class="login-logo" src="../assets/images/logo-rebobine-branca.svg" alt="REBOBINE"><p class="admin-kicker">SEGURANÇA DA CONTA</p><h1>CRIE UMA NOVA SENHA</h1><p class="muted">Por segurança, altere a senha temporária antes de continuar.</p><?php if ($erro): ?><div class="admin-alert error"><?= admin_escapar($erro) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?= admin_escapar(admin_csrf_token()) ?>"><label>Senha atual<input type="password" name="senha_atual" required autocomplete="current-password"></label><label>Nova senha<input type="password" name="nova_senha" required minlength="10" autocomplete="new-password"></label><label>Confirmar nova senha<input type="password" name="confirmacao" required minlength="10" autocomplete="new-password"></label><button class="btn btn-primary" type="submit">Atualizar senha</button></form></main></body></html>
