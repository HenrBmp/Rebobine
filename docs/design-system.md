<p align="center">
  <img src="src/assets/logos/svg/Logo-Original.svg" alt="Logo da Rebobine" width="320">
</p>

---
version: "1.2"
name: "Rebobine — Retro Psychedelic Rental Design System"
description: "Contrato visual e técnico do sistema de design da Rebobine: estética web de 1997–2003 reinterpretada como desenho animado psicodélico, equilibrando expressividade na casca e clareza funcional nos fluxos de catálogo e CRUD."
---

# DESIGN.md

## Overview

This document defines the visual language, design tokens, components, and UI constraints for **Rebobine**. (For brand history, narrative, and academic scope, see `PROJETO.md`).

The central design principle is: **psychodelia belongs to the shell; clarity belongs to the core**. Landing pages, login, empty states, 404 pages, and footer areas are expressive and maximalist. Catalog browsing, search, forms, tables, rental, and CRUD workflows must remain calm, readable, and efficient.

### Key Characteristics

- **Acid Green (`#9EFF1F`)** is the primary brand accent and action color; **Ultraviolet (`#7A2BFF`)** establishes the world and large expressive surfaces.
- Use bold black outlines (`3px`–`4px`) and hard offset shadows (`5px 5px 0`) for sticker-like objects. **Never blur shadows.**
- Use black text (`#000000`) on Acid Green. White or cream text on Acid Green is strictly forbidden.
- **Rubik Mono One** is reserved for short display headlines and category labels; **Saira** handles interface and body text; **VT323** is reserved for OSD status displays, slogans in marquees, and timecodes.
- Film category is always **Tang Orange (`#FF7A1A`)**; game category is always **AV Cyan (`#1FE3FF`)**.
- VHS-style status labels use `PLAY`, `REC`, `PAUSE`, `EJECT`, and `ERR`, paired with an icon and an accessible plain-language label.
- Decorative objects may tilt or float in expressive areas, but never in tables, forms, or reading surfaces.
- Mascots (**Rewind** and **Byte**) change expressions and cycle the modular slogan (`"Seja ___, Rebobine!"`) based on UI state; assets must remain swappable without code changes.

## 1. Visual Theme & Atmosphere

A linguagem visual combina a web de 1997–2003 com a estética de desenho animado psicodélico impresso: campos de cor chapados, contornos pretos grossos, sombras duras sem desfoque e objetos de locadora (fita VHS, etiqueta, código de barras, prateleira, TV de tubo) tratados como personagens ou adesivos.

**Referências visuais:** Pablito Planeta (planetas, blobs, composição maximalista), Meow Wolf (portal, cores intensas, piso listrado preto e branco), Retro Web 1996–2001 (selos, estrelinhas, wordmarks com contorno).  
**Evitar:** Estética gamer de esports, loja virtual genérica, layout corporativo minimalista, neon/gradientes sem propósito, glassmorphism.

## 2. Color Palette & Roles

### Marca
| Nome | Hex | Papel |
|---|---|---|
| Verde Ácido | `#9EFF1F` | Assinatura. Ação principal, foco, destaques, barra de navegação, logo principal |
| Preto da Logo | `#000000` | Contornos, sombras duras, texto sobre verde, texto sobre cores claras |
| Etiqueta | `#F4EFE0` | Cards, papel, texto sobre fundo escuro, tema claro |

### Mundo e superfícies
| Nome | Hex | Papel |
|---|---|---|
| Ultravioleta | `#7A2BFF` | Cor do mundo: portal, campos grandes, capa, página de destaque |
| Noite | `#12052B` | Fundo escuro padrão das telas |
| Painel | `#1D0B45` | Superfície elevada sobre Noite (tabelas, painéis, inputs) |
| Painel 2 | `#2B1459` | Superfície alternada, cartões de mensagem |
| Lilás Bala | `#C9A7FF` | Decoração suave, blobs, estrelas secundárias |
| Lilás Texto | `#C3B5E3` | Texto secundário sobre Noite |

