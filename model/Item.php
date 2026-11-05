<?php

namespace model;

use PDO, PDOException, Iterator;

class Item {
    private static const int ITEMS_POR_SELECT = 20;

    public bool $sucesso;

    public function __construct(string $nome, string $empresa, int $tipo) {
        $db = Database::usandoEnv();

        $sql = "INSERT INTO Item (Nome, Empresa, IdTipo) VALUE (:nome, :empresa, :tipo)";
        try {
            $stmt = $db->query("sql", [
                "nome" => $nome,
                "empresa" => $empresa,
                "tipo" => $tipo
            ]);

            $this->sucesso = $stmt->rowCount() === 1;
        } catch (PDOException $err) {
            $this->sucesso = FALSE;
        }
    }

    public static function obterTodosDaPagina(int $pagina, Ordenacoes $ordem): Iterator|false {
        $db = Database::usandoEnv();

        $sql = "SELECT I.Nome, I.Empresa, D.Descricao
        FROM Itens AS I INNER JOIN Dominio AS D
        ON I.IdTipo = D.Id WHERE Ativo = TRUE
        ORDER BY :ordem
        LIMIT " . Item::ITEMS_POR_SELECT;

        try {
            $stmt = $db->query($sql, [
                "ordem" => $ordem
            ]);
            return $stmt->getIterator();
        } catch (PDOException $err) {
            return false;
        }
    }
}
