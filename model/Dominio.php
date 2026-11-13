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
     * Retorna todos os dominios cadastrados ordenados alfabeticamente.
     * @param Ordenacoes Indica se o retorno é crescente ou decrescente.
     * @return contract\DominioContract[]|false
     */
    public static function obterTodos(Ordenacoes $ordem = Ordenacoes::DESC): array|false {
        $db = Database::usandoEnv();

        $sql = "SELECT Id, Descricao, Tipo FROM Dominio WHERE Ativo = TRUE
            ORDER BY Descricao $ordem->value";
        try {
            $stmt = $db->query($sql);

            if ($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchAll(PDO::FETCH_CLASS, contract\DominioContract::class);
            return $resultado;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

    /**
     * Retorna todos os dominios de certo tipo ordenados alfabeticamente.
     * @param Ordenacoes Indica se o retorno é crescente ou decrescente.
     * @return contract\DominioContract[]|false
     */
    public static function comTipo(int $tipo, Ordenacoes $ordem = Ordenacoes::DESC): array|false {
        $db = Database::usandoEnv();

        $sql = "SELECT Id, Descricao, Tipo FROM Dominio WHERE Ativo = TRUE AND Tipo=:tipo
            ORDER BY Descricao $ordem->value";
        try {
            $stmt = $db->query($sql, ["tipo" => $tipo]);

            if ($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchAll(PDO::FETCH_CLASS, contract\DominioContract::class);
            return $resultado;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

    /**
     * @param int $id Identificador do domínio.
     * @return contract\DominioContract|false Resultado segue o contrato para Dominio. False se não encontrar nada ou obter erro.
     */
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
        } catch (PDOException $err) {
            return FALSE;
        }
    }
}
