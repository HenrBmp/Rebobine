<?php

namespace model\contract;

use DateTime;

class PagamentoContract {
    private int $Id;
    private float $ValorPago, $ValorOriginal;
    private string $NomeUsuario, $EmailUsuario, $NomeItem, $DataEmprestimo, $DataVencimento, $DescricaoTipo;

    public function getId():int { return $this->Id; }
    public function getDataEmprestimo(): DateTime { return new DateTime($this->DataEmprestimo); }
    public function getDataVencimento(): DateTime { return new DateTime($this->DataVencimento); }
    public function getNomeUsuario(): string { return $this->NomeUsuario; }
    public function getEmailUsuario(): string { return $this->EmailUsuario; }
    public function getNomeItem(): string { return $this->NomeItem; }
    public function getValorPago(): float { return $this->ValorPago; }
    public function getValorOriginal(): float|null { return $this->ValorOriginal; }
    public function getValorTaxa(): float { return $this->ValorPago - $this->ValorOriginal; }
    public function getDescricaoTipo(): string { return $this->DescricaoTipo; }
}
