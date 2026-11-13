<?php

namespace model\contract;

use DateTime;

class DevolucaoContract {
    private int $Id;
    private string $NomeUsuario, $EmailUsuario, $NomeItem, $DataEmprestimo, $DataDevolucao;

    public function getId():int { return $this->Id; }
    public function getDataEmprestimo(): DateTime { return new DateTime($this->DataEmprestimo); }
    public function getNomeUsuario(): string { return $this->NomeUsuario; }
    public function getEmailUsuario(): string { return $this->EmailUsuario; }
    public function getNomeItem(): string { return $this->NomeItem; }
    public function getDataDevolucao(): DateTime { return new DateTime($this->DataDevolucao); }
}
