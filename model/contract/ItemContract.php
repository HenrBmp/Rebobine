<?php

namespace model\contract;

class ItemContract {
    private string $Nome, $Empresa, $Descricao;
    private float $Valor;
    private int $Id, $IdTipo;

    public function getId(): int { return $this->Id; }

    public function getIdTipo(): int { return $this->IdTipo; }

    public function getNome(): string { return $this->Nome; }

    public function getEmpresa(): string { return $this->Empresa; }

    public function getValor(): float { return $this->Valor; }

    public function getDescricao(): string { return $this->Descricao; }
}
