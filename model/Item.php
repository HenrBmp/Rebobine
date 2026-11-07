<?php

namespace model;

use PDO, PDOException;

class Item {
    private const int ITENS_POR_SELECT = 20;

    public bool $sucesso;

    public function __construct(string $nome, string $empresa, int $tipo) {
        $db = Database::usandoEnv();

        $sql = "INSERT INTO Item (Nome, Empresa, IdTipo) VALUE (:nome, :empresa, :tipo)";
        try {
            $stmt = $db->query($sql, [
                "nome" => $nome,
                "empresa" => $empresa,
                "tipo" => $tipo
            ]);

            $this->sucesso = $stmt->rowCount() === 1;
        } catch (PDOException $err) {
            $this->sucesso = FALSE;
        }
    }

    /**
     * @return contract\ItemContract[]|false
     */
    public static function obterTodosDaPagina(int $pagina, Ordenacoes $ordem): array|false {
        $db = Database::usandoEnv();

        $sql = "SELECT I.Nome, I.Empresa, I.Valor, D.Descricao
            FROM Itens AS I INNER JOIN Dominio AS D
            ON I.IdTipo = D.Id WHERE Ativo = TRUE
            ORDER BY $ordem->value
            LIMIT ". Item::ITENS_POR_SELECT ." OFFSET :pagina";
        try {
            $stmt = $db->query($sql, [
                "pagina" => $pagina
            ]);

            if ($stmt->columnCount() === 0) return FALSE;

            $result = $stmt->fetchAll(PDO::FETCH_CLASS, contract\ItemContract::class);
            return $result;
        } catch (PDOException $err) {
            return false;
        }
    }

    public static function comId(int $id): contract\ItemContract|false {
        $db = Database::usandoEnv();

        $sql = "SELECT I.Id, I.Nome, I.Empresa, I.Valor, D.Descricao
        FROM Itens AS I INNER JOIN Dominio AS D
        ON I.IdTipo = D.Id WHERE I.Ativo = TRUE AND I.Id = :id";
        try {
            $stmt = $db->query($sql, [
                "id" => $id
            ]);

            if ($stmt->columnCount() === 0) return FALSE;

            $result = $stmt->fetchObject(contract\ItemContract::class);
            return $result;
        } catch (PDOException $err) {
            return FALSE;
        }
    }
}