### Categorias e sinais
| Nome | Hex | Papel |
|---|---|---|
| Laranja Tang | `#FF7A1A` | Categoria **Filmes** |
| Ciano Canal AV | `#1FE3FF` | Categoria **Jogos**, foco de teclado |
| Amarelo Preço | `#FFE600` | Reservado (`PAUSE`), aviso, brilhos |
| Vermelho REC | `#FF3B1F` | Alugado (`REC`), atraso (`ERR`), perigo, excluir |
| Azul Tela | `#1E34FF` | Estado "sem sinal": 404, lista vazia, carregando |

### Tema claro (opcional, via `prefers-color-scheme: light`)
Fundo `#EFE6FF`, texto `#12052B`, superfície `#FFFFFF`, texto secundário `#4A3B73`, linhas `rgba(18,5,43,.2)`. Portal, cabeçalho e blocos Ultravioleta/Verde mantêm suas cores nos dois temas. Verde Ácido sobre fundo claro nunca é texto: use Preto sobre bloco Verde.

### Contraste (WCAG, calculado)
| Texto / Fundo | Razão | Uso |
|---|---|---|
| Preto / Verde Ácido | 16,8:1 | Botão primário, navegação |
| Etiqueta / Noite | 16,9:1 | Texto padrão no escuro |
| Verde Ácido / Noite | 15,5:1 | Destaques, status PLAY |
| Ciano / Noite | 12,5:1 | Foco, tag de jogos |
| Laranja / Noite | 7,4:1 | Tag de filmes |
| Lilás Texto / Noite | 10,2:1 | Texto secundário |
| Preto / Ciano | 13,5:1 | Capas de jogos |
| Preto / Amarelo | 16,6:1 | Aviso, selo |
| Preto / Laranja | 8,1:1 | Capas de filmes |
| Preto / Vermelho REC | 5,9:1 | Chip de atraso, botão de perigo |
| Etiqueta / Azul Tela | 6,1:1 | Sem sinal |
| Etiqueta / Ultravioleta | 5,1:1 | Texto no portal |
| Verde Ácido / Ultravioleta | 4,6:1 | Logo e títulos grandes (evite texto pequeno) |
| Etiqueta / Verde Ácido | **1,1:1** | **Proibido** |

### Proporção e Regras de Cor
- **Proporção (telas escuras):** 50% Noite, 22% Ultravioleta, 15% Etiqueta, 8% Verde Ácido, 5% demais acentos. Não pinte áreas grandes dentro do CRUD de verde.
- **Papéis fixos:** Laranja é sempre filmes; Ciano é sempre jogos.
- **Acessibilidade:** Status nunca dependem apenas de cor (sempre ícone + texto).
- **Sem gradientes:** Apenas cores chapadas com contorno preto.

## 3. Typography Rules

### Famílias
| Função | Fonte | Fallback | Origem |
|---|---|---|---|
| Display | Rubik Mono One | `"Arial Black", sans-serif` | Google Fonts |
| Interface e texto | Saira (400, 600, 800) | `"Segoe UI", system-ui, sans-serif` | Google Fonts |
| OSD (status, timecode) | VT323 | `"Courier New", monospace` | Google Fonts |

```html
<link href="https://fonts.googleapis.com/css2?family=Rubik+Mono+One&family=Saira:wght@400;600;800&family=VT323&display=swap" rel="stylesheet">
```

*(Nota: A arte da logo utiliza a fonte Gamestation apenas como vetor estático).*

