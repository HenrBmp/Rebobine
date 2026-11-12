<?php

namespace model;

use PDO, PDOException;

include "../autoloader.php";

class Auth {
    /**
     * Variável populada automaticamente, se verificarLogin for bem sucedido, o Id do usuário logado será atribuido a ela. Do contrário, ela será nula.
     */
    public ?int $idEncontrado;

    /**
     * Verifica se as credenciais estão corretas.
     * @return bool True se existir usuario com essas credenciais.
     */
    public function verificarLogin(string $email, string $senha): bool {
        $db = Database::usandoEnv();

        $sql = "SELECT Id, Senha FROM Usuario WHERE Email = :email AND Ativo = TRUE";
        $stmt = $db->query($sql, [
            "email" => $email,
        ]);

        try {
            $resultado = $stmt->fetch();

            if (!password_verify($senha, $resultado["Senha"])) return FALSE;

            $this->idEncontrado = (int)$resultado["Id"];
        } catch (PDOException $e) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Busca pela propriedade '$idEncontrado' nesta instancia de Auth e, se possível, guarda em $_SESSION.
     * @return bool Indica se '$idEncontrado' foi achado nesta instancia.
     */
    public function guardarIdNaSessao(): bool {
        if ($this->idEncontrado) {
            session_start();
            $_SESSION["IdUsuario"] =  $this->idEncontrado;
            return session_commit();
        } else {
            return FALSE;
        }
    }
}
