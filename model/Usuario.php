<?php

namespace model;

use PDOException, PDO;

include "../autoloader.php";

class Usuario extends Tabela {
    public const int ITENS_POR_SELECT = 50;

    public function __construct(string $nome, string $senhaHash, string $email) {
        $db = Database::usandoEnv();

        $sql = "INSERT INTO Usuario(Nome, Senha, Email) VALUES (:nome, :senha, :email)";

        try {
            $stmt = $db->queryComTransacao($sql, [
                "nome" => $nome,
                "senha" => $senhaHash,
                "email" => $email
            ]);

            $this->sucesso = $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            $this->sucesso = FALSE;
        }
    }

    public static function inativar(int $id): bool {
        $db = Database::usandoEnv();

        $sql = "UPDATE Usuario SET Ativo = FALSE WHERE Id = :id";
        try {
            $stmt = $db->queryComTransacao($sql, [
                "id" => $id
            ]);
            return $stmt->rowCount() === 1;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

    /**
     * @return contract\UsuarioContract[]|false
     */
    public static function obterTodosDaPagina(int $pagina, Ordenacoes $ordem = Ordenacoes::NOME): array|false {
        $db = Database::usandoEnv();

        $sql ="SELECT Id, Nome, Email, DataInscricao
            FROM Usuario WHERE Ativo=TRUE
            ORDER BY $ordem->value
            LIMIT ". self::ITENS_POR_SELECT ." OFFSET :pagina";
        try {
            $stmt = $db->query($sql, [
                "pagina" => $pagina
            ]);

            if ($stmt->columnCount() === 0) return FALSE;

            $result = $stmt->fetchAll(PDO::FETCH_CLASS, contract\UsuarioContract::class);
            return $result;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

    public static function comId(int $id): contract\UsuarioContract|false {
        $db = Database::usandoEnv();

        $sql = "SELECT Id, Nome, Email, DataInscricao FROM Usuario WHERE Ativo=TRUE AND Id=:id";
        try {
            $stmt = $db->query($sql, [
                "id" => $id
            ]);

            if ($stmt->rowCount() === 0) return FALSE;

            $result = $stmt->fetchObject(contract\UsuarioContract::class);
            return $result;
        } catch (PDOException $err) {
            return FALSE;
        }
    }
}

