<?php
require_once __DIR__ . '/../../autoloader.php';
require_once __DIR__ . '/../includes/admin_guard.php';
use model\AdminRepository;

admin_exigir_login();
$mensagem = '';
$tipoMensagem = 'success';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_validar_csrf()) {
        $mensagem = 'Sua sessão expirou. Atualize a página e tente novamente.';
        $tipoMensagem = 'error';
    } else {
        $acao = (string)($_POST['acao'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        if ($acao === 'criar') {
            $nome = trim((string)($_POST['nome'] ?? ''));
            $login = trim((string)($_POST['login'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $senha = (string)($_POST['senha'] ?? '');
            if ($nome === '' || !preg_match('/^[a-zA-Z0-9._-]{3,64}$/', $login) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 10) {
                $mensagem = 'Confira os campos. A senha deve ter pelo menos 10 caracteres e o usuário deve ter 3–64 caracteres (letras, números, ponto, hífen ou sublinhado).';
                $tipoMensagem = 'error';
            } elseif (!AdminRepository::criar($nome, $login, $email, $senha)) {
                $mensagem = 'Não foi possível criar. Verifique se o usuário ou e-mail já existe.';
                $tipoMensagem = 'error';
            } else $mensagem = 'Administrador criado. Ele precisará trocar a senha no primeiro acesso.';
        } elseif ($acao === 'atualizar') {
            $nome = trim((string)($_POST['nome'] ?? ''));
            $login = trim((string)($_POST['login'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            if ($id <= 0 || $nome === '' || !preg_match('/^[a-zA-Z0-9._-]{3,64}$/', $login) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $mensagem = 'Dados inválidos. Revise nome, usuário e e-mail.';
                $tipoMensagem = 'error';
            } elseif (!AdminRepository::atualizar($id, $nome, $login, $email)) {
                $mensagem = 'Não foi possível atualizar. Verifique se o usuário ou e-mail já está em uso.';
                $tipoMensagem = 'error';
            } else {
                if ($id === (int)$_SESSION['admin_id']) {
                    $_SESSION['admin_nome'] = $nome;
                    $_SESSION['admin_login'] = $login;
                }
                $mensagem = 'Dados do administrador atualizados.';
            }
        } elseif ($acao === 'senha') {
            $senha = (string)($_POST['senha'] ?? '');
            if ($id <= 0 || strlen($senha) < 10) {
                $mensagem = 'A nova senha deve possuir pelo menos 10 caracteres.';
                $tipoMensagem = 'error';
            } elseif (!AdminRepository::alterarSenha($id, $senha, true)) {
                $mensagem = 'Não foi possível redefinir a senha.';
                $tipoMensagem = 'error';
            } else {
                $mensagem = 'Senha redefinida. O administrador deverá alterá-la no próximo acesso.';
                if ($id === (int)$_SESSION['admin_id']) {
                    $_SESSION['admin_trocar_senha'] = true;
                }
            }
        } elseif ($acao === 'inativar') {
            $resultado = AdminRepository::inativar($id, (int)$_SESSION['admin_id']);
            $mensagem = [
                'ok' => 'Conta administrativa inativada.',
                'self' => 'Você não pode inativar a própria conta enquanto está conectado.',
                'last' => 'Não é possível inativar o último administrador ativo.',
                'missing' => 'Administrador não encontrado ou já inativo.',
                'error' => 'Não foi possível inativar a conta.'
            ][$resultado] ?? 'Não foi possível concluir a operação.';
            if ($resultado !== 'ok') $tipoMensagem = 'error';
        }
    }
}
$admins = [];
try { $admins = AdminRepository::listar(); } catch (Throwable $e) { error_log('[REBOBINE][AdminPage] ' . $e->getMessage()); $mensagem = 'Não foi possível carregar a lista de administradores.'; $tipoMensagem = 'error'; }
admin_layout_inicio('Administradores');
?>
<p class="admin-intro">Gerencie quem pode acessar o painel administrativo. Contas inativadas deixam de conseguir entrar, mas seus registros são preservados.</p>
<?php if ($mensagem !== ''): ?><div class="admin-alert <?= admin_escapar($tipoMensagem) ?>"><?= admin_escapar($mensagem) ?></div><?php endif; ?>
<div class="admin-panel"><h2>Adicionar administrador</h2><form method="post" class="admin-form-grid"><input type="hidden" name="csrf" value="<?= admin_escapar(admin_csrf_token()) ?>"><input type="hidden" name="acao" value="criar"><label>Nome<input name="nome" required maxlength="255"></label><label>Usuário<input name="login" required minlength="3" maxlength="64" pattern="[a-zA-Z0-9._-]+"></label><label>E-mail<input type="email" name="email" required maxlength="255"></label><label>Senha inicial<input type="password" name="senha" required minlength="10" autocomplete="new-password"></label><div class="form-actions"><button class="btn btn-primary" type="submit">Criar administrador</button><small>A senha inicial será trocada no primeiro acesso.</small></div></form></div>
<div class="section-heading admin-section-heading"><div><h2>CONTAS ATIVAS</h2><p><?= count($admins) ?> administrador(es) ativo(s)</p></div></div>
<?php if (!$admins): ?><div class="empty-state">Nenhum administrador ativo encontrado.</div><?php else: ?>
<div class="admin-list"><?php foreach ($admins as $admin): $id = (int)$admin['Id']; ?>
<article class="admin-user-card"><div class="admin-user-heading"><div><h3><?= admin_escapar($admin['Nome']) ?></h3><span>@<?= admin_escapar($admin['Login']) ?> · <?= admin_escapar($admin['Email']) ?></span></div><span class="admin-id">#<?= $id ?></span></div>
<form method="post" class="admin-form-grid compact"><input type="hidden" name="csrf" value="<?= admin_escapar(admin_csrf_token()) ?>"><input type="hidden" name="acao" value="atualizar"><input type="hidden" name="id" value="<?= $id ?>"><label>Nome<input name="nome" value="<?= admin_escapar($admin['Nome']) ?>" required maxlength="255"></label><label>Usuário<input name="login" value="<?= admin_escapar($admin['Login']) ?>" required minlength="3" maxlength="64" pattern="[a-zA-Z0-9._-]+"></label><label>E-mail<input type="email" name="email" value="<?= admin_escapar($admin['Email']) ?>" required maxlength="255"></label><div class="form-actions"><button class="btn btn-secondary" type="submit">Salvar dados</button></div></form>
<div class="admin-inline-actions"><form method="post" class="password-reset-form"><input type="hidden" name="csrf" value="<?= admin_escapar(admin_csrf_token()) ?>"><input type="hidden" name="acao" value="senha"><input type="hidden" name="id" value="<?= $id ?>"><label>Nova senha<input type="password" name="senha" required minlength="10" placeholder="Mínimo 10 caracteres" autocomplete="new-password"></label><button class="btn btn-secondary" type="submit">Redefinir senha</button></form>
<form method="post" onsubmit="return confirm('Inativar esta conta administrativa?');"><input type="hidden" name="csrf" value="<?= admin_escapar(admin_csrf_token()) ?>"><input type="hidden" name="acao" value="inativar"><input type="hidden" name="id" value="<?= $id ?>"><button class="btn btn-danger" type="submit" <?= $id === (int)$_SESSION['admin_id'] ? 'disabled title="Não é possível inativar sua própria conta"' : '' ?>>Inativar</button></form></div></article>
<?php endforeach; ?></div><?php endif; ?>
<?php admin_layout_fim(); ?>
