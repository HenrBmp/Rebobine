# Cadastro de títulos pelo painel administrativo

## 1. Objetivo

Esta documentação descreve a proposta funcional e técnica para cadastrar filmes e jogos no REBOBINE usando TMDB, OMDb e RAWG. O cadastro deverá ser iniciado pelo painel administrativo, sem exigir que a equipe insira manualmente capas, sinopses e identificadores externos.

O fluxo foi pensado para PHP 8.1, MySQL e a estrutura atual do projeto. É uma especificação para orientar a implementação; a tela administrativa e os serviços HTTP das APIs ainda precisam ser desenvolvidos.

## 2. Decisão de interface: seção no dashboard

Sim. A busca e o cadastro de títulos devem ficar dentro do dashboard do administrador, em uma seção chamada **Acervo** ou **Gerenciar acervo**. É o local mais coerente porque a ação altera o catálogo comercial da locadora.

A seção deverá ter duas áreas principais:

1. **Adicionar título:** pesquisa um filme ou jogo em sua API de origem, permite conferir os resultados e abre um formulário de confirmação.
2. **Títulos cadastrados:** lista os itens já registrados no banco, com busca, filtro por Filme/Jogo e acesso à edição/inativação.

Não se deve expor essa ferramenta na área pública do catálogo. O acesso deve exigir sessão autenticada e autorização explícita de administrador no servidor. Esconder o link no HTML não é controle de acesso.

> Estado atual: existe autenticação administrativa, login, dashboard, proteção de
> rotas e gerenciamento de administradores em `public/admin/`. A alteração
> obrigatória da senha no primeiro acesso também está implementada. A seção de
> cadastro de filmes e jogos continua pendente.

## 3. Fluxo de cadastro

1. O administrador abre **Dashboard → Acervo → Adicionar título**.
2. Seleciona o tipo: **Filme** ou **Jogo**.
3. Digita o nome do título e inicia a pesquisa.
4. O backend consulta o provedor adequado: TMDB para filmes ou RAWG para jogos.
5. A interface mostra resultados resumidos para desambiguação: título, ano/data, imagem e identificador externo.
6. O administrador seleciona o resultado correto.
7. O backend busca os detalhes completos. Para filmes, consulta o OMDb como complemento quando houver identificador IMDb disponível e a chave estiver configurada.
8. O administrador revisa os dados externos e informa/confirma os dados comerciais do REBOBINE, especialmente **valor da locação** e **empresa**.
9. O backend valida os dados e grava `Itens` e `ItemMetadados` em uma única transação.
10. O título passa a aparecer no catálogo público usando os metadados locais.

Se a consulta externa falhar, o cadastro não deve criar um registro parcialmente associado. A interface deve informar o erro e permitir tentar novamente. Se o provedor estiver indisponível, não inventar metadados nem preencher silenciosamente com dados de outro título.

## 4. Responsabilidade de cada fonte

| Fonte | Uso | Identificador armazenado |
|---|---|---|
| TMDB | Dados principais de filmes | ID do filme TMDB em `IdExterno` |
| OMDb | Complemento de filmes | `imdbID` em `IdOmdb`, quando disponível |
| RAWG | Dados principais de jogos | ID do jogo RAWG em `IdExterno` |

OMDb não é a fonte principal do item e não substitui TMDB. A integração deve continuar funcionando caso o OMDb esteja indisponível ou não retorne um `imdbID`; nesse caso `IdOmdb` permanece `NULL` e o cadastro pode prosseguir com os dados do TMDB.

## 5. Mapeamento para o banco atual

### `Dominio`

Os identificadores confirmados para o catálogo são:

- `Id = 1`: Filme (`Tipo = 1`).
- `Id = 2`: Jogo (`Tipo = 1`).

O backend deve validar esses IDs e não confiar no valor enviado pelo navegador.

### `Itens`

Guarda os dados operacionais/comerciais do REBOBINE:

- `Nome`: título escolhido pelo administrador.
- `Empresa`: produtora/distribuidora do filme ou desenvolvedora/publicadora do jogo, quando a informação estiver disponível e fizer sentido para o campo atual.
- `Valor`: preço definido pelo administrador; nunca deve ser obtido da API externa.
- `IdTipo`: `1` para filme ou `2` para jogo.
- `Ativo`: controle interno de disponibilidade no catálogo.

### `ItemMetadados`

A tabela já existente foi conferida e possui os campos:

| Campo | Uso esperado |
|---|---|
| `IdItem` | Chave primária e referência a `Itens.Id` |
| `FontePrincipal` | `TMDB` ou `RAWG` |
| `IdExterno` | ID do título no provedor principal |
| `IdOmdb` | IMDb ID retornado pelo OMDb, opcional |
| `Sinopse` | Descrição do título |
| `CapaUrl` | URL da imagem de capa |
| `FundoUrl` | URL da imagem de fundo |
| `DataLancamento` | Data de lançamento |
| `Nota` | Avaliação numérica do provedor |
| `AtualizadoEm` | Atualização automática pelo banco |

A tabela tem chave primária em `IdItem` e índice único em `(FontePrincipal, IdExterno)`, impedindo que o mesmo identificador externo seja associado a mais de um item pela mesma fonte. O vínculo com `Itens` usa chave estrangeira com `ON DELETE CASCADE`.

A estrutura atual não possui campos dedicados para gênero, duração, plataformas, desenvolvedoras múltiplas ou elenco. Não se deve tentar armazenar esses dados em colunas inexistentes; uma expansão futura do esquema deve ser documentada e migrada separadamente.

## 6. Arquitetura PHP sugerida

Manter a integração fora das classes de locação e pagamento. Organização sugerida:

