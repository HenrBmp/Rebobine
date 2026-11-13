<?php

namespace model\contract;

use DateTime;

class UsuarioContract {
    private string $Nome, $Email, $DataInscricao;
    private int $Id;

    public function getId(): int { return $this->Id; }

    public function getNome(): string { return $this->Nome; }

    public function getEmail(): string { return $this->Email; }

    public function getDataInscricao(): DateTime { return new DateTime($this->DataInscricao); }
}
