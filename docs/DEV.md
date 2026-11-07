# Guia de Desenvolvimento

## Sumário
1. [Instalação](#instalação)
2. [Nomenclatura](#nomenclatura)

## Instalação

Clone este repositório:
```bash
git clone https://github.com/HenrBmp/Rebobine.git
```

Para maior conveniência, certifique-se de que nas configurações de seu MySql conste a seguinte declaração na seção "[mysqld]":
```
lower_case_table_names=2
```

Utilize os seguintes comandos para gerar as tabelas do banco de dados:
```sql
CREATE DATABASE IF NOT EXISTS Rebobine CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE Rebobine;

DROP TABLE Usuario;
CREATE TABLE Usuario (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(255) NOT NULL,
    Senha VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    DataInscricao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    UNIQUE (Email)
);

DROP TABLE Dominio;
CREATE TABLE Dominio (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Descricao VARCHAR(255) NOT NULL,
    Tipo INT NOT NULL,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    UNIQUE (Descricao)
);

DROP TABLE Itens;
CREATE TABLE Itens (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(255) NOT NULL,
    Empresa VARCHAR(255) NOT NULL,
    Valor DOUBLE NOT NULL,
    IdTipo INT NOT NULL,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (IdTipo) REFERENCES Dominio(Id)
);

DROP TABLE Emprestimo;
CREATE TABLE Emprestimo (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdUsuario INT NOT NULL,
    IdItem INT NOT NULL,
    DataEmprestimo DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    DataVencimento DATETIME NOT NULL,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (IdUsuario) REFERENCES Usuario(Id),
    FOREIGN KEY (IdItem) REFERENCES Itens(Id)
);

DROP TABLE PagamentoAtraso;
CREATE TABLE PagamentoAtraso (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdTipoPagamento INT NOT NULL,
    IdEmprestimo INT NOT NULL,
    Valor DOUBLE NOT NULL,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (IdTipoPagamento) REFERENCES Dominio(Id),
    FOREIGN KEY (IdEmprestimo) REFERENCES Emprestimo(Id)
);

DROP TABLE PagamentoEmprestimo;
CREATE TABLE PagamentoEmprestimo (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    IdEmprestimo INT NOT NULL,
    IdTipoPagamento INT NOT NULL,
    Valor DOUBLE NOT NULL,
    Ativo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (IdEmprestimo) REFERENCES Emprestimo(Id),
    FOREIGN KEY (IdTipoPagamento) REFERENCES Dominio(Id)
);

DROP TABLE Devolucao;
CREATE TABLE Devolucao (
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