```text
service/
  ApiHttpClient.php
  TmdbService.php
  OmdbService.php
  RawgService.php
  CatalogoService.php
model/
  Item.php
  ItemMetadados.php
controller/
  ItemController.php
  CatalogoAdminController.php
public/
  admin/
    dashboard.php
    acervo.php
    buscar-titulos.php
    cadastrar-titulo.php
```

A organização final deve respeitar o autoloader e o padrão de namespaces já utilizado no repositório. Os endpoints em `public` devem apenas receber a requisição, validar sessão/permissão/CSRF, chamar o controller e devolver uma resposta adequada. A regra de negócio e o acesso ao banco permanecem fora da camada visual.

### Responsabilidades

- `TmdbService`: pesquisa e detalhes de filmes; transforma a resposta da API em um formato interno comum.
- `OmdbService`: recebe o IMDb ID quando disponível e complementa dados de filme.
- `RawgService`: pesquisa e detalhes de jogos.
- `CatalogoService`: coordena provedor, validação, deduplicação e gravação transacional de `Itens` + `ItemMetadados`.
- `CatalogoAdminController`: valida a entrada do painel e retorna resultados seguros para a interface.
- `ItemMetadados`: encapsula leitura e persistência da tabela de metadados.

Os nomes acima são uma proposta, não arquivos que já existam no projeto.

## 7. Segurança e credenciais

As chaves devem existir somente no `.env` local do servidor:

- `TMDB_API_KEY`
- `OMDB_API_KEY`
- `RAWG_API_KEY`

As URLs-base também ficam configuráveis no ambiente:

- `TMDB_BASE_URL`
- `TMDB_IMAGE_BASE_URL`
- `OMDB_BASE_URL`
- `RAWG_BASE_URL`

O `.env.example` deve conter os nomes e valores ilustrativos/vazios, nunca chaves reais. O `.env` deve permanecer ignorado pelo Git. Nunca enviar chaves ao JavaScript, HTML, mensagens de erro ou logs. Para OMDb, a chave é transmitida no parâmetro `apikey` da requisição HTTP, montada no backend; não deve ser gravada no código-fonte.

A seção administrativa exige:

- validação de sessão em cada endpoint;
- autorização de perfil administrador no servidor;
- token CSRF em operações de gravação;
- validação e limite de tamanho dos campos recebidos;
- consultas SQL preparadas;
- escape de texto ao renderizar resultados;
- timeout de HTTP e tratamento de limite de requisições;
- logs sem credenciais e sem dados sensíveis.

A autenticação atual do projeto deve ser revisada antes de liberar a seção: a existência de um domínio `Administrador` no banco, por si só, não prova que cada endpoint está protegido por autorização administrativa.

## 8. Persistência, duplicidade e atualização

- A gravação de `Itens` e `ItemMetadados` deve ocorrer na mesma transação. Se uma das operações falhar, ambas devem ser revertidas.
- Antes de cadastrar, verificar a combinação `FontePrincipal + IdExterno` para evitar duplicidade.
- A chave `IdItem` garante no máximo um registro de metadados por item.
- O preço e a situação ativa são sempre definidos pelo REBOBINE.
- Uma atualização de metadados não deve sobrescrever `Valor`, `Ativo` ou outras decisões comerciais.
- Não consultar APIs na renderização comum de `acervo.php`; a página pública lê o banco local.
- A atualização de metadados deve ser uma ação administrativa explícita ou tarefa controlada, respeitando limites dos provedores.

## 9. Tratamento de falhas

A interface deve distinguir pelo menos:

- nenhum resultado encontrado;
- provedor indisponível/timeout;
- chave ausente ou inválida;
- limite de requisições atingido;
- resposta inválida do provedor;
- título já cadastrado;
- erro de validação do formulário;
- falha de persistência no banco.

Mensagens ao usuário devem ser claras, mas não exibir URL com chave, stack trace, SQL ou detalhes internos. Os detalhes técnicos podem ser registrados no log do servidor com a chave removida.

## 10. Critérios de aceite

A seção estará pronta quando:

- somente administradores autenticados conseguirem acessá-la;
- for possível pesquisar filmes no TMDB e jogos no RAWG;
- os resultados puderem ser conferidos antes de cadastrar;
- OMDb complementar filmes quando houver IMDb ID, sem ser obrigatório;
- o administrador definir o valor comercial;
- a gravação de `Itens` e `ItemMetadados` for atômica;
- duplicidades forem bloqueadas com mensagem compreensível;
- o catálogo público carregar metadados do banco sem chamadas externas;
- falhas de API não gerarem itens incompletos;
- nenhuma chave aparecer no navegador ou no repositório.

## 11. Estado de implementação

Já existente e conferido:

- painel administrativo com login, dashboard e gerenciamento de administradores;
- proteção das rotas administrativas e alteração obrigatória da senha;
- `Itens` e `Dominio` no banco;
- tabela `ItemMetadados` com os dez campos documentados;
- IDs de domínio `1 = Filme` e `2 = Jogo`;
- variáveis de ambiente de banco no parser atual;
- `.env.example` com variáveis de configuração (sem segredos).

Ainda a implementar:

- carregamento das variáveis de API pelo ambiente;
- clientes HTTP e serviços TMDB/OMDb/RAWG;
- model/repositório de `ItemMetadados`;
- fluxo transacional de cadastro;
- interface de busca, pré-visualização, confirmação e gerenciamento;
- exibição de metadados reais no catálogo e página de detalhes.

## 12. Referências oficiais

- TMDB API: https://developer.themoviedb.org/docs
- OMDb API: https://www.omdbapi.com/
- RAWG API: https://rawg.io/apidocs
