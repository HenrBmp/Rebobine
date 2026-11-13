<?php

namespace model;

use PDOException, PDO;

class Devolucao extends Tabela {
    /**
     * @param int $idEmprestimo
     * @throws AuthError Caso o emprestimo especificado nao encontre a chave 'IdUsuario' em $_SESSION
     */
    public function __construct(int $idEmprestimo) {
        $db = Database::usandoEnv();

        $emprestimo = Emprestimo::comId($idEmprestimo);
        if (!$emprestimo) {
            $this->sucesso = FALSE; return;
        }

        $sql = "INSERT INTO Devolucao(IdEmprestimo) VALUE (:id)";
        try {
            $stmt = $db->queryComTransacao($sql, ["id" => $idEmprestimo]);

            $this->sucesso = $stmt->rowCount() === 1;
        } catch (PDOException $err) {
            $this->sucesso = FALSE;
        }
    }

    /**
     * @param int $id Identificador da devolucao.
     * @throws AuthError Caso a chave "IdUsuario" não seja encontrada em $_SESSION.
     * @return contract\DevolucaoContract|false Resultado segue o contrato para Devolucao. False se não encontrar nada ou obter erro.
     */
    public static function comId(int $id): contract\DevolucaoContract|false {
        $db = Database::usandoEnv();

        session_start();
        $idUsuario = $_SESSION["IdUsuario"] ?? throw new AuthError();

        $sql = "SELECT D.Id, D.DataDevolucao,
        E.DataEmprestimo,
        U.Nome AS NomeUsuario, U.Email AS EmailUsuario,
        I.Nome AS NomeItem
        FROM Devolucao AS D
        INNER JOIN Emprestimo AS E ON D.IdEmprestimo = E.Id
        INNER JOIN Itens AS I ON E.IdItem = I.Id
        INNER JOIN Usuario AS U ON E.IdUsuario = U.Id
        WHERE D.Ativo = TRUE AND U.Id = :idUser AND D.Id = :idDevolucao";
        try {
            $stmt = $db->query($sql, [
                "idUser" => $idUsuario,
                "idDevolucao" => $id
            ]);

            if ($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchObject(contract\DevolucaoContract::class);
            return $resultado;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

    /**
     * @param int $offset Define o OFFSET da query.
     * @return contract\DevolucaoContract[]|false Resultado segue o contrato para Devolucao. False se não encontrar nada ou obter erro.
     * @throws AuthError Caso a chave "IdUsuario" não seja encontrada em $_SESSION.
     */
    public static function obterTodosComPaginacao(int $offset, Ordenacoes $ordem = Ordenacoes::DESC): array|false {
        $db = Database::usandoEnv();

        session_start();
        $idUsuario = $_SESSION["IdUsuario"] ?? throw new AuthError();

        $sql = "SELECT D.Id, D.DataDevolucao,
        E.DataEmprestimo,
        U.Nome AS NomeUsuario, U.Email AS EmailUsuario,
        I.Nome AS NomeItem
        FROM Devolucao AS D
        INNER JOIN Emprestimo AS E ON D.IdEmprestimo = E.Id
        INNER JOIN Itens AS I ON E.IdItem = I.Id
        INNER JOIN Usuario AS U ON E.IdUsuario = U.Id
        WHERE D.Ativo = TRUE AND U.Id = :idUser
        ORDER BY D.DataDevolucao $ordem->value LIMIT ". self::ITENS_POR_SELECT ." OFFSET :offset";
        try {
            $stmt = $db->query($sql, [
                "idUser" => $idUsuario,
                "offset" => $offset
            ]);

            if ($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchAll(PDO::FETCH_CLASS, contract\DevolucaoContract::class);
            return $resultado;
        } catch (PDOException $err) {
            return FALSE;
        }
    }
}
