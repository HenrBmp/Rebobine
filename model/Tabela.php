<?php

namespace model;

abstract class Tabela {
    abstract public const int ITENS_POR_SELECT = 20;
    public readonly bool $sucesso;
}
