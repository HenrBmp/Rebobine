<?php
require_once __DIR__ . '/../includes/admin_guard.php';
admin_exigir_login();
admin_layout_inicio('Visão geral');
?>
<div class="admin-welcome"><span class="eyebrow">BEM-VINDO DE VOLTA</span><h2><?= admin_escapar($_SESSION['admin_nome'] ?? 'Administrador') ?>.</h2><p>Este é o centro de controle da locadora. As ferramentas administrativas ficam reunidas aqui.</p></div>
<div class="admin-card-grid">
  <a class="admin-card" href="admins.php"><span class="admin-card-icon">♙</span><h2>Administradores</h2><p>Consultar contas, criar novos administradores, editar dados e redefinir senhas.</p><span class="admin-card-link">Gerenciar administradores →</span></a>
  <div class="admin-card is-pending"><span class="admin-card-icon">▣</span><h2>Gerenciar acervo</h2><p>Cadastro de filmes e jogos com TMDB, OMDb e RAWG.</p><span class="admin-card-link">Próxima etapa</span></div>
</div>
<?php admin_layout_fim(); ?>