### Hierarquia
| Papel | Fonte | Tamanho | Peso | Altura de linha | Observações |
|---|---|---|---|---|---|
| Hero / H1 | Rubik Mono One | `clamp(2rem, 6vw, 3.5rem)` | 400 | 1.1 | Caixa alta. Máx. 3 palavras |
| H2 de seção | Rubik Mono One | `clamp(1.5rem, 4.4vw, 2.4rem)` | 400 | 1.1 | Caixa alta |
| Título de card / selo | Rubik Mono One | `1.15rem` a `1.4rem` | 400 | 1.05 | Caixa alta |
| Tag e chip de categoria | Rubik Mono One | `.62rem` a `.8rem` | 400 | 1 | Caixa alta, fundo preto |
| Subtítulo | Saira | `1.1rem` a `1.25rem` | 800 | 1.2 | |
| Texto | Saira | `1.05rem` | 400 | 1.55 | Máx. 64 caracteres por linha |
| Texto pequeno | Saira | `.92rem` | 400 | 1.4 | Legendas, metadados |
| Botão e rótulo | Saira | `1rem` | 800 | 1 | |
| Status (chip) | VT323 | `1.3rem` | 400 | 1 | |
| Faixa / timecode | VT323 | `1.5rem` | 400 | 1 | |

### Regras Tipográficas
- **Rubik Mono One:** Apenas em títulos curtos e rótulos de categoria (`letter-spacing: 0`). Nunca em parágrafos, tabelas ou formulários.
- **VT323:** Apenas em status, timecode e faixas decorativas (`letter-spacing: .04em`). Nunca em texto longo.
- **Tratamento do Slogan (`"Seja ___, Rebobine!"`):** Sempre escrito com vírgula após o adjetivo variável e ponto de exclamação ao final. Em displays OSD (`VT323`) ou faixas (`marquee`), usa-se caixa alta (`SEJA SUBLIME, REBOBINE!`); em balões dos mascotes ou textos corridos (`Saira`), usa-se caixa de frase (`Seja sublime, Rebobine!`). Quando exibido como fórmula aberta, a lacuna pode ser estilizada como sublinhado (`___`) ou bloco destacado em Verde Ácido com texto preto.

## 4. Component Stylings

### Navegação
- Barra `sticky` no topo, fundo Verde Ácido, borda inferior `4px solid #000`, `z-index` alto.
- Símbolo da logo (fita com órbita) em preto (`70px` a `92px` de largura).
- Links em pílula: `Saira` 800 `.95rem`, padding `8px 14px`, borda `2px solid #000`, `border-radius: 999px`, fundo Etiqueta, sombra `3px 3px 0 #000`.
- Hover: fundo Amarelo Preço, `translate(2px, 2px)`. Item ativo: órbita (anel inclinado `-14°`) em volta da pílula. Máximo de 5 itens.

### Botões
| Variante | Fundo | Texto | Uso |
|---|---|---|---|
| Primário | Verde Ácido | Preto | Ação principal da tela (um por tela) |
| Secundário | Etiqueta | Preto | Ação alternativa (ver acervo, cancelar) |
| Laranja | Laranja Tang | Preto | Ação ligada a filmes ou devolução |
| Perigo | Vermelho REC | Preto | Excluir, cancelar locação |

- Borda `3px solid #000`, `border-radius: 14px`, padding `12px 22px`, `Saira` 800 `1rem`.
- Sombra dura `5px 5px 0` na cor que contrasta com o fundo (Preto sobre Ultravioleta/Etiqueta; Ultravioleta, Etiqueta ou Ciano sobre Noite).
- Hover: `translate(2px, 2px)` e sombra `3px 3px 0`. Active: `translate(4px, 4px)` e sombra `0`. Transição `.1s steps(2)`.
- Foco de teclado: `outline: 3px solid #1FE3FF; outline-offset: 3px`. Desabilitado: `opacity: .4`, sem sombra, `cursor: not-allowed`.

### Chip de Status (OSD)
Fundo preto, `border-radius: 4px`, padding `4px 10px 3px`, `VT323` `1.3rem`, ícone SVG de `10px` ao lado do texto.

| Estado | Texto | Cor | Ícone | Enum PHP |
|---|---|---|---|---|
| Disponível | `PLAY` | Verde Ácido | triângulo ▶ | `StatusMidia::Disponivel` |
| Alugado | `REC` | Vermelho REC | círculo piscando | `StatusMidia::Alugado` |
| Reservado | `PAUSE` | Amarelo Preço | duas barras | `StatusMidia::Reservado` |
| Manutenção | `EJECT` | Cinza `#A9A99F` | triângulo com base | `StatusMidia::Manutencao` |
| Atrasado | `ERR` | Preto sobre fundo Vermelho REC | triângulo piscando | `StatusMidia::Atrasado` |

