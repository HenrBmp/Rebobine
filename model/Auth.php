<?php

namespace model;

use PDO, PDOException;

include "../autoloader.php";

class Auth {
    public function verificarLogin(string $nomeOuEmail, string $senha) {
        $db = Database::usandoEnv();

        $sql = "SELECT COUNT(*) AS 'qnt' FROM Usuario WHERE (Nome = :nomeOuEmail OR Email = :nomeOuEmail) AND Senha = :senha";
        $stmt = $db->query($sql, [
            "nomeOuEmail" => $nomeOuEmail,
            "senha" => $senha
        ]);

        try {
            $usuariosEncontrados = $stmt->fetch()[0]["qnt"];
        } catch (PDOException $e) {
            return FALSE;
        }

        return $usuariosEncontrados === 1;
    }
}