<?php

namespace model;

use PDO, PDOException;

class Item extends Tabela {
    public function __construct(string $nome, string $empresa, float $valor, int $tipo) {
        $db = Database::usandoEnv();

        $sql = "INSERT INTO Itens (Nome, Empresa, Valor, IdTipo) VALUE (:nome, :empresa,:valor, :tipo)";
        try {
            $stmt = $db->query($sql, [
                "nome" => $nome,
                "empresa" => $empresa,
                "valor" => $valor,
                "tipo" => $tipo
            ]);

            $this->sucesso = $stmt->rowCount() === 1;
        } catch (PDOException $err) {
            $this->sucesso = FALSE;
        }
    }

    /**
     * @param int $offset Define o OFFSET da query.
     * @param Ordenacoes Indica se o retorno é crescente ou decrescente
     * @return contract\ItemContract[]|false Resultado segue o contrato para Item. False se não encontrar nada ou obter erro.
     */
    public static function obterTodosComPaginacao(int $offset, Ordenacoes $ordem = Ordenacoes::DESC): array|false {
        $db = Database::usandoEnv();

        $sql = "SELECT I.Id, I.Nome, I.Empresa, I.Valor, D.Descricao
            FROM Itens AS I INNER JOIN Dominio AS D
            ON I.IdTipo = D.Id WHERE I.Ativo = TRUE
            ORDER BY I.Nome $ordem->value
            LIMIT ". self::ITENS_POR_SELECT ." OFFSET :offset";
        try {
            $stmt = $db->query($sql, [
                "offset" => $offset
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