Acompanhar com texto por extenso quando houver espaço (ex.: `"PLAY: disponível"`). Ícone pisca com `steps(2)` a `1s` apenas em `REC` e `ERR`.

### Card de Acervo e Prateleira
- **Card (`<button class="item">`):** Capa (`aspect-ratio: 3/4`, borda `4px solid #000`, `border-radius: 12px`, sombra `6px 6px 0 #000`) usando padrões CSS puros (**planeta**, **listras**, **colina**, **moldura**).
- **Tag de categoria:** Pílula preta no canto superior esquerdo (`Rubik Mono One` `.62rem`, texto Etiqueta: `"FILME"` ou `"JOGO"`). Em listas/tabelas, usar texto Laranja Tang (Filmes) ou Ciano Canal AV (Jogos).
- **Rotação em repouso:** `-2°`, `1.5°`, `-.6°`. Hover/foco: `rotate(0) translateY(-8px) scale(1.03)`.
- **Prateleira:** Grid `repeat(auto-fill, minmax(210px, 1fr))`, `gap: 30px 24px`. Base com piso listrado diagonal `135°` preto e Etiqueta (`16px` de faixa), altura `30px`, borda `4px solid #000`, `border-radius: 6px`.

### Filtros, Busca e Etiqueta de Aluguel (`<dialog>`)
- **Filtros:** Pílulas com borda `3px`, `aria-pressed`. Ativo: fundo Verde Ácido, texto preto, sombra `3px 3px 0`.
- **Etiqueta de aluguel (`<dialog>`):** Fundo Etiqueta, borda `4px solid #000`, `border-radius: 14px`, sombra `8px 8px 0 #9EFF1F`, rotação `-1.5°`. Cabeçalho em Vermelho REC. Contém título, metadados, chip OSD, código de barras CSS (`repeating-linear-gradient`), data de devolução e ações. `::backdrop` em `rgba(18,5,43,.8)`.

### Formulários e Tabelas (Zona Calma / CRUD)
- **Inputs/Selects:** Borda `2px`–`3px`, `border-radius: 8px`, fundo Painel (escuro) ou `#FFFFFF` (claro), `Saira` `1rem`, padding `11px 12px`. Erro: borda Vermelho REC e mensagem clara.
- **Tabela:** Fundo Painel, borda `2px`, `border-radius: 8px`, células `12px 14px`, divisor `1px`. Sem inclinação, sem sombra, sem animação. Rolagem horizontal no próprio contêiner (`min-width: 640px`).

### Sem Sinal (Estados Vazios, 404, Carregando)
Bloco Azul Tela (`#1E34FF`), borda `4px solid #000`, `border-radius: 12px`, texto `VT323` `1.7rem` em Etiqueta, centralizado.

### Portal (Hero), Faixa e Rodapé
- **Portal:** Fundo Ultravioleta com halftone (`radial-gradient` preto `22%` opacidade, passo `22px`), borda inferior `4px solid #000`. Logo em Verde Ácido com `drop-shadow(5px 5px 0 #000)`, slogan modular interativo (`Seja ___, Rebobine!`) e objetos flutuantes SVG com parallax (`--mx`, `--my`, `--d`) e flutuação (`7s ease-in-out infinite alternate`).
- **Faixa (`marquee`):** Fundo preto, `VT323` `1.5rem` Verde Ácido, borda inferior `4px` Verde, alternando variações do slogan (`"✦ FILMES ✦ JOGOS ✦ SEJA GENTIL, REBOBINE! ✦ SEJA SUBLIME, REBOBINE! ✦ SEJA SOLENE, REBOBINE!"`), rolagem linear `22s`.
- **Rodapé:** Bloco preto com `"▶ PLAY 00:00:01 ✦ SEJA GENTIL, REBOBINE!"` em `VT323` (adjetivo dinâmico por contexto) e faixa final em Verde Ácido com texto preto.

