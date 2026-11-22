-- REBOBINE | Atualização incremental consolidada
-- Aplicar sobre Rebobine_v1 após backup completo.
-- Este arquivo não recria tabelas existentes, não apaga dados e não contém credenciais.
-- Os blocos a seguir verificam a existência de colunas e restrições antes de criá-las.

USE Rebobine_v1;

-- Dados iniciais necessários pelo catálogo e pela autenticação.
-- Reconcilia pelos IDs sem apagar registros fora deste conjunto.
INSERT INTO Dominio (Id, Descricao, Tipo, Ativo) VALUES
    (1, 'Filme', 1, 1),
    (2, 'Jogo', 1, 1),
    (3, 'Pix', 2, 1),
    (4, 'Dinheiro', 2, 1),
    (5, 'Cartão', 2, 1),
    (6, 'Cliente', 3, 1),
    (7, 'Administrador', 3, 1)
ON DUPLICATE KEY UPDATE
    Descricao = VALUES(Descricao),
    Tipo = VALUES(Tipo),
    Ativo = VALUES(Ativo);

-- Metadados externos aditivos do catálogo.
CREATE TABLE IF NOT EXISTS ItemMetadados (
    IdItem INT NOT NULL,
    FontePrincipal VARCHAR(10) NOT NULL,
    IdExterno VARCHAR(64) NOT NULL,
    IdOmdb VARCHAR(20) NULL,
    Sinopse TEXT NULL,
    CapaUrl VARCHAR(2048) NULL,
    FundoUrl VARCHAR(2048) NULL,
    DataLancamento DATE NULL,
    Nota DECIMAL(4,2) NULL,
    AtualizadoEm DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (IdItem),
    UNIQUE KEY UQ_ItemMetadados_Fonte_Externo (FontePrincipal, IdExterno),
    CONSTRAINT FK_ItemMetadados_Item
        FOREIGN KEY (IdItem) REFERENCES Itens(Id)
        ON DELETE CASCADE,
    CONSTRAINT CK_ItemMetadados_Fonte
        CHECK (FontePrincipal IN ('TMDB', 'RAWG')),
    CONSTRAINT CK_ItemMetadados_Nota
        CHECK (Nota IS NULL OR (Nota >= 0 AND Nota <= 10))
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Campos aditivos necessários pela autenticação administrativa.
DELIMITER $$

DROP PROCEDURE IF EXISTS rb_add_usuario_auth_columns$$
CREATE PROCEDURE rb_add_usuario_auth_columns()
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Usuario'
          AND COLUMN_NAME = 'Login'
    ) THEN
        ALTER TABLE Usuario ADD COLUMN Login VARCHAR(64) NULL AFTER Nome;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Usuario'
          AND COLUMN_NAME = 'IdTipo'
    ) THEN
        ALTER TABLE Usuario ADD COLUMN IdTipo INT NULL AFTER Email;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Usuario'
          AND COLUMN_NAME = 'TrocarSenha'
    ) THEN
        ALTER TABLE Usuario
            ADD COLUMN TrocarSenha TINYINT(1) NOT NULL DEFAULT 0 AFTER Ativo;
    END IF;
END$$

CALL rb_add_usuario_auth_columns()$$
DROP PROCEDURE rb_add_usuario_auth_columns$$

DELIMITER ;

-- Usuários antigos tornam-se clientes e recebem login temporário.
UPDATE Usuario
SET Login = CONCAT('usuario_', Id)
WHERE Login IS NULL
   OR TRIM(Login) = '';

UPDATE Usuario
SET IdTipo = 6
WHERE IdTipo IS NULL;

ALTER TABLE Usuario
    MODIFY Login VARCHAR(64) NOT NULL,
    MODIFY IdTipo INT NOT NULL DEFAULT 6;

-- Índices e relacionamento são adicionados apenas quando ainda não existirem.
DELIMITER $$

DROP PROCEDURE IF EXISTS rb_add_usuario_auth_constraints$$
CREATE PROCEDURE rb_add_usuario_auth_constraints()
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Usuario'
          AND INDEX_NAME = 'UQ_Usuario_Login'
    ) THEN
        ALTER TABLE Usuario
            ADD UNIQUE KEY UQ_Usuario_Login (Login);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Usuario'
          AND INDEX_NAME = 'IDX_Usuario_IdTipo_Ativo'
    ) THEN
        ALTER TABLE Usuario
            ADD KEY IDX_Usuario_IdTipo_Ativo (IdTipo, Ativo);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
          AND TABLE_NAME = 'Usuario'
          AND CONSTRAINT_NAME = 'FK_Usuario_Dominio_Tipo'
    ) THEN
        ALTER TABLE Usuario
            ADD CONSTRAINT FK_Usuario_Dominio_Tipo
            FOREIGN KEY (IdTipo) REFERENCES Dominio(Id);
    END IF;
END$$

CALL rb_add_usuario_auth_constraints()$$
DROP PROCEDURE rb_add_usuario_auth_constraints$$

DELIMITER ;
