<?php

namespace model;

use PDO, PDOException;

include "../autoloader.php";

class Auth {
    private ?int $idEncontrado;

    public function verificarLogin(string $nomeOuEmail, string $senha) {
        $db = Database::usandoEnv();

        $sql = "SELECT COUNT(*) AS 'qnt', Id FROM Usuario
        WHERE (Nome = :nomeOuEmail OR Email = :nomeOuEmail) AND Senha = :senha AND Ativo = TRUE";
        $stmt = $db->query($sql, [
            "nomeOuEmail" => $nomeOuEmail,
            "senha" => $senha
        ]);

        try {
            $resultado = $stmt->fetch()[0];
            $usuariosEncontrados = $resultado["qnt"];
            $this->idEncontrado = $resultado["Id"];
        } catch (PDOException $e) {
            return FALSE;
        }

        return $usuariosEncontrados === 1;
    }
}