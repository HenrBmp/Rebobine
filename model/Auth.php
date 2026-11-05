<?php

namespace model;

use PDO, PDOException;

include "../autoloader.php";

class Auth {
    private ?int $idEncontrado;

    public function verificarLogin(string $email, string $senha) {
        $db = Database::usandoEnv();

        $sql = "SELECT COUNT(*) AS 'Qnt', Id FROM Usuario
        WHERE Email = :email AND Senha = :senha AND Ativo = TRUE";
        $stmt = $db->query($sql, [
            "email" => $email,
            "senha" => $senha
        ]);

        try {
            $resultado = $stmt->fetch()[0];
            $usuariosEncontrados = $resultado["Qnt"];
            $this->idEncontrado = $resultado["Id"];
        } catch (PDOException $e) {
            return FALSE;
        }

        return $usuariosEncontrados === 1;
    }
}
