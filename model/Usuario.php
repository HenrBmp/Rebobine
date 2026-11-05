<?php

namespace model;

use PDOException;

include "../autoloader.php";

class Usuario {
    public bool $sucesso;

    public function __construct(string $nome, string $senha, string $email) {
        $db = Database::usandoEnv();

        $sql = "INSERT INTO Usuario(Nome, Senha, Email) VALUES (:nome, :senha, :email)";
        $stmt = $db->queryComTransacao($sql, [
            "nome" => $nome,
            "senha" => $senha,
            "email" => $email
        ]);

        try {
            $this->sucesso = $stmt->rowCount() === 1;
        } catch (PDOException) {
            $this->sucesso = FALSE;
        }
    }

}