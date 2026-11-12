<?php

namespace model;

use PDOException, DateTime, PDO;

class Emprestimo extends Tabela {
    public function __construct(int $idItem, DateTime $dataVencimento) {
        $db = Database::usandoEnv();

        session_start();
        $idUsuario = $_SESSION["IdUsuario"];
        session_abort();

        $sql = "INSERT INTO Emprestimo(IdUsuario, IdItem, DataVencimento) VALUE (:usuario, :item, :vencimento)";
        try {
            $stmt = $db->queryComTransacao($sql, [
                "usuario" => $idUsuario,
                "item" => $idItem,
                "vencimento" => $dataVencimento
            ]);

            $this->sucesso = $stmt->rowCount() === 1;
        } catch (PDOException $err) {
            $this->sucesso = FALSE;
        }
    }

    public static function comId(int $idEmprestimo): contract\EmprestimoContract|false {
        $db =  Database::usandoEnv();

        session_start();
        $idUsuario = $_SESSION["IdUsuario"];

        $sql = "SELECT E.Id, E.DataEmprestimo, E.DataVencimento,
        U.Nome AS NomeUsuario, U.Email AS EmailUsuario,
        I.Nome AS NomeItem, I.Empresa AS EmpresaItem, I.Valor AS ValorItem,
        FROM Emprestimo AS E
        INNER JOIN Usuario AS U ON E.IdUsuario = U.Id
        INNER JOIN Itens AS I ON E.IdItem = I.Id
        WHERE E.Ativo = TRUE AND E.Id = :idEmp AND U.Id = :idUser";
        try {
            $stmt = $db->query($sql, [ "idEmp" => $idEmprestimo, "idUser" => $idUsuario ]);

            if ($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchObject(contract\EmprestimoContract::class);
            return $resultado;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

    /**
     * @return contract\EmprestimoContract[]|bool
     */
    public static function obterTodosDaPagina(
        int $pagina,
        Ordenacoes $ordem = Ordenacoes::NOME
    ): array|false {
        $db = Database::usandoEnv();

        session_start();
        $idUsuario = $_SESSION["IdUsuario"];

        $sql = "SELECT E.Id, E.DataEmprestimo, E.DataVencimento,
        U.Nome AS NomeUsuario, U.Email AS EmailUsuario,
        I.Nome AS NomeItem, I.Empresa AS EmpresaItem, I.Valor AS ValorItem,
        FROM Emprestimo AS E
        INNER JOIN Usuario AS U ON E.IdUsuario = U.Id
        INNER JOIN Itens AS I ON E.IdItem = I.Id
        WHERE E.Ativo = TRUE AND U.Id = :idUser
        ORDER BY $ordem->value LIMIT ". self::ITENS_POR_SELECT ."OFFSET :pagina";
        try {
            $stmt = $db->query($sql, [
                "pagina" > $pagina,
                "idUsuario" => $idUsuario,
            ]);

            if ($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchAll(PDO::FETCH_CLASS, contract\EmprestimoContract::class);
            return $resultado;
        } catch (PDOException $err) {
            return FALSE;
        }
    }
}
