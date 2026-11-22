# Autenticação e painel administrativo

## Acesso inicial

Após aplicar `database/migrations/003_atualizacao_incremental.sql`, execute na raiz do projeto:

```bash
php scripts/criar_admin_inicial.php
```

A conta inicial deve ser criada pelo script exclusivo de CLI
`scripts/criar_admin_inicial.php`. Não há usuário ou senha fixa no código.
Informe os dados por variáveis de ambiente no terminal seguro:

```bash
set REBOBINE_ADMIN_LOGIN=nome_do_admin
set REBOBINE_ADMIN_PASSWORD=senha_temporaria_segura
set REBOBINE_ADMIN_NAME=Nome do Administrador
set REBOBINE_ADMIN_EMAIL=admin@exemplo.local
php scripts/criar_admin_inicial.php
```

No primeiro acesso, o sistema exige a troca da senha. Não registre a senha
temporária em arquivos versionados, logs ou parâmetros visíveis no processo.

Acesse `public/admin/login.php` ou `public/login.php`.

## Papéis

A autorização usa `Usuario.IdTipo` relacionado a `Dominio`: `6` representa Cliente e `7` representa Administrador. A sessão administrativa guarda o ID e o tipo do usuário; cada página protegida verifica ambos no servidor.

## Gestão de administradores

No Dashboard → Administradores é possível:

- listar contas administrativas ativas;
- criar uma conta com nome, login, e-mail e senha inicial;
- editar nome, login e e-mail;
- redefinir senha, exigindo troca no próximo acesso;
- inativar uma conta sem apagar seu histórico.

O sistema impede inativar a própria conta pela interface e impede inativar o último administrador ativo.

## Segurança implementada

- Senhas armazenadas com `password_hash()` e verificadas com `password_verify()`.
- Troca obrigatória da senha temporária no primeiro login.
- Regeneração do ID da sessão após autenticação.
- Cookies de sessão `HttpOnly` e `SameSite=Lax` (Secure quando HTTPS estiver ativo).
- Token CSRF em formulários administrativos.
- Autorização de administrador no servidor, não apenas ocultação de links.
- Consultas parametrizadas e mensagens de erro sem detalhes internos.
- Contas inativadas preservadas no banco.

## Migration

A migration adiciona `Login`, `IdTipo` e `TrocarSenha` à tabela `Usuario`. Usuários existentes são mantidos como clientes (`IdTipo = 6`) e recebem logins temporários `usuario_ID`. Faça backup antes de executar.

## Observações

Este painel inicial cobre autenticação e gestão de administradores. A seção de gerenciamento do acervo com TMDB/OMDb/RAWG continua planejada para a próxima etapa. Antes de publicar o sistema na internet, configure HTTPS, remova a senha inicial, faça revisão de segurança e considere adicionar limitação de tentativas de login e recuperação de conta.
