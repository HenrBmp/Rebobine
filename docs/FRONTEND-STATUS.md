# Frontend — etapa 1

## Implementado
- Tokens CSS alinhados ao `docs/design-system.md`.
- Estilos globais, navegação, botões, cards e rodapé.
- Primeira tela `public/acervo.php`, conectada ao `ItemController::listarPorPagina()`.
- Busca e filtros client-side por nome/empresa/tipo.
- Fallback visual de capa enquanto os metadados externos não estiverem integrados.
- Área reservada aos créditos do TMDB no rodapé.
- `public/index.php` redireciona para o acervo.

## Pendente antes da integração completa
- Fazer backup e validar o banco de destino antes de aplicar `003_atualizacao_incremental.sql`.
- Implementar importação/consulta TMDB e RAWG no backend.
- Trocar os fallbacks pelas capas persistidas.
- Implementar a página de detalhes dos itens.

## Migration
- `database/migrations/003_atualizacao_incremental.sql` foi criada.
- Ela foi testada duas vezes em ambiente MySQL isolado.
- Sua aplicação no banco de destino ainda depende de backup e validação.

## Observação
As telas administrativas de login e dashboard já estão implementadas.
O link público de detalhes ainda não possui página implementada.
