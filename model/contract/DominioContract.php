<?php

namespace model\contract;

class DominioContract {
    private string $Descricao;
    private int $Tipo, $Id;

    public function getId(): int { return $this->Id; }
    public function getDescricao(): string { return $this->Descricao; }
    public function getTipo():int  { return $this->Tipo; }
}
