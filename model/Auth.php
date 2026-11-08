<?php

namespace model;

use PDO, PDOException;

include "../autoloader.php";

class Auth {
    /**
     * Variável populada automaticamente, se verificarLogin for bem sucedido, o Id do usuário logado será atribuido a ela. Do contrário, ela será nula.
     */
    public ?int $idEncontrado;

    public function verificarLogin(string $email, string $hashSenha): bool {
        $db = Database::usandoEnv();

        $sql = "SELECT COUNT(*) AS 'Qnt', Id FROM Usuario
        WHERE Email = :email AND Senha = :senha AND Ativo = TRUE";
        $stmt = $db->query($sql, [
            "email" => $email,
            "senha" => $hashSenha
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
