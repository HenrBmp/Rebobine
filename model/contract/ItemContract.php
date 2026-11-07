<?php

namespace model\contract;

class ItemContract {
    private string $Nome, $Empresa, $Descricao;
    private float $Valor;
    private int $Id;

    public function getId(): int {  return $this->Id; }

    public function getNome(): string { return $this->Nome; }

    public function getEmpresa(): string { return $this->Empresa; }

    public function getValor(): float { return $this->Valor; }

    public function getDescricao(): string { return $this->Descricao; }
}
