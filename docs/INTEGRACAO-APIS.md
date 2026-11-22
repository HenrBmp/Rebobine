# Integração de APIs — REBOBINE

## Objetivo

O REBOBINE utiliza provedores externos para enriquecer os dados informativos de filmes e jogos. As informações serão consultadas durante o cadastro/atualização administrativa e persistidas localmente. O catálogo público deve consultar o MySQL, não as APIs a cada carregamento.

## Provedores e responsabilidades

| Provedor | Tipo de conteúdo | Papel |
|---|---|---|
| TMDB | Filmes | Fonte principal |
| OMDb | Filmes | Fonte complementar, opcional |
| RAWG | Jogos | Fonte principal |

OMDb deve ser consultado quando houver um IMDb ID disponível. Sua indisponibilidade não deve impedir o cadastro do filme vindo do TMDB.

## Domínios confirmados

A tabela `Dominio` usa `Tipo = 1` para categorias de catálogo, `Tipo = 2` para formas de pagamento e `Tipo = 3` para perfis. Os registros confirmados são:

| Id | Descrição | Tipo | Uso |
|---:|---|---:|---|
| 1 | Filme | 1 | Item de catálogo |
| 2 | Jogo | 1 | Item de catálogo |
| 3 | Pix | 2 | Forma de pagamento |
| 4 | Dinheiro | 2 | Forma de pagamento |
| 5 | Cartão | 2 | Forma de pagamento |
| 6 | Cliente | 3 | Perfil de usuário |
| 7 | Administrador | 3 | Perfil de usuário |

O acervo deve classificar os itens por `Itens.IdTipo`, usando os IDs confirmados, e não por comparação textual de `Dominio.Descricao`.

## Separação de responsabilidades

- `Itens`: dados internos/comerciais, como nome, empresa, valor, categoria e estado ativo.
- `ItemMetadados`: dados externos, como sinopse, imagens, lançamento, avaliação e identificadores dos provedores.
- TMDB: fonte principal de filmes.
- OMDb: complemento opcional para filmes; `imdbID` pode ser armazenado em `IdOmdb`.
- RAWG: fonte principal de jogos.

## Variáveis de ambiente

As variáveis são documentadas em `.env.example` e devem ser preenchidas no `.env` local:

```env
TMDB_API_KEY=
TMDB_BASE_URL=https://api.themoviedb.org/3
TMDB_IMAGE_BASE_URL=https://image.tmdb.org/t/p

OMDB_API_KEY=
OMDB_BASE_URL=https://www.omdbapi.com/

RAWG_API_KEY=
RAWG_BASE_URL=https://api.rawg.io/api
```

A chave do OMDb é enviada pelo backend no parâmetro `apikey` da URL HTTP. Ela deve continuar armazenada separadamente em `OMDB_API_KEY`, nunca codificada diretamente no fonte.

## Persistência

A tabela `ItemMetadados` já está criada no banco usado no desenvolvimento. Sua estrutura deve ser consultada antes de qualquer nova migration. Ela relaciona cada registro a `Itens` por `IdItem`, guarda `FontePrincipal` (`TMDB` ou `RAWG`) e o identificador externo em `IdExterno`. `IdOmdb` é opcional.

Para atualizar um banco existente, aplique apenas
`database/migrations/003_atualizacao_incremental.sql`. Não execute novamente
migrations antigas já representadas por esse arquivo consolidado.

## Fluxo administrativo

O cadastro de títulos deve ser uma seção do dashboard do administrador. O fluxo detalhado, o mapeamento dos campos, a arquitetura PHP, os critérios de segurança e os critérios de aceite estão em [Cadastro de títulos pelo painel administrativo](./FLUXO-CADASTRO-ACERVO.md).

## Regras importantes

- Chaves ficam apenas no servidor, no `.env`.
- `.env.example` contém nomes e valores ilustrativos/vazios, nunca credenciais reais.
- Não enviar chaves ao navegador.
- Não consultar APIs a cada abertura do catálogo público.
- Não sobrescrever preço ou estado ativo com dados externos.
- Usar transação para gravar `Itens` e `ItemMetadados` juntos.
- Verificar duplicidade por `(FontePrincipal, IdExterno)` antes de inserir.
- Tratar timeout, indisponibilidade, chave inválida e limites de requisição.
- O código deve permanecer compatível com PHP 8.1.

## Referências oficiais

- TMDB: https://developer.themoviedb.org/docs
- OMDb: https://www.omdbapi.com/
- RAWG: https://rawg.io/apidocs
