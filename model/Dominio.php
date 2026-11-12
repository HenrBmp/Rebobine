<?php

namespace model;

use PDO, PDOException;

class Dominio extends Tabela {
    public function __construct(string $descricao, int $tipo) {
        $db =  Database::usandoEnv();

        $sql = "INSERT INTO Dominio(Descricao, Tipo) VALUE (:desc, :tipo)";
        try {
            $stmt = $db->queryComTransacao($sql, [
                "desc" => $descricao,
                "tipo" => $tipo
            ]);

            $this->sucesso = $stmt->rowCount() === 1;
        } catch (PDOException $err) {
            $this->sucesso = FALSE;
        }
    }

    /**
     * @return contract\DominioContract[]|bool
     */
    public static function obterTodos(
        Ordenacoes $ordem = Ordenacoes::NOME
    ): array|false {
        $db = Database::usandoEnv();

        $sql = "SELECT Id, Descricao, Tipo FROM Dominio WHERE Ativo = TRUE
            ORDER BY $ordem->value";
        try {
            $stmt = $db->query($sql);

            if ($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchAll(PDO::FETCH_CLASS, contract\DominioContract::class);
            return $resultado;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

    public static function comId(int $id): contract\DominioContract|false {
        $db = Database::usandoEnv();

        $sql = "SELECT Id, Descricao, Tipo FROM Dominio
            WHERE Ativo = TRUE AND Id=:id";
        try {
            $stmt = $db->query($sql, [
                "id"=>$id
            ]);

            if ($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchObject(contract\DominioContract::class);
            return $resultado;
        }
    }
}
