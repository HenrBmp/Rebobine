<?php

namespace model;

use PDOException, PDO;

class Pagamento extends Tabela{
    /**
     * @throws AuthError Caso a chave "IdUsuario" não seja encontrada em $_SESSION.
     */
    public function __construct(int $idEmprestimo, int $idTipo, float $valor) {
        $db = Database::usandoEnv();

        $emprestimo = Emprestimo::comId($idEmprestimo);
        if (!$emprestimo) {
            $this->sucesso = FALSE; return;
        }
        $atrasado = $emprestimo->getAtrasado();

        $sql = "INSERT INTO Pagamento(IdEmprestimo, IdTipoPagamento, Atrasado, Valor) VALUES (:idEmp, :idTipo, :atrasado :valor)";
        try {
            $stmt = $db->queryComTransacao($sql, [
                "idEmp" => $idEmprestimo,
                "idTipo" => $idTipo,
                "atrasado" => (int)$atrasado,
                "valor" => $valor
            ]);

            $this->sucesso = $stmt->rowCount() === 1;
        } catch (PDOException $err) {
            $this->sucesso = FALSE;
        }
    }

    /**
     * @throws AuthError Caso a chave "IdUsuario" não seja encontrada em $_SESSION.
     * @return contract\PagamentoContract[]|bool Resultado segue o contrato para Pagamento. False se não encontrar nada ou obter erro.
     */
    public static function comId(int $id): contract\PagamentoContract|false {
        $db = Database::usandoEnv();

        session_start();
        $idUsuario = $_SESSION["IdUsuario"] ?? throw new AuthError();

        $sql = "SELECT P.Id, P.Valor AS ValorPago,
        E.DataEmprestimo, E.DataVencimento,
        U.Nome AS NomeUsuario, U.Email AS EmailUsuario,
        I.Nome AS NomeItem, I.Valor AS ValorOriginal,
        D.Descricao AS DescricaoTipo
        FROM Pagamento AS P
        INNER JOIN Emprestimo AS E ON E.Id = P.IdEmprestimo
        INNER JOIN Usuario AS U ON E.IdUsuario = U.Id
        INNER JOIN Itens AS I ON E.IdItem = I.Id
        INNER JOIN Dominio AS D ON D.Id = P.IdTipoPagamento
        WHERE Ativo=TRUE AND P.Id = :idPagamento AND U.Id = :idUser";

        try {
            $stmt = $db->query($sql, [ "idPagamento" => $id, "idUser" => $idUsuario ]);

            if ($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchObject(contract\PagamentoContract::class);
            return $resultado;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

    /**
     * @param int $pagina Define o OFFSET da query.
     * @param Ordenacoes Indica se o retorno é crescente ou decrescente.
     * @throws AuthError Caso a chave "IdUsuario" não seja encontrada em $_SESSION.
     * @return contract\PagamentoContract[]|bool Resultado segue o contrato para Pagamento. False se não encontrar nada ou obter erro.
     */
    public function obterTodosDaPagina(int $pagina, Ordenacoes $ordem = Ordenacoes::DESC): array|bool {
        $db = Database::usandoEnv();

        session_start();
        $idUsuario = $_SESSION["IdUsuario"] ?? throw new AuthError();

        $sql = "SELECT P.Id, P.Valor AS ValorPago,
        E.DataEmprestimo, E.DataVencimento,
        U.Nome AS NomeUsuario, U.Email AS EmailUsuario,
        I.Nome AS NomeItem, I.Valor AS ValorOriginal,
        D.Descricao AS DescricaoTipo
        FROM Pagamento AS P
        INNER JOIN Emprestimo AS E ON E.Id = P.IdEmprestimo
        INNER JOIN Usuario AS U ON E.IdUsuario = U.Id
        INNER JOIN Itens AS I ON E.IdItem = I.Id
        INNER JOIN Dominio AS D ON D.Id = P.IdTipoPagamento
        WHERE Ativo=TRUE AND U.Id = :idUser
        ORDER BY E.DataVencimento $ordem->value LIMIT ". self::ITENS_POR_SELECT ." OFFSET :pagina";
        try {
            $stmt = $db->query($sql, [
                "idUser" => $idUsuario,
                "pagina" => $pagina
            ]);

            if($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchAll(PDO::FETCH_CLASS, contract\PagamentoContract::class);
            return $resultado;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

}
