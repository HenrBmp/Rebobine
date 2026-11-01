# Guia de Desenvolvimento

## Sumário
1. [Instalação](#instalação)
2. [Nomenclatura](#nomenclatura)

## Instalação

Clone este repositório:
```bash
git clone https://github.com/HenrBmp/Rebobine.git
```

Para maior conveniência, certifique-se de que nas configurações de seu MySql conste a seguinte declaração:
```
lower_case_table_names=2
```

Utilize os seguintes comandos para gerar as tabelas do banco de dados:
```sql
CREATE DATABASE IF NOT EXISTS Rebobine CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE Rebobine;

CREATE TABLE IF NOT EXISTS Usuario (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(255) NOT NULL,
    Senha VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    DataInscricao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS Dominio (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Descricao VARCHAR(255) NOT NULL,
    Tipo INT NOT NULL,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS Itens (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(255) NOT NULL,
    Empresa VARCHAR(255) NOT NULL,
    IdTipo INT NOT NULL,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (IdTipo) REFERENCES Dominio(Id)
);

CREATE TABLE IF NOT EXISTS Emprestimo (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdUsuario INT NOT NULL,
    IdItem INT NOT NULL,
    DataEmprestimo DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    DataVencimento DATETIME NOT NULL,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (IdUsuario) REFERENCES Usuario(Id),
    FOREIGN KEY (IdItem) REFERENCES Itens(Id)
);

CREATE TABLE IF NOT EXISTS AtrasoEmprestimo (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdEmprestimo INT NOT NULL,
    Valor DOUBLE NOT NULL,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (IdEmprestimo) REFERENCES Emprestimo(Id)
);

CREATE TABLE IF NOT EXISTS PagamentoAtraso (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdAtraso INT NOT NULL,
    IdTipoPagamento INT NOT NULL,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (IdAtraso) REFERENCES AtrasoEmprestimo(Id),
    FOREIGN KEY (IdTipoPagamento) REFERENCES Dominio(Id)
);

CREATE TABLE IF NOT EXISTS PagamentoEmprestimo (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdEmprestimo INT NOT NULL,
    IdTipoPagamento INT NOT NULL,
    Valor DOUBLE NOT NULL,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (IdEmprestimo) REFERENCES Emprestimo(Id),
    FOREIGN KEY (IdTipoPagamento) REFERENCES Dominio(Id)
);

CREATE TABLE IF NOT EXISTS Devolucao (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdEmprestimo INT NOT NULL,
    DataDevolucao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (IdEmprestimo) REFERENCES Emprestimo(Id)
);
```

*Ainda em escrita...*

## Nomenclatura

Busque facilitar o trabalho coletivo usando nomes descritivos. Abreviaturas são permitidas, desde que obvias. Ex. i para index, conn para connection.

Tenha preferência por nomenclaturas em português, salve exceções de termos comuns como "handler", "path", "query" e etc.

Utilize as seguintes convenções de nomenclatura:

| Classes | Variáveis/Propriedades | Funções/Métodos |
| -  | - | - |
| CamelCase | pascalCase | pascalCase |

Caso um arquivo consista apenas na definição de uma classe, seu nome deve ser igual ao dela.
