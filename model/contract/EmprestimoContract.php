<?php

namespace model\contract;

use DateTime;

class EmprestimoContract {
    private int $Id;
    private string $NomeUsuario, $EmailUsuario, $NomeItem, $EmpresaItem, $DataEmprestimo, $DataVencimento;
    private float $ValorItem;

    public function getId():int { return $this->Id; }
    public function getDataEmprestimo(): DateTime { return new DateTime($this->DataEmprestimo); }
    public function getDataVencimento(): DateTime { return new DateTime($this->DataVencimento); }
    public function getNomeUsuario(): string { return $this->NomeUsuario; }
    public function getEmailUsuario(): string { return $this->EmailUsuario; }
    public function getNomeItem(): string { return $this->NomeItem; }
    public function getEmpresaItem(): string { return $this->EmpresaItem; }
    public function getValorItem(): float { return $this->ValorItem; }
    public function getAtrasado(): bool { return $this->getDataVencimento() < new DateTime("now");}
}
