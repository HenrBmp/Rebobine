<?php
require_once __DIR__ . '/../autoloader.php';

use controller\ItemController;

$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$resposta = (new ItemController())->listarPorPagina($pagina);
$itens = ($resposta['sucesso'] ?? false) ? ($resposta['dados'] ?? []) : [];
function escapar(mixed $valor): string { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
function classeCategoria(int $idTipo): string {
    // Dominio.Id 1 = Filme; Dominio.Id 2 = Jogo (Tipo = 1).
    return $idTipo === 2 ? 'game' : 'film';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#12052B">
  <title>Acervo | REBOBINE</title>
  <link rel="stylesheet" href="assets/css/tokens.css"><link rel="stylesheet" href="assets/css/global.css">
  <link rel="stylesheet" href="assets/css/components.css"><link rel="stylesheet" href="assets/css/pages/acervo.css">
</head>
<body>
<header class="site-header"><div class="container header-inner">
  <a class="brand" href="index.php" aria-label="REBOBINE - início"><img src="assets/images/logo-rebobine.svg" alt="REBOBINE"></a>
  <nav class="main-nav" aria-label="Navegação principal">
    <a class="nav-link active" href="acervo.php" aria-current="page">Acervo</a><a class="nav-link" href="dashboard.php">Minha conta</a>
  </nav>
  <div class="header-actions"><a class="btn btn-secondary" href="login.php">Entrar</a></div>
</div></header>
<main>
  <section class="catalog-hero"><div class="container catalog-hero-inner">
    <div class="hero-copy"><div class="eyebrow">PLAY YOUR WAY · LOCADORA DIGITAL</div><h1 class="catalog-slogan"><button class="slogan-switch" id="slogan-switch" type="button" aria-label="Alterar a palavra do slogan">SEJA <span id="slogan-word" aria-live="polite">GENTIL</span>,<br>REBOBINE!</button></h1><p>Filmes para rever, jogos para descobrir e histórias que merecem mais uma rodada. Escolha seu próximo favorito.</p></div>
  </div></section>
  <section class="container catalog-area" aria-label="Catálogo de produtos">
    <div class="catalog-toolbar">
      <label class="search-box"><span aria-hidden="true">⌕</span><input id="catalog-search" type="search" placeholder="Buscar filmes, jogos..." autocomplete="off" aria-label="Buscar no acervo"></label>
      <div class="filter-row" role="group" aria-label="Filtrar por categoria">
        <button class="filter-chip" data-filter="todos" aria-pressed="true">Tudo</button><button class="filter-chip" data-filter="film" aria-pressed="false">Filmes</button><button class="filter-chip" data-filter="game" aria-pressed="false">Jogos</button>
      </div>
    </div>
    <div class="section-heading"><div><h2>NA PRATELEIRA</h2><p>Uma seleção para começar sua próxima sessão.</p></div><span class="catalog-count" id="catalog-count"><?= count($itens) ?> TÍTULOS</span></div>
    <div class="catalog-grid-wrap"><div class="catalog-grid" id="catalog-grid">
      <?php foreach ($itens as $item):
        $nome = $item['nome'] ?? '';
        $empresa = $item['empresa'] ?? '';
        $tipo = $item['descricaoTipo'] ?? '';
        $idTipo = (int)($item['idTipo'] ?? 0);
        $categoria = classeCategoria($idTipo);
        $id = (int)($item['id'] ?? 0);
      ?>
      <article class="media-card" data-kind="<?= escapar($categoria) ?>" data-search="<?= escapar(mb_strtolower($nome . ' ' . $empresa . ' ' . $tipo, 'UTF-8')) ?>">
        <a href="detalhes.php?id=<?= $id ?>" class="media-poster" aria-label="Ver detalhes de <?= escapar($nome) ?>">
          <span class="media-kind <?= $categoria === 'game' ? 'game' : '' ?>"><?= $categoria === 'game' ? 'JOGO' : 'FILME' ?></span>
          <div class="poster-fallback"><span class="poster-mark">REBOBINE® · <?= $categoria === 'game' ? 'GAME' : 'CINEMA' ?></span><strong><?= escapar($nome) ?></strong></div>
        </a>
        <div class="media-info"><h3><?= escapar($nome) ?></h3><div class="media-meta"><span><?= escapar($empresa) ?></span><span class="media-price">R$ <?= number_format((float)($item['valor'] ?? 0), 2, ',', '.') ?></span></div></div>
      </article>
      <?php endforeach; ?>
      <?php if (!$itens): ?><div class="empty-state catalog-empty"><div class="eyebrow">EJECT · PRATELEIRA VAZIA</div><h2>NENHUM TÍTULO POR AQUI.</h2><p>Assim que os itens forem cadastrados, eles aparecem nesta prateleira.</p></div><?php endif; ?>
    </div></div>
    <?php if (!($resposta['sucesso'] ?? false)): ?><p class="catalog-notice">Não foi possível carregar o catálogo agora. Confira a conexão com o banco de dados.</p><?php endif; ?>
  </section>
</main>
<footer class="site-footer"><div class="container footer-inner">
  <div><img class="footer-brand" src="assets/images/logo-rebobine-branca.svg" alt="REBOBINE"><p class="footer-copy">Seja sublime, Rebobine! · Uma locadora de outra realidade.</p></div>
  <div class="tmdb-credit"><img class="tmdb-logo-slot" src="https://www.themoviedb.org/assets/v4/logos/v2/blue_square_2-d537fb228cf3ded904ef09b136fe3fec72548ebc1fea3fbbd1ad9e36364db38b.svg" alt="TMDB"><span>This product uses the TMDB API but is not endorsed or certified by TMDB.</span></div>
</div></footer>
<script src="assets/js/acervo.js" defer></script>
</body></html>