### Mascotes (Especificação Técnica)
> **Nota:** Os desenhos atuais são protótipos gerados por IA. A arte final será produzida pela equipe; os arquivos devem ser substituíveis sem alterar a lógica da aplicação.

- **Arquivos:** `assets/mascotes/{rewind|byte}-{expressao}.svg` (contorno preto sólido, fundo transparente, legível a `48px`). Fallback: `{personagem}-neutro.svg`.
- **Expressões:** `neutro`, `surpreso`, `brilho`, `dormindo`, `bravo`, e `apaixonado` (Rewind) / `confuso` (Byte).
- **Interação:** Piscada a cada `~5s` (`scaleY(.1)` por `150ms`). Clicar no mascote cicla variações da fórmula `"Seja ___, Rebobine!"` em um balão de fala (`aria-live="polite"`).

### Logo e Motivos Gráficos
- **Versões da Logo:** Principal (preto s/ Verde Ácido), Escura (Verde Ácido s/ Noite), Mundo (Verde Ácido s/ Ultravioleta), Papel (Noite s/ Etiqueta), além de variações com slogan modular, sem slogan, apenas símbolo (favicon) e apenas wordmark.
- **Área de respeito:** Altura da letra "R" em todos os lados. Tamanho mínimo: `120px` (logo) / `32px` (símbolo).
- **Motivos:** Brilho 4 pontas ✦ (máx. 3/tela), Órbita (`-14°`, máx. 1/componente), Fita inclinada (`±5°`), Divisão RGB (`±3px` Ciano/Laranja apenas em hover/transições), Halftone (apenas em Ultravioleta), Piso listrado, Selos (`rotate(8deg)`).

## 5. Layout Principles

- **Escala de espaçamento (base 4px):** `4, 8, 12, 16, 24, 32, 48, 64` px. Padding de seção: `60px` topo, `8px` base.
- **Contêiner:** `max-width: 1100px`, padding lateral `20px`, centralizado.
- **Zonas:**
  - *Maximalista (Portal, login, 404, vazio, rodapé):* Formas flutuantes, rotações, sobreposição decorativa, títulos centralizados.
  - *Funcional (Listas, cadastro, edição, devolução, tabelas):* Fundo sólido, alinhamento à esquerda, sem rotações ou efeitos.

## 6. Depth & Elevation

| Nível | Tratamento | Uso |
|---|---|---|
| 0 — Plano | Sem borda, cor chapada | Fundo da página, campos de cor do portal |
| 1 — Painel | Borda `2px` de linha, sem sombra | Tabelas, painéis, formulários |
| 2 — Card | Borda `3px`–`4px` preta, sombra `5px`–`6px 0` | Capas, botões, pílulas de navegação |
| 3 — Modal | Borda `4px` preta, sombra `8px 8px 0` Verde Ácido | Etiqueta de aluguel (`<dialog>`) |

- **Raios:** `4px` (chips), `6px`–`8px` (tabelas/inputs), `10px`–`14px` (cards/botões), `999px` (pílulas).

## 7. Do's and Don'ts

### Faça
- Use Verde Ácido apenas para a ação principal (um primário por tela) e assinatura.
- Use texto preto sobre Verde Ácido sempre.
- Mantenha Filmes em Laranja Tang e Jogos em Ciano Canal AV.
- Respeite a pontuação da fórmula `"Seja ___, Rebobine!"` e adeque o adjetivo ao contexto.
- Mantenha o miolo do CRUD limpo, sem inclinações ou efeitos.
- Respeite `prefers-reduced-motion` desligando flutuação, marquee, piscada e parallax.

### Evite
- Verde Ácido em grandes superfícies dentro do CRUD.
- Branco ou Etiqueta sobre Verde Ácido (`1,1:1`).
- `blur`, `glow`, `glassmorphism` ou gradientes decorativos.
- Efeito RGB, rotação e flutuação em tabelas, formulários ou textos longos.
- Emojis no lugar de ícones SVG.
- `Rubik Mono One` em parágrafos, tabelas ou inputs.

