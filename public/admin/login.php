<?php
require_once __DIR__ . '/../../autoloader.php';
require_once __DIR__ . '/../includes/admin_guard.php';
use model\AdminRepository;

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_validar_csrf()) {
        $erro = 'Sessão expirada. Atualize a página e tente novamente.';
    } else {
        $admin = AdminRepository::autenticar((string)($_POST['login'] ?? ''), (string)($_POST['senha'] ?? ''));
        if ($admin === false) {
            $erro = 'Usuário ou senha incorretos.';
        } else {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$admin['Id'];
            $_SESSION['admin_tipo'] = 7;
            $_SESSION['admin_nome'] = $admin['Nome'];
            $_SESSION['admin_login'] = $admin['Login'];
            $_SESSION['admin_trocar_senha'] = (int)$admin['TrocarSenha'] === 1;
            $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
            header('Location: ' . ($_SESSION['admin_trocar_senha'] ? 'alterar-senha.php' : 'dashboard.php'));
            exit;
        }
    }
}
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Entrar | REBOBINE</title><link rel="stylesheet" href="../assets/css/tokens.css"><link rel="stylesheet" href="../assets/css/global.css"><link rel="stylesheet" href="../assets/css/pages/admin.css"></head><body class="admin-login-body">
<main class="login-card"><a href="../acervo.php"><img class="login-logo" src="../assets/images/logo-rebobine-branca.svg" alt="REBOBINE"></a><p class="admin-kicker">ÁREA RESTRITA</p><h1>ACESSO ADMINISTRATIVO</h1><p class="muted">Entre com suas credenciais para gerenciar a locadora.</p>
<?php if ($erro !== ''): ?><div class="admin-alert error"><?= admin_escapar($erro) ?></div><?php endif; ?>
<form method="post" autocomplete="on"><input type="hidden" name="csrf" value="<?= admin_escapar(admin_csrf_token()) ?>"><label>Usuário<input name="login" required maxlength="64" autocomplete="username" autofocus></label><label>Senha<input type="password" name="senha" required autocomplete="current-password"></label><button class="btn btn-primary" type="submit">Entrar no painel</button></form><a class="back-link" href="../acervo.php">← Voltar ao acervo</a></main></body></html>
