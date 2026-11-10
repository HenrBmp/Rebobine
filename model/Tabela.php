<?php

namespace model;

abstract class Tabela {
    abstract public const int ITENS_POR_SELECT = 20;
    abstract public readonly bool $sucesso;
    abstract public static function obterTodosDaPagina(int $pagina, Ordenacoes $ordem = Ordenacoes::NOME): array|false;
    abstract public static function comId(int $id);
}
