<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    ]);
    session_start();
}

function admin_escapar($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function admin_csrf_token(): string
{
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf'];
}

function admin_validar_csrf(): bool
{
    return isset($_POST['csrf'], $_SESSION['admin_csrf'])
        && hash_equals($_SESSION['admin_csrf'], (string)$_POST['csrf']);
}

function admin_exigir_login(): void
{
    if (empty($_SESSION['admin_id']) || (int)($_SESSION['admin_tipo'] ?? 0) !== 7) {
        header('Location: login.php');
        exit;
    }
    if (!empty($_SESSION['admin_trocar_senha']) && basename($_SERVER['SCRIPT_NAME']) !== 'alterar-senha.php') {
        header('Location: alterar-senha.php');
        exit;
    }
}

function admin_layout_inicio(string $titulo): void
{
    $nome = admin_escapar($_SESSION['admin_nome'] ?? 'Administrador');
    echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . admin_escapar($titulo) . ' | REBOBINE</title>';
    echo '<link rel="stylesheet" href="../assets/css/tokens.css"><link rel="stylesheet" href="../assets/css/global.css"><link rel="stylesheet" href="../assets/css/pages/admin.css"></head><body class="admin-body">';
    echo '<header class="admin-header"><a href="dashboard.php"><img src="../assets/images/logo-rebobine.svg" alt="REBOBINE"></a><div><span>' . $nome . '</span><a class="btn btn-secondary" href="logout.php">Sair</a></div></header>';
    echo '<main class="admin-shell"><aside class="admin-sidebar"><span class="eyebrow">PAINEL DE CONTROLE</span><a href="dashboard.php">Visão geral</a><a href="admins.php">Administradores</a><a class="disabled" aria-disabled="true">Gerenciar acervo · em breve</a></aside><section class="admin-content">';
    echo '<p class="admin-kicker">REBOBINE / ADMIN</p><h1>' . admin_escapar($titulo) . '</h1>';
}

function admin_layout_fim(): void
{
    echo '</section></main></body></html>';
}
