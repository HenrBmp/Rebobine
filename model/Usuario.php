<?php

namespace model;

use PDOException;

include "../autoloader.php";

class Usuario {
    public bool $sucesso;

    public function __construct(string $nome, string $senha, string $email) {
        $db = Database::usandoEnv();

        $sql = "INSERT INTO Usuario(Nome, Senha, Email) VALUES (:nome, :senha, :email)";

        try {
            $stmt = $db->queryComTransacao($sql, [
                "nome" => $nome,
                "senha" => $senha,
                "email" => $email
            ]);

            $this->sucesso = $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            $this->sucesso = FALSE;
        }
    }

    public function inativar(int $id): bool {
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
}