### Easter Eggs Permitidos
1. Clicar no mascote ou na lacuna do slogan cicla variações de `"Seja ___, Rebobine!"`.
2. Código `↑ ↑ ↓ ↓` ativa "fita desalinhada" (divisão RGB e tremor) por `3,5s`.
3. Confirmar devolução exibe tela verde `"◀◀ REBOBINANDO…"` por `~0,9s`.

## 8. Responsive Behavior

- **Breakpoints:** Mobile (`375–479px`, 2 colunas de capa, portal 1 coluna), Mobile grande (`480–759px`), Tablet (`760–1023px`, portal 2 colunas), Desktop (`1024–1439px`), Wide (`≥ 1440px`).
- **Toque e Acessibilidade:** Alvo mínimo `44×44px`, foco visível (`3px solid #1FE3FF`), `aria-hidden="true"` na decoração, `aria-pressed` nos filtros, `aria-live="polite"` nas trocas de slogan e feedback de rebobinar.
- **Movimento:** Transições rápidas em degraus (`.1s`–`.15s steps(2)`). Desativar animações sob `prefers-reduced-motion: reduce`.

## 9. Agent Prompt Guide

### Tokens CSS (`style.css`)
```css
:root {
  --noite:#12052B; --painel:#1D0B45; --painel2:#2B1459;
  --uv:#7A2BFF; --acido:#9EFF1F; --etiqueta:#F4EFE0; --preto:#000;
  --lilas:#C9A7FF; --lilas-txt:#C3B5E3;
  --laranja:#FF7A1A; --ciano:#1FE3FF; --sol:#FFE600; --rec:#FF3B1F; --azul-tela:#1E34FF;

  --bg:var(--noite); --fg:var(--etiqueta); --sf:var(--painel); --mute:var(--lilas-txt); --ln:#3a3a3a;
  --disp:"Rubik Mono One","Arial Black",sans-serif;
  --body:"Saira","Segoe UI",system-ui,sans-serif;
  --osd:"VT323","Courier New",monospace;

  --borda:3px solid var(--preto); --borda-g:4px solid var(--preto);
  --sombra:5px 5px 0 var(--preto); --sombra-g:8px 8px 0 var(--acido);
  --r-chip:4px; --r-input:8px; --r-card:12px; --r-btn:14px; --r-pill:999px;
  --s1:4px; --s2:8px; --s3:12px; --s4:16px; --s5:24px; --s6:32px; --s7:48px; --s8:64px;
  --foco:3px solid var(--ciano);
}
@media (prefers-color-scheme: light) {
  :root:not([data-theme="dark"]) { --bg:#EFE6FF; --fg:var(--noite); --sf:#fff; --mute:#4A3B73; --ln:rgba(18,5,43,.2); }
}
@media (prefers-reduced-motion: reduce) { *,*::before,*::after { animation:none!important; transition:none!important; } }
```

### Checklist de Implementação
1. Identificar a zona da tela: maximalista (vitrine/portal) ou funcional (CRUD).
2. Definir superfície: Ultravioleta (portal), Noite (padrão) ou Etiqueta (tema claro).
3. Aplicar contorno preto e sombra dura nos elementos de destaque; manter tabelas e inputs planos.
4. Reservar Verde Ácido para a ação primária e assinatura.
5. Sinalizar categoria (Laranja/Ciano) e status (chip OSD com ícone + texto).
6. Ajustar expressão do mascote e variação do slogan (`Seja ___, Rebobine!`) ao estado da tela.
7. Validar contraste WCAG, navegação por teclado, largura `375px` e `prefers-reduced-motion`.

### Estrutura de Arquivos Sugerida
```
/assets/logo/       logo-principal.svg  logo-escura.svg  simbolo.svg  favicon.svg
/assets/mascotes/   rewind-neutro.svg ... byte-confuso.svg   (12 arquivos)
/assets/fonts/      (opcional) Rubik Mono One, Saira, VT323 em woff2
/css/               style.css  (tokens da seção 9)
PROJETO.md          conceito, narrativa e escopo
DESIGN.md           contrato visual e design system
```
