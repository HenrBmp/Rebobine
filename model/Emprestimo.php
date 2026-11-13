<?php

namespace model;

use PDOException, DateTime, PDO;

class Emprestimo extends Tabela {
    /**
     * Indica a taxa cobrada por semana em caso de atraso.
     * Representação decimal da taxa percentual (número de 0 à 1).
     */
    public const float TAXA_DE_ATRASO_POR_SEMANA = 0.025;

    /**
     * Tenta criar um Emprestimo em nome do usuario logado.
     * @throws AuthError Caso a chave "IdUsuario" não seja encontrada em $_SESSION.
     */
    public function __construct(int $idItem, DateTime $dataVencimento) {
        $db = Database::usandoEnv();

        session_start();
        $idUsuario = $_SESSION["IdUsuario"] ?? throw new AuthError();

        $sql = "INSERT INTO Emprestimo(IdUsuario, IdItem, DataVencimento) VALUE (:usuario, :item, :vencimento)";
        try {
            $stmt = $db->queryComTransacao($sql, [
                "usuario" => $idUsuario,
                "item" => $idItem,
                "vencimento" => $dataVencimento->format("c")
            ]);

            $this->sucesso = $stmt->rowCount() === 1;
        } catch (PDOException $err) {
            $this->sucesso = FALSE;
        }
    }

    /**
     * @throws AuthError Caso a chave "IdUsuario" não seja encontrada em $_SESSION.
     * @return contract\EmprestimoContract|false Resultado segue o contrato para Emprestimo. False se não encontrar nada ou obter erro.
     */
    public static function comId(int $idEmprestimo): contract\EmprestimoContract|false {
        $db =  Database::usandoEnv();

        session_start();
        $idUsuario = $_SESSION["IdUsuario"] ?? throw new AuthError();

        $sql = "SELECT E.Id, E.DataEmprestimo, E.DataVencimento,
        U.Nome AS NomeUsuario, U.Email AS EmailUsuario,
        I.Nome AS NomeItem, I.Empresa AS EmpresaItem, I.Valor AS ValorItem
        FROM Emprestimo AS E
        INNER JOIN Usuario AS U ON E.IdUsuario = U.Id
        INNER JOIN Itens AS I ON E.IdItem = I.Id
        WHERE E.Ativo = TRUE AND E.Id = :idEmp AND U.Id = :idUser";
        try {
            $stmt = $db->query($sql, [
                "idEmp" => $idEmprestimo,
                "idUser" => $idUsuario
            ]);

            if ($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchObject(contract\EmprestimoContract::class);
            return $resultado;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

    /**
     * Retorna os emprestimos do usuario ordenados pelo vencimento.
     * @param int $offset Define o OFFSET da query.
     * @param Ordenacoes Indica se o retorno é crescente ou decrescente.
     * @throws AuthError Caso a chave "IdUsuario" não seja encontrada em $_SESSION.
     * @return contract\EmprestimoContract[]|false Resultado segue o contrato para Emprestimo. False se não encontrar nada ou obter erro.
     */
    public static function obterTodosComPaginacao(
        int $offset,
        Ordenacoes $ordem = Ordenacoes::DESC
    ): array|false {
        $db = Database::usandoEnv();

        session_start();
        $idUsuario = $_SESSION["IdUsuario"] ?? throw new AuthError();

        $sql = "SELECT E.Id, E.DataEmprestimo, E.DataVencimento,
        U.Nome AS NomeUsuario, U.Email AS EmailUsuario,
        I.Nome AS NomeItem, I.Empresa AS EmpresaItem, I.Valor AS ValorItem
        FROM Emprestimo AS E
        INNER JOIN Usuario AS U ON E.IdUsuario = U.Id
        INNER JOIN Itens AS I ON E.IdItem = I.Id
        WHERE E.Ativo = TRUE AND U.Id = :idUser
        ORDER BY E.DataVencimento $ordem->value LIMIT ". self::ITENS_POR_SELECT ." OFFSET :offset";
        try {
            $stmt = $db->query($sql, [
                "offset" => $offset,
                "idUser" => $idUsuario,
            ]);

            if ($stmt->columnCount() === 0) return FALSE;

            $resultado = $stmt->fetchAll(PDO::FETCH_CLASS, contract\EmprestimoContract::class);
            return $resultado;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

    /**
     * @throws AuthError Caso a chave "IdUsuario" não seja encontrada em $_SESSION.
     */
    public static function inativar(int $id): bool {
        $db = Database::usandoEnv();

        session_start();
        $idUsuario = $_SESSION["IdUsuario"] ?? throw new AuthError();

        $sql = "UPDATE Emprestimo SET Ativo = FALSE WHERE Id = :id AND IdUsuario = :usuario";
        try {
            $stmt = $db->queryComTransacao($sql, [ "id" => $id, "usuario" => $idUsuario]);

            return $stmt->rowCount() === 1;
        } catch (PDOException $err) {
            return FALSE;
        }
    }

    /**
     * Calcula o valor de pagamento do emprestimo com base nas semanas atrasadas, cada semana iniciada conta (Ex. 8 dias = 2 semanas).
     * O juros é composto e baseado na constante Emprestimo::TAXA_DE_ATRASO_POR_SEMANA.
     * @throws AuthError Caso a chave "IdUsuario" não seja encontrada em $_SESSION.
     */
    public static function obterValorDePagamento(int $id): int|float|false {
        $emprestimo = self::comId($id);

        if ($emprestimo === FALSE) return FALSE;

        $valorBase = $emprestimo->getValorItem();

        if (!$emprestimo->getAtrasado()) return $valorBase;

        $diferencaDeTempo = $emprestimo->getDataVencimento()->diff(new DateTime("now"), absolute: true);
        $semanas = ceil($diferencaDeTempo->days / 7);
        $valorComTaxa = $valorBase * pow((1 + self::TAXA_DE_ATRASO_POR_SEMANA), $semanas);
        $valorArredondado = \round($valorComTaxa, 2);

        return $valorArredondado;
    }

}
